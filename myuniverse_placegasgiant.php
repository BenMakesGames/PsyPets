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

if($universe === false || $universe['stage'] != 'gameplay' || $universe['gasgiants'] < 1)
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
$y = (int)$_GET['y'];

if($x < 16 || $x >= 600 - 16 || $y < 16 || $y >= 300 - 16)
{
  header('Location: ./universe_viewsystem.php?id=' . $systemid . '&msg=143');
  exit();
}

if(!solar_system_space_is_empty($systemid, $x, $y, 16))
{
  header('Location: ./universe_viewsystem.php?id=' . $systemid . '&msg=143');
  exit();
}

$size = mt_rand(1, 5);

universe_spend($universe, 'gasgiants', 1);
create_gas_giant($universe['idnum'], $solar_system, $x, $y, $size);

header('Location: ./universe_viewsystem.php?id=' . $systemid);
?>
