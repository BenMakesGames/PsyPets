<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/tiles.php';

$tile = $this_inventory['data'];
if(strlen($tile) == 0)
{
  $tile = GenerateTile();
  $command = 'UPDATE monster_inventory SET data=' . quote_smart($tile) . ' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'setting tile data');
}

if($tile{0} == 'b')
{
  $type = 'base';
  $graphic = $TILES_BASE[substr($tile, 1)];
}
else if($tile{0} == 'd')
{
  $type = 'decor';
  $graphic = $TILES_DECOR[substr($tile, 1)];
}

?>
<i>This small, well-cut stone has the following "<?= $type ?>" painted on its surface:</i></p>
<p><img src="<?= 'gfx/town/' . $graphic . '.png' ?>" alt="" width="48" height="48" />
