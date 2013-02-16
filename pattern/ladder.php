<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/itemlib.php';
require_once 'commons/mazelib.php';

if($user['show_pattern'] == 'no')
{
  header('Location: /myhouse.php');
  exit();
}

$this_tile = get_maze_byid($user['mazeloc']);

if($this_tile === false)
{
  echo "Uh oh:  You seem to be located somewhere that doesn't exist in the maze.  If this keeps happening, you should probably contact <a href=\"/admincontact.php\">an administrator</a>.<br />\n";
  exit();
}

$x = $this_tile['x'];
$y = $this_tile['y'];
$z = $this_tile['z'];

if($this_tile['feature'] == 'none' && $this_tile['obstacle'] == 'none' && ($_GET['dir'] == 'u' || $_GET['dir'] == 'd'))
{
  if($_GET['dir'] == 'u')
  {
    $tile_up = get_maze_bycoord($x, $y, $z - 1);

    if($tile_up === false)
    {
      if(delete_inventory_byname($user['user'], 'Magic Ladder', 1, 'Storage') > 0)
      {
        maze_add_ladder_up($this_tile['idnum']);

        $tile_id = maze_create_tile_with_ladder_down($x, $y, $z - 1);

        maze_move_user($user, $tile_id);
      }
    }
    else if($tile_up['feature'] == 'none' && $tile_up['obstacle'] == 'none')
    {
      if(delete_inventory_byname($user['user'], 'Magic Ladder', 1, 'Storage') > 0)
      {
        maze_add_ladder_up($this_tile['idnum']);
        maze_add_ladder_down($tile_up['idnum']);

        maze_move_user($user, $tile_up['idnum']);
      }
    }
  }
  else if($_GET['dir'] == 'd')
  {
    $tile_down = get_maze_bycoord($x, $y, $z + 1);

    if($tile_down === false)
    {
      if(delete_inventory_byname($user['user'], 'Magic Ladder', 1, 'Storage') > 0)
      {
        maze_add_ladder_down($this_tile['idnum']);

        $tile_id = maze_create_tile_with_ladder_up($x, $y, $z + 1);

        maze_move_user($user, $tile_id);
      }
    }
    else if($tile_down['feature'] == 'none' && $tile_down['obstacle'] == 'none')
    {
      if(delete_inventory_byname($user['user'], 'Magic Ladder', 1, 'Storage') > 0)
      {
        maze_add_ladder_down($this_tile['idnum']);
        maze_add_ladder_up($tile_down['idnum']);

        maze_move_user($user, $tile_down['idnum']);
      }
    }
  }
}

header('Location: /pattern/');
?>