<?php
$require_petload = 'no';
$invisible = 'yes';
$AJAX = true;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/utility.php';

$rooms = take_apart(',', $house['rooms']);
foreach($rooms as $i=>$room)
  $real_rooms[$i] = 'home/' . $room;

$real_rooms[] = 'home';
$real_rooms[] = 'storage';
$real_rooms[] = 'storage/locked';
$real_rooms[] = 'storage/mystore';

$itemname = trim($_POST['itemname']);
$target = trim($_POST['target']);

if(!in_array($target, $real_rooms))
{
  echo 'failure:room' . "\n";
  $failed = true;
}

$command = 'SELECT itemname FROM monster_items WHERE itemname=' . quote_smart($itemname) . ' LIMIT 1';
$item_data = $database->FetchSingle($command, 'fetching item');

if($item_data === false)
{
  echo 'failure:item' . "\n";
  $failed = true;
}
else
{
  $command = 'SELECT idnum FROM psypets_autosort WHERE userid=' . $user['idnum'] . ' AND itemname=' . quote_smart($item_data['itemname']) . ' LIMIT 1';
  $existing = $database->FetchSingle($command, 'fetching exiting rule');

  if($existing !== false)
  {
    echo 'hilight:' . $existing['idnum'] . "\n";
    $failed = true;
  }
}

if(!$failed)
{
  $command = 'INSERT INTO psypets_autosort (userid, itemname, room) VALUES (' . $user['idnum'] . ', ' . quote_smart($item_data['itemname']) . ', ' . quote_smart($target) . ')';
  $database->FetchNone($command, 'creating rule');

  echo 'addrule:' . $database->InsertID() . ';' . $item_data['itemname'] . ';' . $target;
}
?>
