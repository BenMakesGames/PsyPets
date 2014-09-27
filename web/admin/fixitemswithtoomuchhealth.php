<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once "commons/dbconnect.php";

$command = 'SELECT a.idnum,a.itemname FROM monster_inventory AS a LEFT JOIN monster_items AS b ON a.itemname=b.itemname WHERE a.health>0 AND b.durability=0 LIMIT 5000';
$items = $database->FetchMultiple(($command, 'fetching items with durability they don\'t need');

$items_fixed = array();

foreach($items as $item)
{
  $command = 'UPDATE monster_inventory SET health=0 WHERE idnum=' . $item['idnum'] . ' LIMIT 1';
  $database->FetchNone(($command, 'updating health of item');

  $items_fixed[$item['itemname']]++;
}

if(count($items_fixed) > 0)
{
  echo '<head><meta http-equiv="refresh" content="1" /></head>';
}

echo '<p>Fixed...</p><ul>';

foreach($items_fixed as $item_name=>$quantity)
  echo '<li>' . $quantity . '&times; ' . $item_name . '</li>';

echo '</ul>';
?>
