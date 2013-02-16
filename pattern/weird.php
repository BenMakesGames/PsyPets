<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

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

if($this_tile['feature'] != 'weird')
{
  header('Location: /pattern/');
  exit();
}

$oldest = time() - (60 * 60 * 24 * 30);

$command = 'SELECT mazeloc FROM monster_users WHERE mazeloc>0 AND idnum!=' . $user['idnum'] . ' AND lastactivity>' . $oldest . ' ORDER BY lastactivity ASC LIMIT 1';
$data = fetch_single($command, 'fetching oldest maze player, within one month');

if($data === false)
{
  header('Location: /pattern/?msg=136');
  exit();
}

maze_move_user($user, $data['mazeloc']);

$command = 'UPDATE monster_users SET mazemp=mazemp-1 WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
fetch_none($command, 'themaze_travelto.php');

header('Location: /pattern/');
?>
