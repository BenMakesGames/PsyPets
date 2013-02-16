<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';

if($user['show_pattern'] == 'no')
{
  header('Location: /myhouse.php');
  exit();
}

$rooms['storage'] = 'Storage';
$rooms['home'] = 'Common';

if(strlen($house['rooms']) > 0)
{
  $m_rooms = explode(',', $house['rooms']);
  foreach($m_rooms as $room)
    $rooms['home/' . $room] = $room;
}

if(array_key_exists($_POST['room'], $rooms))
{
  fetch_none('
    UPDATE monster_users
    SET pattern_item_room=' . quote_smart($_POST['room']) . '
    WHERE idnum=' . $user['idnum'] . '
    LIMIT 1
  ');
  
  if($database->AffectedRows() > 0)
    $user['pattern_item_room'] = $_POST['room'];
}

echo json_encode(array('room' => $user['pattern_item_room']));
?>