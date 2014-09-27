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

$system = get_solar_system($object_id);

if($system['universeid'] != $universe['idnum'])
{
  header('Location: ./myuniverse.php');
  exit();
}

$newname = trim($_POST['name']);

if($newname != $system['name'] && strlen($newname) > 0)
{
  $command = '
    UPDATE psypets_stellar_objects
    SET name=' . quote_smart($newname) . '
    WHERE idnum=' . $object_id . '
    LIMIT 1
  ';
  $database->FetchNone($command, 'renaming system');

  log_universe_event($universe['idnum'], 'The ' . $system['name'] . ' System has been renamed.  It is now the ' . $newname . ' System.');
}

header('Location: ./universe_viewsystem.php?id=' . $object_id);
?>
