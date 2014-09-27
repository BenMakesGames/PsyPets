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
require_once 'commons/messages.php';

if($user['show_universe'] != 'yes')
{
  header('Location: ./myhouse.php');
  exit();
}

$CONTENT_STYLE = 'background-color: #000;';
$CONTENT_CLASS = 'universe';

$UNIVERSE_MESSAGES = array();

$object_id = (int)$_GET['galaxy'];

$universe = get_universe($user['idnum']);

if($universe === false || $universe['stage'] != 'gameplay')
{
  header('Location: ./myuniverse.php');
  exit();
}

$galaxy = get_galaxy($object_id);

if($galaxy['universeid'] != $universe['idnum'])
{
  header('Location: ./myuniverse.php');
  exit();
}

if($universe['stars'] < 1)
{
  header('Location: ./universe_viewgalaxy.php?id=' . $object_id);
  exit();
}

$x = (int)$_GET['x'];
$y = (int)$_GET['y'];

if(!universe_galaxy_space_is_empty($object_id, $x, $y))
{
  header('Location: ./universe_viewgalaxy.php?id=' . $object_id . '&msg=141');
  exit();
}

universe_spend($universe, 'stars', 1);
create_solar_system($universe['idnum'], $object_id, $x, $y);

header('Location: ./universe_viewgalaxy.php?id=' . $object_id);
?>
