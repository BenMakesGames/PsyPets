<?php
if($okay_to_be_here !== true)
  exit();

delete_inventory_byid($_GET["idnum"]);

add_inventory_quantity($user["user"], '', "Snapppy Bricks", "", $this_inventory["location"], 5);
?>
You take the <?= $this_inventory['itemname'] ?> apart into its constituent Snappy Bricks.
