<?php
$whereat = 'home';
$wiki = 'Multiverse';
$THIS_ROOM = 'Multiverse';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/universelib.php';

if($user['show_universe'] != 'yes')
{
  header('Location: ./myhouse.php');
  exit();
}

$object_id = (int)$_GET['id'];

$universe = get_universe($user['idnum']);

if($universe === false || $universe['stage'] != 'gameplay')
{
  header('Location: ./myuniverse.php');
  exit();
}

$planet = get_solar_system_object($object_id);

if($planet['universeid'] != $universe['idnum'])
{
  header('Location: ./myuniverse.php');
  exit();
}

$object_type = ucwords(planet_type($planet));

$newname = trim($_POST['name']);

if($newname != $planet['name'] && strlen($newname) > 0)
{
  $command = '
    UPDATE psypets_planets
    SET name=' . quote_smart($newname) . '
    WHERE idnum=' . $object_id . '
    LIMIT 1
  ';
  $database->FetchNone($command, 'renaming planet');

  log_universe_event($universe['idnum'], 'The ' . $planet['name'] . ' ' . $object_type . ' has been renamed.  It is now the ' . $newname . ' ' . $object_type . '.');
}

header('Location: ./universe_viewobject.php?id=' . $object_id);
?>
