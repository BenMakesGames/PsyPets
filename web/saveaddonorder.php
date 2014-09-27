<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/houselib.php';

$locid = $user['locid'];

$house = get_house_byuser($user['idnum'], $locid);

$addons = take_apart(',', $house['addons']);

$list = take_apart('|', $_GET['list']);

$i = 0;
$last_addon = $house['max_addons_shown'];

foreach($list as $room)
{
  if($room == 'separator')
  {
    $last_addon = $i;
    continue;
  }

  $index = substr($room, 1);
  $newaddons[] = $addons[$index];
  unset($addons[$index]);

  $i++;
}

if(count($addons) > 0)
{
  header('Location: ./arrangeaddons.php');
  exit();
}

if($last_addon == count($newaddons))
  $last_addon = 255;

$q_addons = quote_smart(implode(',', $newaddons));

$command = 'UPDATE monster_houses SET addons=' . $q_addons . ',max_addons_shown=' . $last_addon . ' WHERE idnum=' . $house['idnum'] . ' LIMIT 1';
$database->FetchNone($command, 'rearranging house addons');

header('Location: ./arrangeaddons.php?msg=93');
?>
