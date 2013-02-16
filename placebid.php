<?php
$whereat = 'auctionhouse';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/rpgfunctions.php';

if($user['license'] == 'no')
{
  header('Location: ./myhouse.php');
  exit();
}

$command = 'SELECT * FROM monster_auctions WHERE idnum=' . (int)$_GET['auction'] . ' LIMIT 1';
$auction_details = $database->FetchSingle($command, 'fetching auction details');

if($auction_details === false || $auction_details['claimed'] == 'yes' || $auction_details['bidtime'] <= $now)
{
  header('Location: ./auctionhouse.php');
  exit();
}

$auction_item = get_item_byname($auction_details['itemname']);

$auction_real_item = get_inventory_byid($auction_details['itemid']);

$auction_owner = get_user_byid($auction_details['ownerid']);

$bid_amount = (int)$_POST['bidvalue'];

// field length is 9 characters
if($bid_amount > 999999999)
{
  header('Location: ./auctiondetails.php?auction=' . $auction_details['idnum']);
  exit();
}

$increment = pow(10, max(0, floor(log($auction_details["bidvalue"], 10)) - 1));

// simple check to find people bidding on their own items

$match_name = '/' . preg_replace('/[0-9]+/', '[0-9]*', $user['user']) . '/';

// TODO: message an administrator if the user accounts bidding and hosting are suspiciously similar
/*
if(preg_match($match_name, $auction_owner['user']) > 0)
  psymail_user('telkoth', $SETTINGS['site_ingame_mailer'], 'possible self-bidding', $user['display'] . ' (' . $user['user'] . ') bid on auction #' . $auction_details['idnum'] . ' hosted by ' . $auction_owner['display'] . ' (' . $auction_owner['user'] . ')');
*/

// ---

if($auction_details['highbidder'] > 0)
{
  $command = "SELECT * FROM monster_users WHERE idnum=" . $auction_details["highbidder"] . " LIMIT 1";
  $auction_highbidder = $database->FetchSingle($command, 'fetching high bidder');
}

if($auction_details['ownerid'] == $user['idnum'] || $auction_details["highbidder"] == $user["idnum"])
{
  // you're already involved in this auction - go away
  header('Location: ./auctiondetails.php?auction=' . $auction_details['idnum']);
  exit();
}
else if($now > $auction_details['bidtime'])
{
  // bidding is over
  header('Location: ./auctiondetails.php?auction=' . $auction_details['idnum']);
  exit();
}
else if($bid_amount < $increment + $auction_details["bidvalue"] || !is_numeric($bid_amount) || (int)$bid_amount != $bid_amount)
{
  // you didn't bid enough
  header('Location: ./auctiondetails.php?auction=' . $auction_details["idnum"] . "&msg=23:" . ($auction_details["bidvalue"] + $increment));
  exit();
}
else if($bid_amount > $user['money'])
{
  // you don't have enough money
  header('Location: ./auctiondetails.php?auction=' . $auction_details['idnum'] . '&msg=22');
  exit();
}
else
{
  if($auction_details['highbidder'] > 0)
  {
    $database->FetchNone('UPDATE monster_users SET money=money+' . $auction_details['bidvalue'] . ' WHERE idnum=' . $auction_details['highbidder'] . ' LIMIT 1');

		add_transaction($auction_highbidder['user'], $now, 'Auction refund', $auction_details['bidvalue']);

    psymail_user(
      $auction_highbidder['user'],
      $SETTINGS['site_ingame_mailer'],
      $auction_details['itemname'] . ' auction',
      'You were outbid on this item and have been refunded ' . $auction_details['bidvalue'] . '<span class="money">m</span>.  <a href="//' . $SETTINGS['site_domain'] . '/auctiondetails.php?auction=' . $auction_details["idnum"] . '">Check out the auction details</a>.'
    );
  }

  $command = "UPDATE monster_users SET money=money-$bid_amount WHERE idnum=" . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'deducting bid amount from bidder');

  if($auction_details['bidtime'] < time() + 8 * 60 * 60)
    $command = "UPDATE monster_auctions SET bidvalue=$bid_amount, highbidder=" . $user['idnum'] . ', bidtime=' . (time() + 8 * 60 * 60) . ' WHERE idnum=' . $auction_details['idnum'] . ' LIMIT 1';
  else
    $command = "UPDATE monster_auctions SET bidvalue=$bid_amount, highbidder=" . $user['idnum'] . ' WHERE idnum=' . $auction_details['idnum'] . ' LIMIT 1';

  $database->FetchNone($command, 'recording new high bid');

  if($bid_amount >= 1000)
  {
    $badges = get_badges_byuserid($user['idnum']);
    if($badges['spender'] == 'no')
    {
      set_badge($user['idnum'], 'spender');

      $body = 'The Auction House owner has asked me to contact you on his behalf.  Well, I should say that he\'s my father.  I help take care of some of the paperwork...<br /><br />' .
              'Anyway, Mr. Silloway would like to express his gratitude for your patronage, and has asked that I deliver this badge to you as thanks.<br /><br />' .
              'He of course looks forward to seeing you again, and would like to remind you that his facilities are always available.<br /><br />' .
              '<em>(You earned the Big Spender Badge!)</em>';

      psymail_user($user['user'], 'csilloway', 'You bid over 1,000 moneys in an auction!', $body);

    }
  }

  header('Location: ./auctiondetails.php?auction=' . $auction_details['idnum']);
}
?>
