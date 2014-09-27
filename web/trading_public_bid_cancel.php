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

if($bid === false || $bid['userid'] != $user['idnum'])
{
  header('Location: ./trading_public_view.php?id=' . (int)$_GET['tradeid']);
  exit();
}

// return items to bidder
$command = '
  UPDATE monster_inventory SET location=\'storage/incoming\',changed=' . $now . '
  WHERE idnum IN (' . $bid['itemids'] . ') LIMIT ' . (substr_count($bid['itemids'], ',') + 1) . '
';
$database->FetchNone($command, 'returning items to bidder');
flag_new_incoming_items($user['user']);

$command = 'DELETE FROM psypets_trading_house_bids WHERE idnum=' . $bidid . ' LIMIT 1';
$database->FetchNone($command, 'deleting trade bid');

header('Location: ./incoming.php');
exit();
?>
