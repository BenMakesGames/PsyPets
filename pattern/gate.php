<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/mazelib.php';

if($user['show_pattern'] == 'no')
{
  header('Location: /myhouse.php');
  exit();
}

if($user['mazeloc'] == 0 || $user['mazemp'] == 0)
{
  header('Location: /pattern/');
  exit();
}

$this_tile = get_maze_byid($user['mazeloc']);

if($this_tile === false)
{
  echo "Uh oh:  You seem to be located somewhere that doesn't exist in the maze.  If this keeps happening, you should probably contact <a href=\"admincontact.php\">an administrator</a>.<br />\n";
  exit();
}

if($this_tile['feature'] != 'gate')
{
  header('Location: /pattern/');
  exit();
}

$gateid = (int)$_GET['gate'];

$command = 'SELECT x,y,z FROM psypets_maze_gates WHERE idnum=' . $gateid . ' LIMIT 1';
$gate = fetch_single($command, 'fetching gate');

if($gate === false)
{
  header('Location: /pattern/');
  exit();
}

$command = 'SELECT idnum FROM psypets_maze WHERE x=' . $gate['x'] . ' AND y=' . $gate['y'] . ' AND z=' . $gate['z'] . ' LIMIT 1';
$target_tile = fetch_single($command, 'fetching gate target');

if($target_tile === false)
{
  echo "Uh oh:  The database entry for the gate is broken!  You should probably contact <a href=\"admincontact.php\">an administrator</a>.<br />\n";
  exit();
}

maze_move_user($user, $target_tile['idnum']);

fetch_none('
  UPDATE monster_users
  SET mazemp=mazemp-1
  WHERE idnum=' . $user['idnum'] . '
  LIMIT 1
');

header('Location: /pattern/');
?>