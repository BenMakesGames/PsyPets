<?php
if($okay_to_be_here !== true)
  exit();

require_once "commons/itemlib.php";

$items = get_inventory_byuser($user["user"], $this_inventory["location"]);

$magnifying = false;

foreach($items as $item)
{
  if($item["itemname"] == "Magnifying Glass")
  {
    $magnifying = true;
    break;
  }
}
?>
<p>The print is too small to read<?= $magnifying ? ', even with a magnifying glass' : '' ?>.</p>
