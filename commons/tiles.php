<?php
$TILES_BASE = array(
     1 => 'grass',
     2 => 'water',
     3 => 'water_top',
     4 => 'water_left',
     5 => 'water_right',
     6 => 'water_bottom',
     7 => 'water_top_left',
     8 => 'water_top_right',
     9 => 'water_bottom_left',
    10 => 'water_bottom_right',
    11 => 'water_top_left_i',
    12 => 'water_top_right_i',
    13 => 'water_bottom_left_i',
    14 => 'water_bottom_right_i',
    15 => 'house_left',
    16 => 'house_middle',
    17 => 'house_right',
    18 => 'house_left_door',
    19 => 'house_left_window',
    20 => 'house_middle_door',
    21 => 'house_right_door',
    22 => 'house_right_window',
    23 => 'roof_top',
    24 => 'roof_bottom',
    25 => 'roof_top_left',
    26 => 'roof_top_right',
    27 => 'roof_bottom_left',
    28 => 'roof_bottom_right',
    29 => 'house_left2',
    30 => 'house_left2_window',
    31 => 'house_middle2',
    32 => 'house_middle2_window',
    33 => 'house_middle_window',
    34 => 'house_right2',
    35 => 'house_right2_window',
    36 => 'grass_2',
    37 => 'flowers_1',
    38 => 'flowers_2',
    39 => 'flowers_3',
);

$TILES_DECOR = array(
     1 => 'tree_bottom',
     2 => 'tree_top',
     3 => 'signpost',
     4 => 'bridge_vertical',
     5 => 'bridge_horizontal',
     6 => 'rail_bottom_left',
     7 => 'rail_bottom_right',
     8 => 'rail_end_bottom_left',
     9 => 'rail_end_bottom_right',
    10 => 'rail_end_left',
    11 => 'rail_end_right',
    12 => 'rail_end_top_left',
    13 => 'rail_end_top_right',
    14 => 'rail_left',
    15 => 'rail_middle',
    16 => 'rail_right',
    17 => 'rail_top_left',
    18 => 'rail_top_right',
    19 => 'arch',
    20 => 'xmas_bottom',
    21 => 'xmas_top',
    22 => 'pumpkin',
    23 => 'fountain_bottom',
    24 => 'fountain_top',
);

function GenerateTile()
{
  global $TILES_BASE, $TILES_DECOR;

  if(mt_rand(1, 5) == 1)
    $data = 'd' . array_rand($TILES_DECOR);
  else
  {
    if(mt_rand(1, 4) == 1) // 1:4 chance of being water or grass
    {
      if(mt_rand(1, 4) == 1) // 1:4 chance of being water
        $data = 'b2';
      else                   // 3:4 chance of being grass
        $data = 'b1';
    }
    else // something completely different (or maybe water or grass :P)
      $data = 'b' . array_rand($TILES_BASE);
  }

  return $data;
}

function CreateMap($groupid)
{
  $data = str_repeat('000000', 15 * 20);

  $command = 'INSERT INTO psypets_towns (groupid, data) VALUES ' .
             '(' . $groupid . ', \'' . $data . '\')';
  fetch_none($command, 'creating map');
}

function EditTile($groupid, $x, $y, $base, $decor)
{
  $command = 'SELECT data FROM psypets_towns WHERE groupid=' . $groupid . ' LIMIT 1';
  $data = fetch_single($command, 'fetching map data');

  if($data === false)
    return false;

  $pos = $y * 20 + $x;

  $prefix = substr($data['data'], 0, $pos * 6);
  $postfix = substr($data['data'], $pos * 6 + 6);

  $hexbase = dechex($base);
  $hexdecor = dechex($decor);
  
  while(strlen($hexbase) < 3)
    $hexbase = '0' . $hexbase;

  while(strlen($hexdecor) < 3)
    $hexdecor = '0' . $hexdecor;

  $newdata = $prefix . $hexbase . $hexdecor . $postfix;

  $command = 'UPDATE psypets_towns SET data=\'' . $newdata . '\' WHERE groupid=' . $groupid . ' LIMIT 1';
  fetch_none($command, 'editing a tile');
}

function GetTile($groupid, $x, $y)
{
  $command = 'SELECT data FROM psypets_towns WHERE groupid=' . $groupid . ' LIMIT 1';
  $data = fetch_single($command, 'fetching map data');

  if($data === false)
    return false;

  $pos = $y * 20 + $x;

  $tile = array(
    'base' => hexdec(substr($data['data'], $pos * 6, 3)),
    'decor' => hexdec(substr($data['data'], $pos * 6 + 3, 3)),
  );
  
  return $tile;
}

function _map_from_data($groupid, $can_edit, $data)
{
  global $TILES_BASE, $TILES_DECOR;

  $rows = explode("\n", $data);
  $mapxhtml = '<table border="0" cellspacing="0" cellpadding="0" id="townmap">';

  for($y = 0; $y < 15; ++$y)
  {
    $mapxhtml .= '<tr>';

    $row = substr($data, $y * 6 * 20, 6 * 20);

    for($x = 0; $x < 20; ++$x)
    {
      $base = hexdec(substr($row, $x * 6, 3));
      $decor = hexdec(substr($row, $x * 6 + 3, 3));

      if($base > 0)
        $mapxhtml .= '<td style="background: url(\'gfx/town/' . $TILES_BASE[$base] . '.png\');">';
      else
        $mapxhtml .= '<td style="background: url(\'gfx/town/blank.png\');">';

      if($can_edit)
        $mapxhtml .= '<a href="grouptownedit.php?id=' . $groupid . '&x=' . $x . '&y=' . $y . '">';

      if($decor > 0)
        $mapxhtml .= '<img src="gfx/town/' . $TILES_DECOR[$decor] . '.png" width="24" height="24" alt="" />';
      else
        $mapxhtml .= '<img src="gfx/shim.png" width="24" height="24" alt="" />';

      if($can_edit)
        $mapxhtml .= '</a>';

      $mapxhtml .= '</td>';
    }

    $mapxhtml .= '</tr>';
  }

  $mapxhtml .= '</table>';

  return $mapxhtml;
}

function MapXHTML($groupid, $can_edit)
{
  $command = 'SELECT data FROM psypets_towns WHERE groupid=' . $groupid . ' LIMIT 1';
  $data = fetch_single($command, 'fetching map data');

  if($data === false)
    return false;
  else
    return _map_from_data($groupid, $can_edit, $data['data']);
}
?>
