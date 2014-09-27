<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;
$RECOUNT_INVENTORY = true;

delete_inventory_byid($this_inventory['idnum']);

$possible_items = array(
  'Hydrogen',
  'Radioactive Material',
  'Pyrium',
  'Gossamer',
  'Aquite',
  'Zephrous',
  'Tin',
  'Iron',
  'Gold',
  'Zinc'
);

$n_items = mt_rand(1, 3);
$item_list = array();

for($i = 0; $i < $n_items; ++$i)
{
  $itemname = $possible_items[array_rand($possible_items)];
  add_inventory($user['user'], '', $itemname, 'Recovered from ' . $this_item['itemname'], $this_inventory['location']);
  $item_list[] = $itemname;
}

require_once 'commons/statlib.php';
record_stat($user['idnum'], 'Untangled a Brane', 1);

$descript2 = array(
  'eventually recovering',
  'revealing',
  'exposing',
);

echo '<p>You untangle the ', $this_item['itemname'], ', ', $descript2[array_rand($descript2)], ' ', list_nice($item_list), '.</p>';
?>
