<?php
$whereat = 'bank';
$wiki = 'Trading_House';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
require_once 'commons/userlib.php';
require_once 'commons/publictradinglib.php';

if($NO_PVP)
{
  header('Location: /lostdata.php');
  exit();
}

if($user['license'] == 'no')
{
  header('Location: /ltc.php?dialog=2');
  exit();
}

$tradeid = (int)$_GET['id'];

$trade = $database->FetchSingle('SELECT * FROM psypets_trading_house_requests WHERE idnum=' . $tradeid . ' LIMIT 1');

if($trade === false)
{
  header('Location: /trading_public2.php');
  exit();
}

if($trade['userid'] == $user['idnum'])
{
  $my_trade = true;
  $owner = $user;
}
else
{
  $my_trade = false;
  $owner = get_user_byid($trade['userid'], 'display');
  $my_bid = has_bid_on_trade($user['idnum'], $tradeid);
}

$bids = get_bids_on_trade($tradeid);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Trading House &gt; <?= $trade['sdesc'] ?></title>
<?php include 'commons/head.php'; ?>
  <style type="text/css">
   h6 { text-decoration: none; border-bottom: 1px solid #000; }
  </style>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="trading_public2.php">Trading House</a> &gt; <?= $trade['sdesc'] ?></h4>
     <p>Posted by <?= resident_link($owner['display']) . ' ' . duration($now - $trade['timestamp'], 2) ?> ago.</p>
<?php
if($my_trade)
  echo '
    <ul>
     <li><a href="trading_public_edit.php?id=' . $tradeid . '">Edit descriptions</a></li>
     <li><a href="trading_public_cancel.php?id=' . $tradeid . '" onclick="return confirm(\'Really cancel this trade?\');">Cancel</a></li>
    </ul>
  ';

if(strlen($trade['ldesc']) > 0)
  echo '<p>' . format_text($trade['ldesc']) . '</p>';
?>
     <h5>Offering</h5>
     <ul><?= $trade['itemtext'] ?></ul>
     <h5>Current Bids</h5>
<?php
if(count($bids) == 0)
  echo '<p>No one has bid on this trade.</p>';

if(!$my_trade)
{
  if($my_bid === false)
    echo '<ul><li><a href="/trading_public_bid.php?id=' . $tradeid . '">Place Bid</a></li></ul>';
  else
    echo '<ul><li><a href="/trading_public_bid_cancel.php?id=' . $my_bid['idnum'] . '&amp;tradeid=' . $tradeid . '">Cancel my Bid</a></li></ul>';
}

if(count($bids) > 0)
{
  foreach($bids as $bid)
  {
    $resident = get_user_byid($bid['userid'], 'display');
    
    echo '<h6>' . resident_link($resident['display']) . ', ' . duration($now - $bid['timestamp'], 2) . ' ago</h6>';

    if($bid['itemtable'] == '')
      echo '<ul>' . $bid['itemtext'] . '</ul>';
    else
      echo '<table><tr class="titlerow"><th></th><th>Item</th><th>Quantity</th></tr>' . $bid['itemtable'] . '</table>';

    if($my_trade)
      echo '<ul><li><a href="/trading_public_accept.php?id=' . $bid['idnum'] . '&amp;tradeid=' . $tradeid . '" onclick="return confirm(\'Really accept this bid?  Once it\\\'s done, it\\\'s done!\');">Accept Bid</a></li></ul>';
  }

}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
