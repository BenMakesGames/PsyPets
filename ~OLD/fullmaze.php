<?php

// the pattern is too big; this script just crashes the browser

require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/mazelib.php';

if($admin['clairvoyant'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$min_x = false;
$min_y = false;
$max_x = false;
$max_y = false;

$command = 'SELECT * FROM psypets_maze';
$result = mysql_query($command);

$pieces = array();

while($piece = mysql_fetch_assoc($result))
{
  $pieces[$piece['x']][$piece['y']] = $piece;
  
  if($min_x === false || $piece['x'] < $min_x)
    $min_x = $piece['x'];

  if($max_x === false || $piece['x'] > $max_x)
    $max_x = $piece['x'];

  if($min_y === false || $piece['y'] < $min_y)
    $min_y = $piece['y'];

  if($max_y === false || $piece['y'] > $max_y)
    $max_y = $piece['y'];
}

echo '<nobr>';

for($y = $min_y; $y <= $max_y; ++$y)
{
  for($x = $min_x; $x <= $max_x; ++$x)
  {
    if($pieces[$x][$y]['feature'] == 'gate')
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

    echo '<img src="gfx/maze/' . $graphic . '_tiny.png" width="7" height="7" alt="" />';
  }
  
  echo '</nobr><br /><nobr>';
}

echo '</nobr>';
?>
