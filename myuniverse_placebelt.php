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

$systemid = (int)$_GET['system'];

$universe = get_universe($user['idnum']);

if($universe === false || $universe['stage'] != 'gameplay' || $universe['rocks'] < 20)
{
  header('Location: ./myuniverse.php');
  exit();
}

$solar_system = get_solar_system($systemid);

if($solar_system === false)
{
  header('Location: ./myuniverse.php?systemid=' . $systemid);
  exit();
}

$galaxy = get_galaxy($solar_system['galaxyid']);

if($galaxy === false || $galaxy['universeid'] != $universe['idnum'])
{
  header('Location: ./universe_viewsystem.php');
  exit();
}

$x = (int)$_GET['x'];

if($x < 16 || $x >= 600 - 16)
{
  header('Location: ./universe_viewsystem.php?id=' . $systemid . '&msg=143');
  exit();
}

if(!solar_system_belt_is_clear($systemid, $x))
{
  header('Location: ./universe_viewsystem.php?id=' . $systemid . '&msg=143');
  exit();
}

universe_spend($universe, 'rocks', 20);
create_asteroid_belt($universe['idnum'], $solar_system, $x);

header('Location: ./universe_viewsystem.php?id=' . $systemid);
?>
