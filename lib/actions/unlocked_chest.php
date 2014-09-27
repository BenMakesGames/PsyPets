<?php
if($okay_to_be_here !== true)
  exit();

require_once "commons/itemlib.php";

$items = get_inventory_byuser($user["user"], $this_inventory["location"]);

$key_skeleton = false;
$key_other = false;

delete_inventory_byid($this_inventory['idnum']);

echo 'You open the chest, revealing Phoenix Down which you hold triumphantly above your head for all to see.';

add_inventory($user["user"], '', 'Phoenix Down', "Found in a Tiny Unlocked Chest", $this_inventory['location']);
?>
