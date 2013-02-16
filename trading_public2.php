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

if($user['license'] == 'no')
{
  header('Location: /ltc.php?dialog=2');
  exit();
}

if(array_key_exists('search', $_GET))
{
  $_POST['action'] = 'search';
  $_POST['title'] = $_GET['title'];
  $_POST['offering'] = $_GET['offering'];
}

$where_values = array();

if($_POST['action'] == 'search')
{
  $_POST['title'] = trim($_POST['title']);
  $_POST['offering'] = trim($_POST['offering']);

  $url_extra = '&amp;search';

  if($_POST['title'] != '')
  {
    $url_extra .= '&amp;title=' . $_POST['title'];
    $where_values[] = 'sdesc LIKE ' . quote_smart('%' . $_POST['title'] . '%');
  }

  if($_POST['offering'] != '')
  {
    $url_extra .= '&amp;offering=' . $_POST['offering'];
    $where_values[] = 'itemtext LIKE ' . quote_smart('%' . $_POST['offering'] . '%');
  }

  $searched = true;
}

if((int)$_GET['myoffers'] > 0)
{
  $url_extra .= '&amp;myoffers=1';
  $where_values[] = 'userid=' . $user['idnum'];
  $search_self = true;
  
  if($user['new_bid'] == 'yes')
  {
    $command = 'UPDATE monster_users SET new_bid=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'clearing new bid flag');
    
    $user['new_bid'] = 'no';
  }
}

if(count($where_values) > 0)
  $where_clause = ' WHERE ' . implode(' AND ', $where_values);

$command = 'SELECT COUNT(*) AS c FROM psypets_trading_house_requests' . $where_clause;
$data = $database->FetchSingle($command, 'trading_public.php');

$trade_count = (int)$data['c'];

$numpages = max(1, ceil($trade_count / 20));
$page = (int)$_GET['page'];
if($page < 1)
  $page = 1;
else if($page > $numpages)
  $page = $numpages;

$command = 'SELECT * FROM psypets_trading_house_requests' . $where_clause . ' ORDER BY idnum DESC LIMIT ' . (($page - 1) * 20) . ',20';
$offers = $database->FetchMultiple($command, 'trading_public.php');

$search_command = $command;

$command = 'SELECT * FROM psypets_trading_house_requests WHERE userid=' . $user['idnum'];
$mytrades = $database->FetchMultiple($command, 'trading_public.php');

$num_open_trades = count($mytrades);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Trading House</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Trading House</h4>
     <ul class="tabbed">
      <li class="activetab"><a href="trading_public2.php">Public Trade Offers</a></li>
      <li><a href="trading.php">Private Trading</a></li>
     </ul>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";

$post_cost = post_public_trade_cost($num_open_trades);
?>
     <ul>
      <li><a href="newpublictrade2.php">Post a new public trade offer (<?= $post_cost ?><span class="money">m</span>)</a></li>
      <li><a href="trading_public_mybids.php">View my bids</a></li>
<?php
if($search_self)
  echo '<li><a href="trading_public2.php">View all offers</a></li>';
else if($num_open_trades > 0)
  echo '<li><a href="trading_public2.php?myoffers=1">View my offers</a></li>';
?>
     </ul>
<?php

if($numpages > 1 || $_POST['action'] == 'search' || $_GET['action'] == 'search')
{
  echo '     <h5>Search</h5>';

  if(count($errors) > 0)
    echo '<ul><li>' . implode('</li><li>', $errors) . '</li></ul>';
/*
    if($user['user'] == 'telkoth')
      echo '<p>Query: ' . $search_command . '</p>';
*/
?>
     <p>You do not have to fill in both fields; you may leave one blank.</p>
     <form action="trading_public2.php<?= $_GET['view'] == 'old' ? '?view=old' : '' ?>" method="post">
     <table class="nomargin">
      <tr>
       <th>Asking:</th>
       <td><input name="title" value="<?= $_POST['title'] ?>" /></td>
      </tr>
      <tr>
       <th>Offering:</th>
       <td><input name="offering" value="<?= $_POST['offering'] ?>" /></td>
      </tr>
     </table>
     <p><input type="hidden" name="action" value="search" /><input type="submit" value="Search" /></p>
     </form>
<?php
}

if(count($offers) > 0)
{
  if($searched)
    echo '<p>The following ' . $trade_count . ' trade' . ($trade_count > 1 ? 's' : '') . ' matched your search.</p>' .
         '<ul><li><a href="trading_public2.php">Browse all public trades</a></li></ul>';
  else if($search_self)
    echo '<p>You have ' . $num_open_trades . ' open public trade offer' . ($num_open_trades != 1 ? 's' : '') . '.</p>';
  else
    echo '<p>There are currently ' . $trade_count . ' trade' . ($trade_count > 1 ? 's' : '') . ' available.</p>';

  $page_list = paginate($numpages, $page, 'trading_public2.php?sort=' . $sort . '&page=%s' . $url_extra);

  if($numpages > 1)
    echo $page_list;
?>
     <p><table>
      <tr class="titlerow">
       <th></th><th>Bids</th><th>Asking</th><th>Offering</th><th>Resident</th><th>Posted</th>
      </tr>
<?php
  $rowclass = begin_row_class();

  foreach($offers as $offer)
  {
    $offer_text = trim_to($offer['itemtext'], '</li><li>', 5, '</li><li>(and more...)</li>');
  
?>
      <tr class="<?= $rowclass ?>">
       <td><nobr><a href="trading_public_view.php?id=<?= $offer['idnum'] ?>">View / Bid</a></nobr></td>
<?php
    $items_have = (int)$inventory[$asking['itemname']]['c'];

    if($offer['userid'] == $user['idnum'])
      $offerer = $user;
    else
      $offerer = get_user_byid($offer['userid'], 'display');

    $num_bids = get_bid_count($offer['idnum']);
?>
       <td class="centered"><?= $num_bids ?></td>
       <td><?= $offer['sdesc'] ?></td>
       <td><ul class="plainlist nomargin"><?= $offer_text ?></ul></td>
       <td><?= resident_link($offerer['display']) ?></td>
       <td><?= duration($now - $offer['timestamp'], 2) ?> ago</td>
      </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
     </table></p>
<?php
  if($numpages > 1)
    echo $page_list;
}
else if($searched)
  echo '<p>No public trades matched your search.</p>' .
       '<ul><li><a href="trading_public2.php">Browse all public trades</a></li></ul>';
else
  echo '<p>There are no public trades right now.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
