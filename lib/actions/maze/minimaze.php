<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/mazelib.php';

$this_tile = get_maze_byid($user['mazeloc']);

$min_x = $this_tile['x'] - 30;
$min_y = $this_tile['y'] - 30;
$max_x = $this_tile['x'] + 30;
$max_y = $this_tile['y'] + 30;

$this_z = $this_tile['z'];

$piece_list = $database->FetchMultiple('
  SELECT *
  FROM psypets_maze
  WHERE
    x BETWEEN ' . $min_x . ' AND ' . $max_x . '
    AND y BETWEEN ' . $min_y . ' AND ' . $max_y . '
    AND z=' . $this_z . '
  LIMIT ' . (61 * 61) . '
');

$pieces = array();

foreach($piece_list as $piece)
  $pieces[$piece['x']][$piece['y']] = $piece;

delete_inventory_byid($this_inventory['idnum']);
?>
<style type="text/css">
.mazeminimap
{
  border: 1px solid #000;
  width: 427px;
  height: 427px;
  margin-bottom: 1em;
}

.mazeminimap .row
{
  clear: both;
}

.mazeminimap img
{
  display: block;
  float: left;
}
</style>
<p><i>The scroll evaporates into a dense cloud of smoke which, for a moment, arranges itself into a clear image...</i></p>
<div class="mazeminimap">
<?php
for($y = $min_y; $y <= $max_y; ++$y)
{
  echo '<div class="row">';

  for($x = $min_x; $x <= $max_x; ++$x)
  {
    if($y == $this_tile['y'] && $x == $this_tile['x'])
    {
      echo '<img src="' . $SETTINGS['protocol'] . '://saffron.psypets.net/gfx/maze/yah_tiny.png" width="7" height="7" alt="You are here" title="You are here" />';
      continue;
    }
    else if($pieces[$x][$y]['feature'] == 'gate')
    {
      echo '<img src="' . $SETTINGS['protocol'] . '://saffron.psypets.net/gfx/maze/portal_tiny.png" width="7" height="7" alt="Gate" title="Gate" />';
      continue;
    }
    else if($pieces[$x][$y]['feature'] == 'shop')
    {
      echo '<img src="' . $SETTINGS['protocol'] . '://saffron.psypets.net/gfx/maze/store_tiny.png" width="7" height="7" alt="Shop" title="Shop" />';
      continue;
    }
    else if($pieces[$x][$y]['feature'] == 'weird')
    {
      echo '<img src="' . $SETTINGS['protocol'] . '://saffron.psypets.net/gfx/maze/weird_tiny.png" width="7" height="7" alt="Weird" title="Weird" />';
      continue;
    }
    else if($pieces[$x][$y]['idnum'] > 0)
      $graphic = $pieces[$x][$y]['tile'];
    else
      $graphic = 'none';

    echo '<img src="' . $SETTINGS['protocol'] . '://saffron.psypets.net/gfx/maze/' . $graphic . '_tiny.png" width="7" height="7" alt="" />';
  }

  echo '</div>';
}
?>
</div>
