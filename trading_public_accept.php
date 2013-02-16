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
  header('Location: ./lostdata.php');
  exit();
}

if($user['license'] == 'no')
{
  header('Location: ./ltc.php?dialog=2');
  exit();
}

$bidid = (int)$_GET['id'];

$bid = get_bid($bidid);

if($bid === false)
{
  header('Location: ./trading_public_view.php?id=' . (int)$_GET['tradeid']);
  exit();
}

$tradeid = $bid['tradeid'];

$command = 'SELECT * FROM psypets_trading_house_requests WHERE idnum=' . $tradeid . ' LIMIT 1';
$trade = $database->FetchSingle($command, 'fetching trade');

if($trade === false)
{
  header('Location: ./trading_public2.php');
  exit();
}

if($trade['userid'] != $user['idnum'])
{
  header('Location: ./trading_public_view.php?id=' . $tradeid);
  exit();
}

$trade_items = str_replace(array('<li>', '</li>'), array('', ''), str_replace(array('</li><li>', ' &times;'), array('<br />', ';'), $trade['itemtext']));
$bid_items = str_replace(array('<li>', '</li>'), array('', ''), str_replace(array('</li><li>', ' &times;'), array('<br />', ';'), $bid['itemtext']));

$command = '
  INSERT INTO monster_trades (userid1, userid2, timestamp, step, dialog, items1, itemsdesc1, items2, itemsdesc2)
  VALUES (
    ' . $user['idnum'] . ', ' . $bid['userid'] . ', ' . $now . ', 3, \'<i>Public Trade</i>\',
    \'' . $trade['itemids'] . '\', ' . quote_smart($trade_items) . ',
    \'' . $bid['itemids'] . '\', ' . quote_smart($bid_items) . '
  )
';
$database->FetchNone($command, 'creating trade record');

$bidder = get_user_byid($bid['userid'], 'user');

$num_trade_items = (substr_count($trade['itemids'], ',') + 1);

// give items to bidder
$command = '
  UPDATE monster_inventory SET location=\'storage/incoming\',user=' . quote_smart($bidder['user']) . ',changed=' . $now . '
  WHERE idnum IN (' . $trade['itemids'] . ') LIMIT ' . $num_trade_items . '
';
$database->FetchNone($command, 'giving items to bidder');
flag_new_incoming_items($bidder['user']);

// give items to trade poster
$command = '
  UPDATE monster_inventory SET location=\'storage/incoming\',user=' . quote_smart($user['user']) . ',changed=' . $now . '
  WHERE idnum IN (' . $bid['itemids'] . ') LIMIT ' . (substr_count($bid['itemids'], ',') + 1) . '
';
$database->FetchNone($command, 'giving items to poster');
flag_new_incoming_items($user['user']);

psymail_user(
  $bidder['user'],
  $SETTINGS['site_ingame_mailer'],
  'Your bid was accepted! :)',
  '
    <p>Your bid on {r ' . $user['display'] . '}\'s public trade was accepted!  You received the following items:</p>
    <ul>' . $trade['itemtext'] . '</ul>
    <p>In exchange for:</p>
    <ul>' . $bid['itemtext'] . '</ul>
  ',
  $num_trade_items
);

$command = 'DELETE FROM psypets_trading_house_bids WHERE idnum=' . $bidid . ' LIMIT 1';
$database->FetchNone($command, 'deleting bid');

$command = 'SELECT * FROM psypets_trading_house_bids WHERE tradeid=' . $tradeid;
$bids = $database->FetchMultiple($command, 'fetching declined bids');

foreach($bids as $bid)
{
  $bidder = get_user_byid($bid['userid'], 'user');

  $num_bid_items = substr_count($bid['itemids'], ',') + 1;

  $command = '
    UPDATE monster_inventory SET location=\'storage/incoming\',changed=' . $now . '
    WHERE idnum IN (' . $bid['itemids'] . ') LIMIT ' . $num_bid_items . '
  ';
  $database->FetchNone($command, 'returning items to bidder');
  flag_new_incoming_items($bidder['user']);

  psymail_user(
    $bidder['user'],
    $SETTINGS['site_ingame_mailer'],
    'Your bid was not accepted! :(',
    '
      <p>Your bid on {r ' . $user['display'] . '}\'s public trade was not accepted!  The following items have been returned to you:</p>
      <ul>' . $bid['itemtext'] . '</ul>
    ',
    $num_bid_items
  );
}

$command = 'DELETE FROM psypets_trading_house_bids WHERE tradeid=' . $tradeid . ' LIMIT 1';
$database->FetchNone($command, 'deleting trade bids');

$command = 'DELETE FROM psypets_trading_house_requests WHERE idnum=' . $tradeid . ' LIMIT 1';
$database->FetchNone($command, 'deleting trade offer');

header('Location: ./incoming.php');
exit();
?>
