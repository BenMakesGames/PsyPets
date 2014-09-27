<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

delete_inventory_byid($this_inventory['idnum']);

$items = array('Pumpkin Pieces', 'Pumpkin Pieces', 'Raw Pumpkin Seeds');

if(mt_rand(1, 3) == 1)
  $items[] = 'Raw Pumpkin Seeds';

$a = mt_rand(1, 100);

if($a <= 50)
  $items[] = 'Pumpkin Pieces';
else if($a <= 80)
  $items[] = 'Raw Pumpkin Seeds';
else
{
  if(mt_rand(1, 2) == 1)
  {
    $extra_item = array('Chinese Lantern', 'Talon');
    $items[] = $extra_item[array_rand($extra_item)];
  }
  else
  {
    $extra_item = array('Red Taffy', 'Sour Taffy', 'Orange Taffy', 'Purple Taffy', 'Candy Corn', 'Crispy Crunchy Chocolate Chew');
    $amount = mt_rand(2, 3);
    
    for($i = 0; $i < $amount; ++$i)
      $items[] = $extra_item[array_rand($extra_item)];
  }
}

foreach($items as $item)
  add_inventory($user['user'], 'u:' . $user['idnum'], $item, '', $this_inventory['location']);

require_once 'commons/statlib.php';
record_stat($user['idnum'], 'Pumpkins Smashed', 1);

sort($items);
?>
Breaking open the Pumpkin reveals <?= implode(', ', $items) ?>.
