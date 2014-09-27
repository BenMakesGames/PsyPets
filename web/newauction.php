<?php
$whereat = 'auctionhouse';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/economylib.php';

if($user['license'] == 'no' || $TIME_IS_FUCKED === true)
{
  header('Location: ./auctionhouse.php');
  exit();
}

//$auction_fee = value_with_inflation(15);
$auction_fee = 20;

$error_strings = array();

if($_POST['action'] == 'newauction')
{
  if(strlen($_POST["startbid"]) == 0)
    $error_strings[] = 'You need to provide a starting bid of <em>at least</em> 1<span class="money">m</span>.';
  else if(!is_numeric($_POST["startbid"]))
    $error_strings[] = 'The starting bid should be a number, and must be at least 1<span class="money">m</span>.';
  else if($_POST["startbid"] - (int)$_POST["startbid"] != 0)
    $error_strings[] = 'The starting bid should be a <em>whole number</em>, and must be at least 1<span class="money">m</span>.';
  else
    $startbid = (int)$_POST['startbid'];

  $start_time = (int)$_POST['starttime'];
  
  if($start_time < 1 || $start_time > 8)
    $error_strings[] = 'You did not select an initial bid time.';
  else
    $bid_time = $now + $start_time * 12 * 60 * 60;

  $itemid = 0;

  if(is_numeric($_POST["item"]))
  {
    $auction_item = get_inventory_byid((int)$_POST["item"]);
    $itemid = $auction_item["idnum"];
    $itemname = $auction_item["itemname"];

    $item_info = get_item_byname($itemname);

    if($item_info['noexchange'] == 'yes' || $item_info['cursed'] == 'yes' || $auction_item['user'] != $user['user'] || $auction_item['location'] != 'storage')
      $error_strings[] = 'You cannot auction that item, I\'m afraid...';
  }
  else
    $error_strings[] = 'You forgot to choose the item to auction! :)';

  if($user['money'] < $auction_fee * $start_time)
    $error_strings[] = 'Hosting an auction for this amount of time would cost ' . ($auction_fee * $start_time) . '<span class="money">m</span>, however you currently have only ' . $user['money'] . '<span class="money">m</span>.';

  if(count($error_strings) == 0)
  {
    $_POST['ldesc'] = format_text($_POST['ldesc']);

    $command = '
      INSERT INTO monster_auctions
      (`ownerid`, `itemid`, `itemname`, `ldesc`, `bidvalue`, `bidtime`)
      VALUES
      (
        ' . $user["idnum"] . ',
        ' . $itemid . ',
        ' . quote_smart($itemname) . ',
        ' . quote_smart($_POST["ldesc"]) . ',
        ' . $startbid . ',
        ' . $bid_time . '
      )
    ';
    $database->FetchNone($command, 'adding auction');

    $idnum = $database->InsertID();

    $command = "UPDATE monster_inventory SET location='storage/outgoing',forsale=0 WHERE idnum=$itemid LIMIT 1";
    $database->FetchNone($command, 'removing item from inventory');

    take_money($user, $auction_fee * $start_time, 'Auction fee');

    header('Location: ./auctiondetails.php?auction=' . $idnum);
    exit();
  }
}

$my_inventory = $database->FetchMultiple('
  SELECT
    a.idnum,a.itemname,a.creator,a.message,a.message2,
    b.graphic,b.graphictype
  FROM
    monster_inventory AS a
    LEFT JOIN monster_items AS b
      ON a.itemname=b.itemname
  WHERE
    a.user=' . quote_smart($user['user']) . '
    AND a.location=\'storage\'
    AND b.cursed=\'no\'
    AND b.noexchange=\'no\'
');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Auction House &gt; New Auction</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="auctionhouse.php">Auction House</a> &gt; New Auction</h4>
<?php
echo '<img src="gfx/npcs/auctioneer.png" align="right" width="350" height="450" alt="(Richard the auction house manager)" />';
include 'commons/dialog_open.php';

if(count($error_strings) > 0)
  echo '<p class="failure">' . implode('</p><p class="failure">', $error_strings) . '</p>';
else
{
?>
     <p>Of course.  Let me know what you'd like to put up for auction, and feel free to provide a description for prospective buyers (links to other auctions you're hosting, or to your store, are a couple example uses).</p>
     <p>Be aware that there is a non-refundable fee for hosting an auction depending on how long you want us to hold your item for auction.</p>
<?php
}

include 'commons/dialog_close.php';
?>
     <form action="newauction.php" method="post">
     <table>
      <tr>
       <th>Starting bid:</th>
       <td><input name="startbid" value="<?= $_POST["startbid"] ?>" maxlength="5" size="5" /><span class="money">m</span></td>
      </tr>
      <tr>
       <th>Bidding time:</th>
       <td><select name="starttime">
        <option value="1"<?= $start_time == 1 ? ' selected' : '' ?>>12 hours (<?= $auction_fee ?>m fee)</option>
        <option value="2"<?= $start_time == 2 ? ' selected' : '' ?>>1 day (<?= 2 * $auction_fee ?>m fee)</option>
        <option value="3"<?= $start_time == 3 ? ' selected' : '' ?>>1&frac12; days (<?= 3 * $auction_fee ?>m fee)</option>
        <option value="4"<?= $start_time == 4 ? ' selected' : '' ?>>2 days (<?= 4 * $auction_fee ?>m fee)</option>
        <option value="5"<?= $start_time == 5 ? ' selected' : '' ?>>2&frac12; days (<?= 5 * $auction_fee ?>m fee)</option>
        <option value="6"<?= $start_time == 6 ? ' selected' : '' ?>>3 days (<?= 6 * $auction_fee ?>m fee)</option>
        <option value="7"<?= $start_time == 7 ? ' selected' : '' ?>>3&frac12; days (<?= 7 * $auction_fee ?>m fee)</option>
        <option value="8"<?= $start_time == 8 ? ' selected' : '' ?>>4 days (<?= 8 * $auction_fee ?>m fee)</option>
       </select></td>
      </tr>
     </table>
     <table>
      <tr>
       <th>Bid description (optional):</th>
      </tr>
      <tr>
       <td><textarea cols="50" rows="10" name="ldesc"><?= $_POST["ldesc"] ?></textarea></td>
      </tr>
      <tr>
       <td></td>
      </tr>
      <tr>
       <th>Item for auction:</th>
      </tr>
     </table>
     <table>
      <tr class="titlerow">
       <th></th>
       <th></th>
       <th>Item</th>
       <th>Maker</th>
       <th>Comment</th>
      </tr>
<?php
if(count($my_inventory) > 0)
{
  $rowclass = begin_row_class();

  foreach($my_inventory as $item)
  {
?>
      <tr class="<?= $rowclass ?>">
       <td><input type="radio" name="item" value="<?= $item['idnum'] ?>"<?= $_POST['item'] == $item['idnum'] ? ' checked' : '' ?> /></td>
       <td align="center"><?= item_display($item, '') ?></td>
       <td><?= $item['itemname'] ?></td>
       <td><?= item_maker_display($item['creator'], true) ?></td>
       <td><?= $item['message'] . '<br />' . $item['message2'] ?></td>
      </tr>
<?php

     $rowclass = alt_row_class($rowclass);
   }
 }
?>
     </table>
     <p><i>(No refunds!  Double check the auction information to make sure it's correct!)</i></p>
     <p><input type="hidden" name="action" value="newauction" /><input type="submit" value="Begin Auction" class="bigbutton" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
