<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

delete_inventory_byid($this_inventory['idnum']);

echo '<p>The ' . $this_inventory['itemname'] . ' yields two Yellow Dye.</p>';

add_inventory($user['user'], $this_inventory['creator'], 'Yellow Dye', '', $this_inventory['location']);
add_inventory($user['user'], $this_inventory['creator'], 'Yellow Dye', '', $this_inventory['location']);

if(mt_rand(1, 100) <= 19) // 19% chance (not 20, just to fuck with people :P)
{
  echo '<p>Ooh, wait, no: three Yellow Dye!</p>';
  add_inventory($user['user'], $this_inventory['creator'], 'Yellow Dye', '', $this_inventory['location']);
}
?>
