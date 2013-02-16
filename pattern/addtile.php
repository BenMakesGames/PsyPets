<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

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

$mytilelist = fetch_multiple('
	SELECT a.idnum,a.itemname,b.graphic,b.graphictype,COUNT(a.idnum) AS qty
	FROM
		monster_inventory AS a,
		monster_items AS b
	WHERE
		a.itemname=b.itemname AND
		a.user=' . quote_smart($user['user']) . '
		AND a.location=\'storage\'
		AND b.itemtype=\'toy/card/maze\'
		AND b.custom=\'no\'
	GROUP BY a.itemname
	ORDER BY a.itemname ASC
');

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Pattern &gt; Add Tile</title>
<?php include 'commons/head.php'; ?>
  <style type="text/css">
   #themaze
   {
     float: right;
     padding: 0;
     margin: 0 0 1em 1em;
   }

   #themaze td
   {
     width: 64px;
     height: 64px;
     padding: 0;
     margin: 0;
     border: 0;
   }
  </style>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? '<p style="color:blue;">' . $check_message . '</p>' : '') ?>
     <h4><a href="/pattern/">The Pattern</a> &gt; Add Tile</h4>
     <div id="themaze">
     <table>
      <tr>
<?php
foreach($maze as $index=>$tile)
{
  if($index % 3 == 0 && $index > 0)
    echo "</tr>\n<tr>\n";

  if($tile === false)
  {
    $x = $index % 3 + $min_x;
    $y = (int)($index / 3) + $min_y;
    
    if($x == $center_x && $y == $center_y)
      $background = 'selected.gif';
    else
      $background = 'none.png';

    echo '<td style="background: url(/gfx/maze/' . $background . ');"></td>';
  }
  else
  {
    $background = $tile['tile'];

    echo '<td style="background: url(/gfx/maze/' . $background . '.png);" align="center" valign="center">';

    if($user['mazeloc'] == $tile['idnum'])
      echo '<img src="/gfx/emote/hee.gif" width="16" height="16" alt="You are here" />';
    else if($tile['obstacle'] != 'none')
    {
      $obstacle_item = get_item_byname($tile['obstacle']);

      echo item_display($obstacle_item, '');
    }

    if($tile['feature'] == 'gate')
      echo '<img src="/gfx/maze/gate.png" alt="Gate" title="Gate" />';

    echo '</td>';
  }
}
?>
      </tr>
     </table>
     </div>
     <p>Only Maze Pieces in your Storage are listed here.</p>
<?php
if(count($mytilelist) > 0)
{
  $rowclass = begin_row_class();

  echo '<table>' .
       '<tr class="titlerow"><th></th><th></th><th>Piece</th><th>Qty.</th></tr>';

  foreach($mytilelist as $mytile)
  {
    $piece = generate_maze_piece($mytile['itemname']);

    $piece_ok = ($piece !== false);

    if($maze[1] !== false && $maze[1]['tile']{2} != $piece{0})
      $piece_ok = false;

    if($maze[3] !== false && $maze[3]['tile']{1} != $piece{3})
      $piece_ok = false;

    if($maze[5] !== false && $maze[5]['tile']{3} != $piece{1})
      $piece_ok = false;

    if($maze[7] !== false && $maze[7]['tile']{0} != $piece{2})
      $piece_ok = false;
?>
 <tr class="<?= $rowclass ?>">
  <td><?= $piece_ok ? '<a href="/pattern/addtile2.php?dir=' . $_GET['dir'] . '&amp;piece=' . $mytile['idnum'] . '">Place</a>' : '' ?></td>
  <td><?= item_display($mytile, '') ?></td>
  <td><?= $mytile['itemname'] ?></td>
	<td class="centered"><?= $mytile['qty'] ?></td>
 </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
  echo '</table>';
}
else
  echo '<p>You have no Maze Pieces in Storage.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
