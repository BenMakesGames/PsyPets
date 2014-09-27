<?php
$whereat = 'auctionhouse';
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
require_once 'commons/itemlib.php';

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

$categories = array();

$command = 'SELECT * FROM monster_auctions WHERE idnum=' . (int)$_GET['auction'] . ' LIMIT 1';
$auction_details = $database->FetchSingle($command, 'fetching auction details');

if($auction_details === false)
{
  header('Location: ./auctionhouse.php');
  exit();
}

$auction_item = get_item_byname($auction_details['itemname']);
$auction_real_item = get_inventory_byid($auction_details['itemid']);

if($auction_details['highbidder'] > 0)
  $auction_highbidder = get_user_byid($auction_details['highbidder']);

$increment = pow(10, max(0, floor(log($auction_details['bidvalue'], 10)) - 1));

include 'commons/html.php';
?>
 <head>
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; Auction House &gt; <?= $auction_details['itemname'] ?></title>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="auctionhouse.php">Auction House</a> &gt; <?= $auction_details['itemname'] ?></h4>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";

if($auction_details['bidtime'] > $now)
{
?>
     <h4>Highest Bid</h4>
     <p><?php
  if($auction_highbidder['idnum'] > 0)
    echo $auction_details['bidvalue'] . '<span class="money">m</span> by ' . resident_link($auction_highbidder['display']);
  else
    echo '<i>No one has bid on this item</i>.';
?></p>
     <p><?= time_amount($auction_details['bidtime'] - $now) ?> remain.</p>
<?php
  if($user['idnum'] != $auction_details['ownerid'] && $user['idnum'] != $auction_details['highbidder'])
  {
?>
     <p>The lowest you may bid is <?= $auction_details['bidvalue'] + $increment ?><span class="money">m</span>.</p>
     <form action="placebid.php?auction=<?= $auction_details['idnum'] ?>" method="post"><p><input maxlength="9" size="9" name="bidvalue" />&nbsp;<input type="submit" value="Place Bid" /></p></form>
<?php
  }
}
else
{
?>
     <h4>Bidding Complete</h4>
     <p>
<?php
  if($auction_highbidder['idnum'] > 0)
  {
?>
      For <?= $auction_details['bidvalue'] ?><span class="money">m</span> by <?= resident_link($auction_highbidder['display']) ?><br />
<?php
    if($auction_details['claimed'] == 'no')
    {
      if($auction_highbidder['idnum'] == $user['idnum'])
      {
?>
      <form action="finalizeauction.php?auction=<?= $auction_details['idnum'] ?>" method="post"><input type="submit" value="Claim Item" class="bigbutton" /></form><br />
<?php
      }
      else if($auction_details['ownerid'] == $user['idnum'])
      {
?>
      <form action="finalizeauction.php?auction=<?= $auction_details['idnum'] ?>" method="post"><input type="submit" value="Claim Bid Money" class="bigbutton" /></form><br />
<?php
      }
    } // not claimed
  }
  else
  {
    echo '<i>No one has bid on this item</i>.<br />';

    if($auction_details['claimed'] == 'no' && $auction_details['ownerid'] == $user['idnum'])
    {
?>
      <form action="finalizeauction.php?auction=<?= $auction_details['idnum'] ?>" method="POST"><input type="submit" value="Reclaim Item" class="bigbutton" /></form><br />
<?php
    }
  }
?>
     </p>
<?php
} // time is up
?>
     <h4>Auction Details</h4>
<?php
$host = get_user_byid($auction_details['ownerid'], 'display');

if($auction_details['claimed'] == 'no')
{
  echo '<p>Hosted by <a href="residentprofile.php?resident=' . link_safe($host['display']) . '">' . $host['display'] . '</a>';

  if(strlen($auction_details['ldesc']) > 0)
    echo ':</p><p style="margin-left:2em;">' . format_text($auction_details['ldesc']) . '</p>';
  else
    echo '.</p>';

  echo '<h5>Item Details</h5>';

  $comment = '';

  if(strlen($auction_real_item['message']) > 0)
    $comment .= $auction_real_item['message'];

  if(strlen($auction_real_item['message2']) > 0)
  {
    if(strlen($comment) > 0)
      $comment .= '<br />';

    $comment .= $auction_real_item['message2'];
  }

  $maker = item_maker_display($auction_real_item['creator'], true);
?>
     <table>
      <tr class="titlerow">
       <th></th><th></th><th>Item</th><th>Maker</th><th>Comment</th>
      </tr>
      <tr>
       <td align="center"><?= item_display($auction_item, '') ?></td>
       <td><?= ($auction_item['bulk'] / 10) . '/' . ($auction_item['weight'] / 10) ?></td>
       <td><?= $auction_item['itemname'] ?></td>
       <td><?= $maker ?></td>
       <td><?= $comment ?></td>
      </tr>
     </table>
<?php
} // auction has not completed
else
{
?>
     <p>Auctioned by <a href="residentprofile.php?resident=<?= link_safe($host['display']) ?>"><?= $host['display'] ?></a></p>
     <p><?= format_text($auction_details['ldesc']) ?></p>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
