<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

$command = 'SELECT itemname FROM monster_items WHERE custom=\'no\' AND rare=\'no\' AND itemtype LIKE \'plant/seed%\'';
$items = $database->FetchMultiple($command, 'fetching seeds');

delete_inventory_byid($this_inventory['idnum']);

$item_is = array_rand($items, 2);

$itemname1 = $items[$item_is[0]]['itemname'];
$itemname2 = $items[$item_is[1]]['itemname'];

add_inventory($user['user'], '', $itemname1, 'Created from a ' . $this_item['itemname'], $this_inventory['location']);
add_inventory($user['user'], '', $itemname2, 'Created from a ' . $this_item['itemname'], $this_inventory['location']);

echo '<p>You feel a warm, summer breeze...</p><p>The ring in your hand has been swept away, replaced with ' . $itemname1 . ' and ' . $itemname2 . '!';
?>
