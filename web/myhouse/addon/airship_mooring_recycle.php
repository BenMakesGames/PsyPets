<?php
require_once 'commons/init.php';

$whereat = 'home';
$wiki = 'Airship Mooring';
$THIS_ROOM = 'Airship Mooring';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/checkpet.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/blimplib.php';

if($NO_PVP)
{
  header('Location: /lostdata.php');
  exit();
}

if(!addon_exists($house, 'Airship Mooring'))
{
  header('Location: /myhouse.php');
  exit();
}

$shipid = (int)$_GET['idnum'];
$airship = get_airship_by_id($shipid);

if($airship === false || $airship['ownerid'] != $user['idnum'])
{
  header('Location: /myhouse/addon/airship_mooring.php');
  exit();
}

$parts = take_apart(',', $airship['parts']);
$parts[] = $airship['chassis'];

$command = 'DELETE FROM psypets_airships WHERE idnum=' . $shipid . ' LIMIT 1';
fetch_none($command, 'deleting airship');

foreach($parts as $itemname)
  add_inventory($user['user'], '', $itemname, 'Recovered from ' . $airship['name'], 'home');

header('Location: /myhouse/addon/airship_mooring.php');
?>
