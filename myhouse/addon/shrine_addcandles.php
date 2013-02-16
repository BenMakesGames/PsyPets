<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$whereat = 'home';
$wiki = 'Shrine';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/inventory.php';
require_once 'commons/checkpet.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/shrinelib.php';

if(!addon_exists($house, 'Shrine'))
{
  header('Location: /myhouse.php');
  exit();
}

$shrine = get_shrine_byuserid($user['idnum']);

if($shrine === false)
{
  header('Location: /myhouse/addon/shrine.php');
  exit();
}

$candles = take_apart(';', $shrine['candles']);

$itemid = (int)$_POST['candle'];
$slot = (int)$_POST['slot'];

if($slot < 0 || $slot > 9 || strlen($candles[$slot]) > 0)
{
  header('Location: /myhouse/addon/shrine.php?noslot');
  exit();
}

$details = get_item_byid($itemid);

if(!in_array($details['itemname'], $CANDLE_LIST))
{
  header('Location: /myhouse/addon/shrine.php?baditem');
  exit();
}

$deleted = delete_inventory_fromhome($user['user'], $details['itemname'], 1);

if($deleted == 0)
{
  header('Location: /myhouse/addon/shrine.php?noitems');
  exit();
}

$candles[$slot] = array_search($details['itemname'], $CANDLE_LIST) . ',' . ($slot + 15);

for($x = 0; $x < 10; ++$x)
{
  $candle_datas[$x] = $candles[$x];

  if(array_key_exists($x, $candles))
    $candle_count++;
}

if($candle_count == 1)
  $extra = ',lastcheck=' . $now;
else
  $extra = '';

$candle_data = implode(';', $candle_datas);

$command = 'UPDATE psypets_shrines SET candles=' . quote_smart($candle_data) . $extra . ' WHERE userid=' . $user['idnum'] . ' LIMIT 1';
fetch_none($command, 'saving candle data');

header('Location: /myhouse/addon/shrine_addcandles.php');
?>
