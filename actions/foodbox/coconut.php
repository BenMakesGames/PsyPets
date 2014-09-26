<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

delete_inventory_byid($this_inventory['idnum']);

$items = array('Coconut Meat', 'Coconut Juice');

$a = mt_rand(1, 100);

if($a <= 50)
  $items[] = 'Coconut Meat';
else if($a <= 80)
  $items[] = 'Coconut Juice';
else
{
  $extra_item = array('Sour Lime', 'Sour Lime', 'Coconut Cordial');
  $items[] = $extra_item[array_rand($extra_item)];
}

foreach($items as $item)
  add_inventory($user['user'], 'u:' . $user['idnum'], $item, '', $this_inventory['location']);

sort($items);

require_once 'commons/statlib.php';
record_stat($user['idnum'], 'Coconuts Opened', 1);
?>
<p>Breaking open the Coconut reveals <?= implode(', ', $items) ?>.</p>
