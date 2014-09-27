<?php
$whereat = 'bank';
$wiki = 'Trading_House';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
require_once 'commons/userlib.php';
require_once 'commons/publictradinglib.php';
require_once 'commons/utility.php';
require_once 'commons/economylib.php';

if($NO_PVP)
{
  header('Location: /lostdata.php');
  exit();
}

if($user['license'] != 'yes')
{
  header('Location: /ltc.php?dialog=2');
  exit();
}

$command = 'SELECT a.tradeid,a.idnum,a.itemtext,b.sdesc,b.itemtext AS trade_for,b.timestamp,b.userid AS trader FROM psypets_trading_house_bids AS a LEFT JOIN psypets_trading_house_requests AS b ON a.tradeid=b.idnum WHERE a.userid=' . $user['idnum'];
$my_bids = $database->FetchMultiple($command, 'fetching my bids');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Trading House &gt; My Open Bids</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Trading House</h4>
     <ul class="tabbed">
      <li class="activetab"><a href="trading_public2.php">Public Trade Offers</a></li>
      <li><a href="trading_public.php">Old Public Trades</a></li>
      <li><a href="trading.php">Private Trading</a></li>
     </ul>
     <ul>
      <li><a href="trading_public2.php">View all public trades</a></li>
     </ul>
<?php
if(count($my_bids) > 0)
{
?>
  <table>
   <tr class="titlerow">
    <th></th>
    <th>Asking</th>
    <th>Offering</th>
    <th>Resident</th>
    <th>Posted</th>
    <th>Your Bid</th>
   </tr>
<?php
  $rowclass = begin_row_class();

  foreach($my_bids as $bid)
  {
    if(strlen($bid['trader']) == 0)
    {
?>
   <tr class="<?= $rowclass ?>">
    <td class="dim">View</td>
    <td>Expired Bid</td>
    <td>&mdash;</td>
    <td>&mdash;</td>
    <td>&mdash;</td>
    <td><ul class="plainlist nomargin"><?= $bid['itemtext'] ?></ul></td>
   </tr>
<?php
    }
    else
    {
      $poster = get_user_byid($bid['trader'], 'display');
?>
   <tr class="<?= $rowclass ?>">
    <td><a href="trading_public_view.php?id=<?= $bid['tradeid'] ?>">View</a></td>
    <td><?= $bid['sdesc'] ?></td>
    <td><ul class="plainlist nomargin"><?= $bid['trade_for'] ?></ul></td>
    <td><?= resident_link($poster['display']) ?></td>
    <td><?= duration($now - $bid['timestamp'], 2) ?> ago</td>
    <td><ul class="plainlist nomargin"><?= $bid['itemtext'] ?></ul></td>
   </tr>
<?php
    }
    $rowclass = alt_row_class($rowclass);
  }
?>
  </table>
<?php
}
else
  echo '<p>You are not currently bidding on any trades.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
