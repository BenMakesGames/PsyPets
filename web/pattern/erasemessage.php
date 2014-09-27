<?php
require_once 'commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/itemlib.php';
require_once 'commons/formatting.php';
require_once 'commons/mazelib.php';

if($user['show_pattern'] == 'no')
{
  header('Location: /myhouse.php');
  exit();
}

if($user['mazeloc'] == 0 || $user['mazemp'] == 0 || $user['childlockout'] == 'yes')
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

$id = (int)$_GET['id'];

$command = 'DELETE FROM psypets_maze_messages WHERE idnum=' . $id . ' AND mazeloc=' . $user['mazeloc'] . ' LIMIT 1';
fetch_none($command, 'erasing message');

if($database->AffectedRows() > 0)
  fetch_none('UPDATE monster_users SET mazemp=mazemp-1 WHERE idnum=' . $user['idnum'] . ' LIMIT 1');

header('Location: /pattern/');
?>
