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
require_once 'commons/tradelib.php';

if($user['license'] != 'yes')
{
  header('Location: /trading.php?noltc');
  exit();
}

$tradeid = (int)$_GET['tradeid'];

$command = 'SELECT * FROM `monster_trades` WHERE tradeid=' . $tradeid . ' LIMIT 1';
$this_trade = $database->FetchSingle($command, 'fetching trade');

// you have to be the user that initiated the trade to accept it
if($this_trade === false)
{
  header('Location: /trading.php?notrade');
  exit();
}

// you may accept only if you're the initiater and it's not a gift, or you're the recipient and it IS a gift
if(
  ($this_trade['userid1'] == $user['idnum'] && $this_trade['gift'] == 'no') ||
  ($this_trade['userid2'] == $user['idnum'] && $this_trade['gift'] == 'yes')
)
  ;
else
{
  header('Location: /trading.php?notrade');
  exit();
}

// have to be waiting to accept the trade to accept it :)
if($this_trade['step'] == 2 || ($this_trade['step'] == 1 && $this_trade['gift'] == 'yes'))
  ;
else
{
  header('Location: /trading.php?wrongstep');
  exit();
}

$target = get_user_byid($this_trade['userid2']);

$user1_items = take_apart(',', $this_trade['items2']);
$user2_items = take_apart(',', $this_trade['items1']);

// step 3 is a successful transaction
$database->FetchNone('
  UPDATE monster_trades
  SET
    step=3,
    timestamp=' . time() . '
  WHERE tradeid=' . $tradeid . '
  LIMIT 1
');

if($this_trade['money2'] > 0)
{
  $command = 'UPDATE `monster_users` SET money=money+' . $this_trade["money2"] . " WHERE `user`=" . quote_smart($user['user']) . " LIMIT 1";
  $database->FetchNone($command);
}

if($this_trade['money1'] > 0)
{
  $command = 'UPDATE `monster_users` SET money=money+' . $this_trade["money1"] . " WHERE `user`=" . quote_smart($target['user']) . ' LIMIT 1';
  $database->FetchNone($command);
}

if(strlen($this_trade['items2']) > 0)
{
  $command = 'UPDATE monster_inventory SET `user`=' . quote_smart($user['user']) . ',`message2`=' . quote_smart('You traded with ' . $target['display'] . ' for this item.') . ",`location`='storage/incoming',`changed`=$now WHERE idnum IN (" . $this_trade['items2'] . ')';
  $database->FetchNone($command);

  flag_new_incoming_items($user['user']);
}

if(strlen($this_trade['items1']) > 0)
{
  if($this_trade['anonymous'] == 'yes')
    $name = 'someone mysterious';
  else
    $name = $user['display'];

  $command = 'UPDATE monster_inventory SET `user`=' . quote_smart($target['user']) . ',`message2`=' . quote_smart('You traded with ' . $name . ' for this item.') . ",`location`='storage/incoming',`changed`=$now WHERE idnum IN (" . $this_trade['items1'] . ')';
  $database->FetchNone($command);

  flag_new_incoming_items($target['user']);
}

set_new_trade_flag($target['idnum']);
consider_new_trade_flag($user['idnum']);

header('Location: /trading.php?msg=26');
?>
