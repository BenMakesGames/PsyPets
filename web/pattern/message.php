<?php
require_once 'commons/init.php';

$wiki = 'The_Pattern';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/itemlib.php';
require_once 'commons/formatting.php';
require_once 'commons/mazelib.php';
require_once 'commons/statlib.php';
require_once 'commons/userlib.php';

if($user['show_pattern'] == 'no')
{
  header('Location: /myhouse.php');
  exit();
}

if($user['mazeloc'] == 0)
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

$message = trim($_POST['message']);

if(strlen($message) == 0)
{
  header('Location: /pattern/?msg=38');
  exit();
}

if(delete_inventory_fromstorage($user['user'], 'Chalk', 1) != 1)
{
  header('Location: /pattern/?msg=70');
  exit();
}

$command = 'INSERT INTO psypets_maze_messages (mazeloc, authorid, message) VALUES ' .
  '(' . $user['mazeloc'] . ', ' . $user['idnum'] . ', ' . quote_smart($message) . ')';
fetch_none($command, 'writing message');

if(record_stat_with_badge($user['idnum'], 'Messages Written In The Pattern', 1, 1, 'graffiti'))
  header('Location: /pattern/?msg=90:Chalk%20Graffiti');
else
  header('Location: /pattern/');
?>
