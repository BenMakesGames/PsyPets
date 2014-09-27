<?php
require_once 'commons/init.php';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
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
  echo "Uh oh:  You seem to be located somewhere that doesn't exist in the pattern!  If this keeps happening, you should probably <a href=\"admincontact.php\">contact " . $SETTINGS['author_resident_name'] . "</a>.<br />\n";
  exit();
}

$x = $this_tile['x'];
$y = $this_tile['y'];
$z = $this_tile['z'];

$new_x = $x;
$new_y = $y;
$new_z = $z;

$dir = strtoupper(substr($_GET['dir'], 0, 1));

if($dir == 'N')
  $new_y--;
else if($dir == 'W')
  $new_x--;
else if($dir == 'E')
  $new_x++;
else if($dir == 'S')
  $new_y++;
else if($dir == 'U')
  $new_z--;
else if($dir == 'D')
  $new_z++;
else
{
  header('Location: /pattern/');
  exit();
}

$target_tile = get_maze_bycoord($new_x, $new_y, $new_z);

$OK = true;

if($target_tile === false)
  $OK = false;
else if($x != $new_x)
{
  if($x + 1 == $new_x)
  {
    if($target_tile['tile']{3} == 1)
      $OK = false;
  }
  else if($x - 1 == $new_x)
  {
    if($target_tile['tile']{1} == 1)
      $OK = false;
  }
  else
    $OK = false;
}
else if($y != $new_y)
{
  if($y + 1 == $new_y)
  {
    if($target_tile['tile']{0} == 1)
      $OK = false;
  }
  else if($y - 1 == $new_y)
  {
    if($target_tile['tile']{2} == 1)
      $OK = false;
  }
  else
    $OK = false;
}
else if($z != $new_z)
{
  if($z + 1 == $new_z)
  {
    if($this_tile['feature'] != 'ladder_down')
      $OK = false;
  }
  else if($z - 1 == $new_z)
  {
    if($this_tile['feature'] != 'ladder_up')
      $OK = false;
  }
  else
    $OK = false;
}
else
  $OK = false;

if(!$OK)
{
  header('Location: /pattern/?badspace');
  exit();
}

$pass = false;

if($target_tile['obstacle'] == 'none')
  $pass = true;
else
{
  $deleted = delete_inventory_byname($user["user"], $target_tile['obstacle'], 1, $user['pattern_item_room']);
  if($deleted > 0)
  {
    add_inventory($user['user'], '', $target_tile['treasure'], 'Found in The Pattern', $user['incomingto']);
    maze_clear_tile($target_tile['idnum']);
    $pass = true;

    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Cleared an Obstacle in The Pattern', 1);
  }
}

if($pass)
{
  maze_move_user($user, $target_tile['idnum']);

  $move_history = $user['pattern_movement_history'] . $dir;
  
  if(strlen($move_history) > 6)
    $move_history = substr($move_history, -6, 6);
  
  award_maze_movement_sequence($user, $move_history);
  
  fetch_none('
    UPDATE monster_users
    SET
      mazemp=mazemp-1,
      pattern_movement_history=' . quote_smart($move_history) . '
    WHERE idnum=' . $user['idnum'] . '
    LIMIT 1
  ');

  require_once 'commons/statlib.php';
  record_stat($user['idnum'], 'Spent a Movement Point in The Pattern', 1);

  if($deleted > 0)
    header('Location: /pattern/?msg=57:' . $target_tile['treasure']);
  else
    header('Location: /pattern/');
}
else
  header('Location: /pattern/?msg=58:' . link_safe($target_tile['obstacle']));

