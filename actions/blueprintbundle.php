<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/itemlib.php';

$AGAIN_WITH_ANOTHER = true;

delete_inventory_byid($this_inventory['idnum']);

echo '<p>You unravel the bundle to find...</p><ul>';

$itemlist = array(
  'Cape Blueprint', 'Cape Blueprint', 'Cape Blueprint', 'Cape Blueprint', 'Cape Blueprint',
  'Hammer Blueprint', 'Hammer Blueprint', 'Hammer Blueprint',
  'Cornucopia Blueprint', 'Cornucopia Blueprint',
  '8-Sided Die Blueprint',
);

$a = 2;
if(mt_rand(1, 4) == 1)
  $a++;

$treasures = array();

$treasures[] = 'Cape Blueprint';

echo '<li>' . item_text_link('Cape Blueprint') . '</li>';

for($i = 0; $i < $a; ++$i)
{
  $item = $itemlist[array_rand($itemlist)];
  $treasures[] = $item;
  
  echo '<li>' . item_text_link($item) . '</li>';
}

echo '</ul>';

foreach($treasures as $item)
  add_inventory($user['user'], '', $item, 'Found inside ' . $this_inventory['itemname'], $this_inventory['location']);
?>
