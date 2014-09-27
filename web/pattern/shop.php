<?php
require_once 'commons/init.php';

$whereat = 'maze';
$wiki = 'The_Pattern';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/mazelib.php';

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

$exchange = (int)$_POST['exchange'];

if($this_tile['feature'] != 'shop')
{
  header('Location: /pattern/');
  exit();
}

if($exchange == 1 && $user['mazemp'] <= 75)
{
  $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE itemname=\'Pyrium\' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\'';
  $pyrium_data = fetch_single($command, 'fetching Pyrium count');
  
  $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE itemname=\'Chalk\' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\'';
  $chalk_data = fetch_single($command, 'fetching Chalk count');

  if($pyrium_data['c'] >= 3 && $chalk_data['c'] >= 3)
  {
    if(delete_inventory_fromstorage($user['user'], 'Pyrium', 3) == 3
      && delete_inventory_fromstorage($user['user'], 'Chalk', 3) == 3)
    {
      $command = 'UPDATE monster_users SET mazemp=mazemp+25 WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
      fetch_none($command, 'giving 25 MP');
      
      header('Location: /pattern/?msg=93');
    }
    else
      header('Location: /pattern/?msg=70');
  }
  else
    header('Location: /pattern/?msg=70');
}
else if($exchange == 2)
{
  $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE itemname=\'Red Dye\' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching Red Dye count');

  if($data['c'] >= 12)
  {
    if(delete_inventory_fromstorage($user['user'], 'Red Dye', 12) == 12)
    {
      $command = 'UPDATE monster_users SET title=\'Bull of Minos\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
      fetch_none($command, 'giving new title');

      header('Location: /pattern/?msg=93');
    }
    else
      header('Location: /pattern/?msg=70');
  }
  else
    header('Location: /pattern/?msg=70');
}
else if($exchange == 3)
{
  $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE itemname=\'Celery and Peanut Butter\' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching Celery and Peanut Butter count');

  if($data['c'] >= 4)
  {
    if(delete_inventory_fromstorage($user['user'], 'Celery and Peanut Butter', 4) == 4)
    {
      $command = 'SELECT idnum FROM psypets_maze WHERE feature=\'shop\' AND idnum!=' . $user['mazeloc'] . ' ORDER BY RAND() LIMIT 1';
      $target = fetch_single($command, 'fetching shop');

			maze_move_user($user, $target['idnum']);

      header('Location: /pattern/?msg=93');
    }
    else
      header('Location: /pattern/?msg=70');
  }
  else
    header('Location: /pattern/?msg=70');
}
else if($exchange == 4)
{
  $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE itemname=\'Coal\' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\'';
  $coal_data = fetch_single($command, 'fetching Coal count');

  $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE itemname=\'Small Giamond\' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\'';
  $sg_data = fetch_single($command, 'fetching Small Giamond count');

  $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE itemname=\'Silver\' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\'';
  $silver_data = fetch_single($command, 'fetching Silver count');

  if($coal_data['c'] >= 50 && $sg_data['c'] >= 50 && $silver_data['c'] >= 50)
  {
    if(delete_inventory_fromstorage($user['user'], 'Coal', 50) == 50
      && delete_inventory_fromstorage($user['user'], 'Small Giamond', 50) == 50
      && delete_inventory_fromstorage($user['user'], 'Silver', 50) == 50)
    {
      add_inventory($user['user'], '', 'Laoc\'s Spiritstaff', $user['display'] . ' traded in The Pattern for this item', $user['incomingto']);

      header('Location: /pattern/?msg=135');
    }
    else
      header('Location: /pattern/?msg=70');
  }
  else
    header('Location: /pattern/?msg=70');
}
else if($exchange == 5)
{
  $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE itemname=\'Paper\' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\'';
  $paper_data = fetch_single($command, 'fetching Paper count');

  $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE itemname=\'Black Dye\' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\'';
  $bd_data = fetch_single($command, 'fetching Black Dye count');

  if($paper_data['c'] >= 20 && $bd_data['c'] >= 20)
  {
    if(delete_inventory_fromstorage($user['user'], 'Paper', 20) == 20
      && delete_inventory_fromstorage($user['user'], 'Black Dye', 20) == 20)
    {
      add_inventory($user['user'], '', 'Book of Creatures I', $user['display'] . ' traded in The Pattern for this item', $user['incomingto']);

      header('Location: /pattern/?msg=135');
    }
    else
      header('Location: /pattern/?msg=70');
  }
  else
    header('Location: /pattern/?msg=70');
}
else
  header('Location: /pattern/');
?>