<?php
$whereat = 'auctionhouse';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/itemlib.php';

if($user['license'] == 'no')
{
  header('Location: ./myhouse.php');
  exit();
}

$now = time();

$categories = array();

if(is_numeric($_GET['auction']) && $_GET['auction'] > 0)
{
  $command = 'SELECT * FROM monster_auctions WHERE idnum=' . quote_smart($_GET['auction']) . ' LIMIT 1';
  $auction_details = $database->FetchSingle($command, 'fetching auction details');

  if($auction_details['claimed'] == 'yes')
  {
    header('Location: ./auctiondetails.php?auction=' . $auction_details['idnum']);
    exit();
  }
}
else
{
  header('Location: ./auctionhouse.php');
  exit();
}

$auction_item = get_item_byname($auction_details['itemname']);
$auction_real_item = get_inventory_byid($auction_details['itemid']);
$auction_owner = get_user_byid($auction_details['ownerid']);

if($auction_details['highbidder'] > 0)
  $auction_highbidder = get_user_byid($auction_details['highbidder']);
else
  $auction_highbidder = false;

if($auction_details['bidtime'] > $now)
{
  // bidding is still in progress
  header('Location: ./auctiondetails.php?auction=' . $auction_details['idnum']);
  exit();
}
else if($auction_details['ownerid'] != $user['idnum'] && $auction_details['highbidder'] != $user['idnum'])
{
  // you're not involved in this auction - go away
  header('Location: ./auctiondetails.php?auction=' . $auction_details['idnum']);
  exit();
}
else
{
  if($auction_highbidder !== false)
  {
    // there is a high bidder

    $command = "UPDATE monster_auctions SET claimed='yes' WHERE idnum=" . $auction_details["idnum"] . " LIMIT 1";
    $database->FetchNone($command, 'marking auction as claimed');

    $command = "UPDATE monster_inventory SET `user`=" . quote_smart($auction_highbidder['user']) . ",`location`='storage/incoming',`changed`=$now,`message2`='You won this item in an auction.' WHERE idnum=" . $auction_details["itemid"] . ' LIMIT 1';
    $database->FetchNone($command, 'giving item to high bidder');

    flag_new_incoming_items($auction_highbidder['user']);

    $command = 'UPDATE monster_users SET money=money+' . $auction_details["bidvalue"] . " WHERE idnum=" . $auction_details["ownerid"] . " LIMIT 1";
    $database->FetchNone($command, 'giving money to auctioner');

    add_transaction($auction_owner['user'], $now, 'Auction earnings', $auction_details["bidvalue"]);

    if($user['user'] != $auction_owner['user'])
    {
      if($auction_owner['user'] != $SETTINGS['site_ingame_mailer'])
      {
        psymail_user($auction_owner['user'], $SETTINGS['site_ingame_mailer'], 'Your ' . $auction_details["itemname"] . " auction has completed", "The high bidder, " . $auction_highbidder['display'] . ', claimed the item for ' . $auction_details['bidvalue'] . '{m}');
        flag_new_mail($auction_owner['user']);
      }
    }
    else
    {
      psymail_user($auction_highbidder["user"], $SETTINGS['site_ingame_mailer'], "You won the " . $auction_details["itemname"] . " auction!", "The auction owner has claimed your high bid of " . $auction_details['bidvalue'] . "{m}, and the item has been sent to " . $auction_highbidder['incomingto'] . '.');
      flag_new_mail($auction_highbidder['user']);
    }

    // redirect to the auction house with a message
    if($auction_details['ownerid'] == $user['idnum'])
      header('Location: ./auctionhouse.php?msg=21:' . link_safe($auction_details['bidvalue']));
    else
      header('Location: ./auctionhouse.php?msg=20:' . link_safe($auction_details['itemname']));
  }
  else
  {
    // there is no high bidder

    // delete the auction
    $command = 'DELETE FROM monster_auctions WHERE idnum=' . $auction_details["idnum"] . ' LIMIT 1';
    $database->FetchNone($command, 'deleting auction');

    // give the item back
    $command = 'UPDATE monster_inventory SET `user`=' . quote_smart($auction_owner['user']) . ",`location`='storage/incoming',`changed`=$now WHERE idnum=" . $auction_details['itemid'] . ' LIMIT 1';
    $database->FetchNone($command, 'returning auction item');

    flag_new_incoming_items($auction_owner['user']);

    header('Location: ./auctionhouse.php?msg=20:' . link_safe($auction_details['itemname']));
  }
}
?>
