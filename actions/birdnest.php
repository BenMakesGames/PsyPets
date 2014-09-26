<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

$possible_items = array(
  'Egg', 'Egg', 'Egg',
  'Fluff', 'Fluff', 'Reed',
  'Feather', 'Feather',
  'Speckled Egg',
  'Blue Egg',
);

delete_inventory_byid($this_inventory['idnum']);

// an average of 2 items per nest
$amount = rand(1, mt_rand(2, 4));

for($x = 0; $x < $amount; ++$x)
  $items[] = $possible_items[array_rand($possible_items)];

// 1:50 chance of a ring being in there
if(mt_rand(1, 50) == 1)
{
  if(mt_rand(1, 3) == 1)
    $items[] = 'Gold Ring';
  else
    $items[] = 'Silver Ring';
}

echo '<p>You find: ';

foreach($items as $item)
{
  $item_list[] = $item;
  add_inventory($user['user'], '', $item, 'Found in a Bird Nest', $this_inventory['location']);
}

sort($item_list);

require_once 'commons/statlib.php';
record_stat($user['idnum'], 'Raided a ' . $this_inventory['itemname'], 1);

echo implode(', ', $item_list), '!</p>';
?>
