<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/houselib.php';

if($_GET['applyto'] == 'storage/incoming')
{
  $apply_to = 'storage/incoming';
  $url = 'incoming.php';
}
else if($_GET['applyto'] == 'storage/mystore')
{
  $apply_to = 'storage/mystore';
  $url = 'mystore.php';
}
else if($_GET['applyto'] == 'storage/locked')
{
  $apply_to = 'storage/locked';
  $url = 'storage_locked.php';
}
else if($_GET['applyto'] == 'storage')
{
  $apply_to = 'storage';
  $url = 'storage.php';
}
else if(substr($_GET['applyto'], 0, 4) == 'home')
{
  $apply_to = $_GET['applyto'];
  $url = 'myhouse.php';
}
else
{
  header('Location: ./autosort_edit.php');
  exit();
}

$rooms = take_apart(',', $house['rooms']);
foreach($rooms as $i=>$room)
  $real_rooms[$i] = 'home/' . $room;

$real_rooms[] = 'home';
$real_rooms[] = 'storage';
$real_rooms[] = 'storage/locked';
$real_rooms[] = 'storage/mystore';

$command = 'SELECT itemname,room FROM psypets_autosort WHERE userid=' . $user['idnum'] . ' AND room!=' . quote_smart($apply_to);
$rules = $database->FetchMultiple($command, 'fetching rules');

foreach($rules as $rule)
{
  if(in_array($rule['room'], $real_rooms))
  {
    $command = 'UPDATE monster_inventory SET location=' . quote_smart($rule['room']) . ',changed=' . $now . ',forsale=0 WHERE itemname=' . quote_smart($rule['itemname']) . ' AND user=' . quote_smart($user['user']) . ' AND location=' . quote_smart($apply_to) . ' AND location!=' . quote_smart($rule['room']);
    $database->FetchNone($command);
  
    $quantity = $database->AffectedRows();
  
    if($quantity > 0)
    {
      $total_items += $quantity;

      if($total_len < 2000)
      {
        $msg = '82:' . urlencode(link_safe($rule['itemname']) . ' &times;' . $quantity . ' to ' . $rule['room']);
        $msgs[] = $msg;
        $total_len += strlen($msg);
      }
    }
  }
}

if(count($msgs) == 0)
  $msgs[] = 137;
else if($total_len > 2000)
  $msgs = array('82:' . $total_items . ' items', 139);

recount_house_bulk($user, $house);

header('Location: ./' . $url . '?msg=' . implode(',', $msgs));
?>
