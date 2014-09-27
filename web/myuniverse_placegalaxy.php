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

$CONTENT_STYLE = 'background-color: #000;';
$CONTENT_CLASS = 'universe';

$UNIVERSE_MESSAGES = array();

$universe = get_universe($user['idnum']);

if($universe === false || $universe['stage'] != 'gameplay' || $universe['galaxies'] < 1)
{
  header('Location: ./myuniverse.php');
  exit();
}

$x = (int)$_GET['x'];
$y = (int)$_GET['y'];

if($x < 24 || $x >= 600 - 24 || $y < 24 || $y >= 600 - 24)
{
  header('Location: ./myuniverse.php');
  exit();
}

if(!universe_space_is_empty($universe['idnum'], $x, $y))
{
  header('Location: ./myuniverse.php?msg=140');
  exit();
}

universe_spend($universe, 'galaxies', 1);
universe_place_spiral_galaxy($universe['idnum'], $x, $y);

header('Location: ./myuniverse.php');
exit();
?>
