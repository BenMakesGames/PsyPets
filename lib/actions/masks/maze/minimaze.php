<?php
if($okay_to_be_here !== true)
  exit();

require_once "commons/mazelib.php";

$piece_list = $database->FetchMultiple('SELECT * FROM psypets_maze');

$pieces = array();

$minx = 0;
$miny = 0;
$maxx = 0;
$maxy = 0;

foreach($piece_list as $piece)
{
  if($piece['x'] < $minx)
    $minx = $piece['x'];
  else if($piece['x'] > $maxx)
    $maxx = $piece['x'];

  if($piece['y'] < $miny)
    $miny = $piece['y'];
  else if($piece['y'] > $maxy)
    $maxy = $piece['y'];

  $pieces[$piece['x']][$piece['y']] = $piece;
}

delete_inventory_byid($_GET['idnum']);
?>
<i>The scroll evaporates into a dense cloud of smoke which for a moment stands motionless as a hazy image...</i></p>
<pre>
<?php
for($y = $miny; $y <= $maxy; ++$y)
{
  for($x = $minx; $x <= $maxx; ++$x)
  {
    if($pieces[$x][$y]['idnum'] > 0)
    {
?>.<?php
    }
    else
    {
?> <?php
    }
  }
?>

<?php
}
?>
</pre>
<p>
