<?php
$require_petload = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/fireworklib.php';
require_once 'commons/threadfunc.php';

if($user['fireworks'] == '')
{
  header('Location: ./myhouse.php');
  exit();
}

$room = $_GET['room'];
$fireworkid = (int)$_GET['firework'];

$supply = get_firework_supply($user);
$rooms = explode(',', $house['rooms']);
$walls = explode(',', $house['wallpapers']);

$room_i = array_search($room, $rooms);

if(array_key_exists($fireworkid, $supply) && ($room_i !== false || $room == ''))
{
  expend_firework($supply, $fireworkid);

  $command = 'UPDATE monster_users SET fireworks=' . quote_smart(render_firework_data_string($supply)) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'removing firework from player');

  if($room == '')
    $walls[0] = $fireworkid;
  else
    $walls[$room_i + 1] = $fireworkid;

  $command = 'UPDATE monster_houses SET wallpapers=\'' . implode(',', $walls) . '\' WHERE userid=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'saving house wallpapers');
}

header('Location: ./myhouse.php');
?>
