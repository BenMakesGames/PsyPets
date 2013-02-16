<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/mazelib.php';

if($admin['clairvoyant'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

if($_GET['action'] == 'query')
{
  $coord_x = (int)$_POST['x'];
  $coord_y = (int)$_POST['y'];
  $coord_z = (int)$_POST['z'];

  $tile = get_maze_bycoord($coord_x, $coord_y, $coord_z);
  
  if($tile === false)
    $messages[] = '<span class="failure">There is no tile at that location.</span>';
  else
  {
    $messages[] = '<pre>' . print_r($tile, true) . '</pre>';
    $messages[] = '<img src="/gfx/maze/' . $tile['tile'] . '.png" />';
  }
}
else if($_GET['action'] == 'move')
{
  $newloc = (int)$_POST['newloc'];
  
  $tile = get_maze_byid($newloc);
  
  if($tile === false)
    $messages[] = '<span class="failure">There is no such tile.</span>';
  else
  {
    maze_move_user($user, $newloc);
    $messages[] = '<span class="success">Moved!</span>';
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; The Pattern</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<?php
if(count($messages) > 0)
  echo '<ul><li>' . implode('</li><li>', $messages) . '</li></ul>';
?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; The Pattern</h4>
     <h5>Get Info</h5>
     <form action="/admin/maze.php?action=query" method="post">
     <p>Coordinates: <input name="x" value="<?= $coord_x ?>" />, <input name="y" value="<?= $coord_y ?>" , <input name="z" value="<?= $coord_z ?>" /> <input type="submit" value="Lookup" /></p>
     </form>
     <h5>Move</h5>
     <p>You are currently on tile #<?= $user['mazeloc'] ?>.</p>
     <form action="/admin/maze.php?action=move" method="post">
     <p>Move: <input name="newloc" value="<?= $user['mazeloc'] ?>" /> <input type="submit" value="Move" /></p>
     </form>
     <h5>View</h5>
     <ul>
      <li><a href="/admin/fullmaze.php">View the full maze on this floor</a> (may lag the browser for a few minutes on certain floors; may eat up your RAM!)</li>
      <li><a href="/admin/partialmaze.php">View the maze around you on this floor</a></li>
     </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
