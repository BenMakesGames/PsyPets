<?php
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/mazelib.php';

if($admin['clairvoyant'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$tile = get_maze_byid($user['mazeloc']);

$min_x = $tile['x'] - 30;
$min_y = $tile['y'] - 30;
$max_x = $tile['x'] + 30;
$max_y = $tile['y'] + 30;

$pieces = array();

$command = 'SELECT * FROM psypets_maze WHERE x>=' . $min_x . ' AND x<=' . $max_x . ' ' .
           'AND y>=' . $min_y . ' AND y<=' . $max_y . ' LIMIT ' . (61 * 61);
$result = mysql_query($command);

while($piece = mysql_fetch_assoc($result))
  $pieces[$piece['x']][$piece['y']] = $piece;

echo '<nobr>';

for($y = $min_y; $y <= $max_y; ++$y)
{
  for($x = $min_x; $x <= $max_x; ++$x)
  {
    if($y == $tile['y'] && $x == $tile['x'])
    {
      echo '<img src="gfx/maze/yah_tiny.png" width="7" height="7" alt="You are here" title="You are here" />';
      continue;
    }
    else if($pieces[$x][$y]['feature'] == 'gate')
    {
      echo '<img src="gfx/maze/portal_tiny.png" width="7" height="7" alt="Gate" title="Gate" />';
      continue;
    }
    else if($pieces[$x][$y]['feature'] == 'shop')
    {
      echo '<img src="gfx/maze/store_tiny.png" width="7" height="7" alt="Shop" title="Shop" />';
      continue;
    }
    else if($pieces[$x][$y]['feature'] == 'weird')
    {
      echo '<img src="gfx/maze/weird_tiny.png" width="7" height="7" alt="Weird" title="Weird" />';
      continue;
    }
    else if($pieces[$x][$y]['idnum'] > 0)
      $graphic = $pieces[$x][$y]['tile'];
    else
      $graphic = 'none';

    echo '<img src="gfx/maze/' . $graphic . '_tiny.png" width="7" height="7" alt="" title="' . $pieces[$x][$y]['idnum'] . '" />';
  }
  
  echo '</nobr><br /><nobr>';
}

echo '</nobr>';
?>
