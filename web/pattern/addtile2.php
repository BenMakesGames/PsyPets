<?php
require_once 'commons/init.php';

$wiki = 'The_Pattern';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/mazelib.php';

$myloc = get_maze_byid($user['mazeloc']);

$center_x = $myloc['x'];
$center_y = $myloc['y'];
$center_z = $myloc['z'];

if($_GET['dir'] == 'n')
  $center_y--;
else if($_GET['dir'] == 'w')
  $center_x--;
else if($_GET['dir'] == 'e')
  $center_x++;
else if($_GET['dir'] == 's')
  $center_y++;
else
{
  header('Location: /pattern/');
  exit();
}

$min_x = $center_x - 1;
$max_x = $center_x + 1;
$min_y = $center_y - 1;
$max_y = $center_y + 1;

$pieceid = (int)$_GET['piece'];

$tiles = $database->FetchMultiple('
  SELECT *
  FROM psypets_maze
  WHERE
    x BETWEEN ' . $min_x . ' AND ' . $max_x . '
    AND y BETWEEN ' . $min_y . ' AND ' . $max_y . '
    AND z=' . $center_z . '
  LIMIT 9
');

$maze = array();

for($i = 0; $i < 9; ++$i)
  $maze[$i] = false;

foreach($tiles as $tile)
{
  $i = ($tile['y'] - $min_y) * 3 + $tile['x'] - $min_x;
  $maze[$i] = $tile;
}

if($maze[1] === false && $maze[3] === false && $maze[5] === false && $maze[7] === false)
{
  header('Location: /pattern/');
  exit();
}

if($maze[4] !== false)
{
  header('Location: /pattern/?viewing=' . $maze[4]['idnum']);
  exit();
}

$piece = get_inventory_byid($pieceid, 'user,location,itemname');

if($piece['user'] != $user['user'] || $piece['location'] != 'storage')
{
  header('Location: /pattern/addtile.php?dir=' . $_GET['dir'] . '&nopiece');
  exit();
}

$piece = generate_maze_piece($piece['itemname']);

$piece_ok = ($piece !== false);

if($maze[1] !== false && $maze[1]['tile']{2} != $piece{0})
  $piece_ok = false;

if($maze[3] !== false && $maze[3]['tile']{1} != $piece{3})
  $piece_ok = false;

if($maze[5] !== false && $maze[5]['tile']{3} != $piece{1})
  $piece_ok = false;

if($maze[7] !== false && $maze[7]['tile']{0} != $piece{2})
  $piece_ok = false;

if($piece_ok)
{
  delete_inventory_byid($pieceid);

  $idnum = add_maze($center_x, $center_y, $center_z, $piece);

  require_once 'commons/questlib.php';

  $mazetiles = get_quest_value($user['idnum'], 'maze tiles');
  $mazetiles_count = (int)$mazetiles['value'] + 1;

  if($mazetiles === false)
    add_quest_value($user['idnum'], 'maze tiles', $mazetiles_count);
  else
    update_quest_value($mazetiles['idnum'], $mazetiles_count);

  $badges = get_badges_byuserid($user['idnum']);
  if($badges['maze-1'] == 'no' && $mazetiles_count >= 1)
  {
    set_badge($user['idnum'], 'maze-1');

    $body = 'Congratulations on placing your first Maze Piece!<br /><br />' .
            'You saw the item depicted on new tile you placed down, right?  In order to move on to that space, you need to have that item in your Storage.  The item will be taken from you, but you\'ll get something in exchange.  There are some very valuable items to be found in The Pattern, including the {i Wand of Wonder}, and the {i Patently-Rare And Valuable Treasure}!<br /><br />' .
            'If you\'re still not convinced, consider this badge:<br /><br />' .
            '{i}(You earned the Maze Builder Badge!){/}<br /><br />' .
            'The first of many!  (Well, a few...)';

    psymail_user($user['user'], 'thaddeus', 'You placed your first Maze Piece!', $body);
  }
  else if($badges['maze-2'] == 'no' && $mazetiles_count >= 10)
  {
    set_badge($user['idnum'], 'maze-2');

    $body = 'Having placed 10 Maze Pieces, you must\'ve come to appreciate their value!<br /><br />' .
            'I doubt you need this badge to convince you to keep with it, but all the same...<br /><br />' .
            '{i}(You earned the Labyrinth Architect Badge!){/}';

    psymail_user($user['user'], 'thaddeus', 'You placed your 10th Maze Piece!', $body);
  }
  else if($badges['maze-final'] == 'no' && $mazetiles_count >= 100)
  {
    set_badge($user['idnum'], 'maze-final');

    $body = '100.  100 Maze Pieces.  Never thought I\'d see the day...<br /><br />' .
            'Well, you\'ve been incredibly helpful!  The least I can do is give you this badge:<br /><br />' .
            '{i}(You earned the Goblin King Badge!){/}<br /><br />' .
            'Hm?  How was it helpful?  Ah, never mind all that.  Enjoy the badge!  It\'s the last and best I have to offer you for your contributions to The Pattern!<br /><br />' .
            'Thanks again!';

    psymail_user($user['user'], 'thaddeus', 'You placed your 100th Maze Piece!', $body);
  }

  header('Location: /pattern/?view=' . $idnum);
  exit();
}
else
{
  header('Location: /pattern/addtile.php?dir=' . $_GET['dir']);
  exit();
}
?>
