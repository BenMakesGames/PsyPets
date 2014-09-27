<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/utility.php';

$AGAIN_WITH_ANOTHER = true;
$RECOUNT_INVENTORY = true;

$command = 'SELECT itemname FROM monster_items WHERE custom=\'no\' AND rare=\'no\' AND action=\'\' AND bulk<=40 ORDER BY RAND() LIMIT ' . mt_rand(2, 4);
$these_items = $database->FetchMultiple($command, 'fetching items');

delete_inventory_byid($this_inventory['idnum']);

foreach($these_items as $that_item)
{
  $item_names[] = $that_item['itemname'];
  add_inventory($user['user'], '', $that_item['itemname'], 'Found inside a ' . $this_item['itemname'], $this_inventory['location']);
}

echo 'You smash open the ' . $this_item['itemname'] . ', revealing ' . list_nice($item_names) . '.';
?>
