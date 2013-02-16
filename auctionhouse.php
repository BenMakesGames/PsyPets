<?php
$whereat = 'auctionhouse';
$wiki = 'Auction_House';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/messages.php';

if($NO_PVP)
{
  header('Location: ./lostdata.php');
  exit();
}

if($user['license'] == 'no')
{
  header('Location: ./ltc.php?dialog=2');
  exit();
}

$orderby = array(
  1 => 'itemname ASC',
  2 => 'itemname DESC',
  3 => 'bidvalue ASC',
  4 => 'bidvalue DESC',
  5 => 'bidtime ASC',
  6 => 'bidtime DESC',
);

$order = (int)$_GET['order'];
$page = (int)$_GET['page'];
$view = $_GET['view'];

if(!array_key_exists($order, $orderby))
  $order = 5;

$search = '';

if($view == 'sell')
  $command = 'SELECT * FROM monster_auctions WHERE claimed=\'yes\' AND ownerid=' . $user['idnum'];
else if($view == 'buy')
  $command = 'SELECT * FROM monster_auctions WHERE claimed=\'yes\' AND highbidder=' . $user['idnum'];
else
{
  if(array_key_exists('search', $_POST))
    $search = $_POST['search'];
  else if(array_key_exists('search', $_GET))
    $search = $_GET['search'];

  $command = 'SELECT * FROM monster_auctions WHERE claimed=\'no\' AND (bidtime>' . $now . ' OR ownerid=' . $user['idnum'] . ' OR highbidder=' . $user['idnum'] . ')';

  if($search != '')
    $command .= ' AND itemname LIKE ' . quote_smart('%' . $search . '%');

  $view = '';
}

$count_command = str_replace('SELECT *', 'SELECT COUNT(*) AS c', $command);

$data = $database->FetchSingle($count_command, 'auctionhouse.php');

$num_auctions = (int)$data['c'];

if($num_auctions > 0)
{
  $num_pages = ceil($num_auctions / 20);
  if($page < 1)
    $page = 1;
  else if($page > $num_pages)
    $page = $num_pages;

  $auction_items = $database->FetchMultiple($command . ' ORDER BY ' . $orderby[$order] . ' LIMIT ' . (($page - 1) * 20) . ',20', 'auctionhouse.php');
}

$auction_hints = array(
  'For your convenience, the items listed here may be sorted by their name, the current bid, or the time remaining.',
  'Daily Storage Box auctions have been temporarily suspended.  Keep an eye out for their return!',
);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Auction House</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Auction House</h4>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(",", $_GET["msg"]));
?>
     <ul class="tabbed">
      <li<?= $view == '' ? ' class="activetab"' : '' ?>><a href="/auctionhouse.php">Items up for bid</a></li>
      <li<?= $view == 'sell' ? ' class="activetab"' : '' ?>><a href="/auctionhouse.php?view=sell">Items you have auctioned</a></li>
      <li<?= $view == 'buy' ? ' class="activetab"' : '' ?>><a href="/auctionhouse.php?view=buy">Items you have won in auction</a></li>
     </ul>
<?php
echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/auctioneer2.png" align="right" width="350" height="392" alt="(The auction house manager)" />';
include 'commons/dialog_open.php';

if($error_message)
  echo "<p>$error_message</p>";
else
  echo '<p>Welcome to my Auction house, available exclusively to those with a License to Commerce.</p>' .
       '<p>If you\'d like to host an auction of your own, please let me know.</p>' .
       '<p>' . $auction_hints[array_rand($auction_hints)] . '</p>';

include 'commons/dialog_close.php';

if($TIME_IS_FUCKED === true)
  echo '<p><i>(Hosting new auctions has been disabled.  Refer to the City Hall for more information.)</i></p>';
else
{
?>
     <ul>
      <li><a href="/newauction.php">Host new auction</a></li>
     </ul>
<?php
}

if($view == '')
{
?>
<form action="auctionhouse.php" method="post">
<p>Item name: <input name="search" value="<?= $search ?>" /> <input type="submit" name="submit" value="Search" /></p>
</form>
<?php
}

if($num_auctions > 0)
{
  $pages = paginate($num_pages, $page, 'auctionhouse.php?view=' . $view . '&search=' . $search . '&order=' . $order . '&page=%s');

  if($num_pages > 1)
    echo $pages;

  $sort_url = 'auctionhouse.php?view=' . $view . '&page=' . $page . '&order=';

  $itemname_link = '<a href="' . $sort_url . '1">&#9661;</a>';
  $highbid_link = '<a href="' . $sort_url . '3">&#9661;</a>';
  $bidtime_link = '<a href="' . $sort_url . '5">&#9661;</a>';

  if($order == 1)
    $itemname_link = '<a href="' . $sort_url . '2">&#9660;</a>';
  else if($order == 2)
    $itemname_link = '<a href="' . $sort_url . '1">&#9650;</a>';
  if($order == 3)
    $highbid_link = '<a href="' . $sort_url . '4">&#9660;</a>';
  else if($order == 4)
    $highbid_link = '<a href="' . $sort_url . '3">&#9650;</a>';
  if($order == 5)
    $bidtime_link = '<a href="' . $sort_url . '6">&#9660;</a>';
  else if($order == 6)
    $bidtime_link = '<a href="' . $sort_url . '5">&#9650;</a>';
?>
     <table>
      <tr class="titlerow">
       <th></th>
       <th>Item&nbsp;<?= $itemname_link ?></th>
       <th>Owner</th>
       <th>High Bidder</th>
       <th>High Bid <?= $highbid_link ?></th>
       <th><nobr>Time Remaining <?= $bidtime_link ?></nobr></th>
<!--       <th></th>-->
      </tr>
<?php
  $rowclass = begin_row_class();

  foreach($auction_items as $item)
  {
    $details = get_item_byname($item['itemname']);

    $high_bidder_info = get_user_byid($item['highbidder']);
    $owner_info = get_user_byid($item['ownerid']);

    $high_bidder = $high_bidder_info['display'];
    $owner = $owner_info["display"];

    if($details['custom'] == 'yes')
      $custom = '<br />(custom item)';
    else if($details['custom'] == 'monthly' || $details['custom'] == 'recurring')
      $custom = '<br />(monthly item)';
    else
      $custom = '';
?>
      <tr class="<?= $rowclass ?>">
       <td class="centered"><?= item_display_extra($details) ?></td>
       <td><?= $item['itemname'] . $custom ?></td>
       <td><a href="userprofile.php?user=<?= link_safe($owner) ?>"><?= $owner ?></a></td>
       <td><?php
if(strtolower($high_bidder) == $SETTINGS['site_ingame_mailer'])
  echo '--';
else
  echo '<a href="userprofile.php?user=' . link_safe($high_bidder) . '">' . $high_bidder . '</a>';
?></td>
       <td align="right"><a href="auctiondetails.php?auction=<?= $item['idnum'] ?>"><?= $item['bidvalue'] ?></a></td>
       <td><?= time_amount($item['bidtime'] - $now) ?></td>
<!--       <td><nobr><a href="auctiondetails.php?auction=<?= $item['idnum'] ?>">details</a></nobr></td>-->
      </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
     </table>
<?php
  if($num_pages > 1)
    echo $pages;
}
else
  echo "     <p>There are no such items at this time.</p>\n";
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
