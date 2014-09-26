<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

if($this_item["itemname"] == "4-Leaf Clover")
{
  delete_inventory_byid($this_inventory['idnum']);
  add_inventory($user["user"], $this_inventory['creator'], "3-Leaf Clover", "", $this_inventory["location"]);
  add_inventory($user["user"], '', "Clover Leaf", "", $this_inventory["location"]);
}
else if($this_item["itemname"] == "5-Leaf Clover")
{
  delete_inventory_byid($this_inventory['idnum']);
  add_inventory($user["user"], $this_inventory['creator'], "4-Leaf Clover", "", $this_inventory["location"]);
  add_inventory($user["user"], '', "Clover Leaf", "", $this_inventory["location"]);
}
?>
You pick a single Clover Leaf from the <?= $this_item["itemname"] ?>...
