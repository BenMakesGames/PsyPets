<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/houselib.php';

$THIS_ROOM = 'Rearrange';

$locid = $user['locid'];

$house = get_house_byuser($user['idnum'], $locid);

$rooms = take_apart(',', $house['rooms']);
$walls = take_apart(',', $house['wallpapers']);

$list = take_apart('|', $_GET['list']);

$newwalls[] = $walls[0];

$i = 0;
$last_room = $house['max_rooms_shown'];

foreach($list as $room)
{
  if($room == 'separator')
  {
    $last_room = $i;
    continue;
  }

  $index = substr($room, 1);
  
  $newrooms[] = $rooms[$index];
  $newwalls[] = $walls[$index + 1];
  unset($rooms[$index]);
  
  $i++;
}

if(count($rooms) > 0)
{
  header('Location: ./arrangerooms.php');
  exit();
}

if($last_room == count($newrooms))
  $last_room = 255;

$q_rooms = quote_smart(implode(',', $newrooms));
$q_walls = quote_smart(implode(',', $newwalls));

$command = 'UPDATE monster_houses SET rooms=' . $q_rooms . ',wallpapers=' . $q_walls . ',max_rooms_shown=' . $last_room . ' WHERE idnum=' . $house['idnum'] . ' LIMIT 1';
$database->FetchNone($command, 'rearranging house rooms');

header('Location: ./arrangerooms.php?msg=93');
?>
