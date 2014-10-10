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
    <?php for($y = $min_y; $y <= $max_y; ++$y): ?>
        <div class="row">
            <?php for($x = $min_x; $x <= $max_x; ++$x): ?>
                <?php if($y == $this_tile['y'] && $x == $this_tile['x']): ?>
                    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/maze/yah_tiny.png" width="7" height="7" alt="You are here" title="You are here" />
                <?php elseif($pieces[$x][$y]['feature'] == 'gate'): ?>
                    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/maze/portal_tiny.png" width="7" height="7" alt="Gate" title="Gate" />
                <?php elseif($pieces[$x][$y]['feature'] == 'shop'): ?>
                    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/maze/store_tiny.png" width="7" height="7" alt="Shop" title="Shop" />
                <?php elseif($pieces[$x][$y]['feature'] == 'weird'): ?>
                    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/maze/weird_tiny.png" width="7" height="7" alt="Weird" title="Weird" />
                <?php elseif($pieces[$x][$y]['idnum'] > 0): ?>
                    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/maze/<?= $pieces[$x][$y]['tile'] ?>_tiny.png" width="7" height="7" alt="" />
                <?php else: ?>
                    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/maze/none_tiny.png" width="7" height="7" alt="" />
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    <?php endfor; ?>
</div>
