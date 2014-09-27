<?php
$whereat = 'storage';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/utility.php';
require_once 'commons/tradelib.php';

if($user['license'] != 'yes')
{
  header('Location: /trading.php');
  break;
}

$tradeid = (int)$_GET['tradeid'];

if($tradeid < 1)
{
  header('Location: /trading.php');
  break;
}

$command = 'SELECT * FROM monster_trades WHERE tradeid=' . $tradeid . ' LIMIT 1';
$this_trade = $database->FetchSingle($command, 'fetching trade');

// if the users don't match
if($this_trade['userid2'] != $user['idnum'] && $this_trade['userid1'] != $user['idnum'])
{
  header('Location: /trading.php');
  exit();
}

// if the transaction is already complete, it cannot be cancelled
if($this_trade['step'] == 3 || $this_trade['step'] == 4)
{
  header('Location: /trading.php?msg=19');
  exit();
}


// get data about the other resident

if($user['idnum'] == $this_trade['userid1'])
{
  $user1 = $user;
  $user2 = get_user_byid($this_trade['userid2']);

  set_new_trade_flag($this_trade['userid2']);
}
else
{
  $user1 = get_user_byid($this_trade['userid1']);
  $user2 = $user;

  set_new_trade_flag($this_trade['userid1']);
}

// cancel the trade

if($this_trade['money1'] > 0)
{
  $command = 'UPDATE `monster_users` SET money=money+' . $this_trade['money1'] . ' WHERE `user`=' . quote_smart($user1['user']) . ' LIMIT 1';
  $database->FetchNone($command, 'giving ' . $user1['display'] . ' their money back');
}

if($this_trade['money2'] > 0)
{
  $command = 'UPDATE `monster_users` SET money=money+' . $this_trade['money2'] . ' WHERE `user`=' . quote_smart($user2['user']) . ' LIMIT 1';
  $database->FetchNone($command, 'giving ' . $user2['display'] . ' their money back');
}

if(strlen($this_trade['items1']) > 0)
{
  $command = "UPDATE monster_inventory SET `user`=" . quote_smart($user1['user']) . ",`location`='storage',`changed`=$now WHERE idnum IN (" . $this_trade['items1'] . ')';
  $database->FetchNone($command, 'giving ' . $user1['display'] . ' their items back');
}

if(strlen($this_trade['items2']) > 0)
{
  $command = "UPDATE monster_inventory SET `user`=" . quote_smart($user2['user']) . ",`location`='storage',`changed`=$now WHERE idnum IN (" . $this_trade['items2'] . ')';
  $database->FetchNone($command, 'giving ' . $user2['display'] . ' their items back');
}

$command = 'UPDATE monster_trades SET step=4, timestamp=' . $now . " WHERE tradeid=$tradeid LIMIT 1";
$database->FetchNone($command, 'setting trade as canceled');

consider_new_trade_flag($user['idnum']);

header('Location: /trading.php?msg=64');
?>
