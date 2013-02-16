<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/formatting.php';
require_once 'commons/houselib.php';

$locid = $user['locid'];

$house = get_house_byuser($user['idnum'], $locid);

if($house === false)
{
  echo '<p>Failed to load your house.  If this problem continues, please contact <a href="admincontact.php">an administrator</a>.</p>';
  exit();
}

if(strlen($_GET['room']) > 0)
{
  $room = $_GET['room'];
  
  if($room == 'Common')
    $room = '';
  else if($room == 'Protected')
    $room = 'Protected';
  else if(strlen($house['rooms']) > 0)
  {
    $rooms = explode(',', $house['rooms']);

    if(array_search($room, $rooms) === false)
      $room = $house['curroom'];
  }

  $command = 'UPDATE monster_houses SET curroom=' . quote_smart($room) . ' WHERE idnum=' . $house['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating current room');
}
else if(strlen($_GET['sortby']) > 0)
{
  if($_GET['sortby'] == 'idnum' || $_GET['sortby'] == 'bulk' || $_GET['sortby'] == 'itemname' || $_GET['sortby'] == 'itemtype' || $_GET['sortby'] == 'ediblefood' || $_GET['sortby'] == 'message')
  {
    $house['sort'] = $_GET['sortby'];
   
    $command = 'UPDATE monster_houses SET sort=' . quote_smart($house['sort']) . ' WHERE idnum=' . $house['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'updating house sort');
  }
}
else if($_GET['viewby'] == 'details' || $_GET['viewby'] == 'icons')
{
  $house['view'] = $_GET['viewby'];

  $command = 'UPDATE monster_houses SET view=' . quote_smart($house['view']) . ' WHERE idnum=' . $house['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating house view');
}

header('Location: /myhouse.php');
exit();
?>
