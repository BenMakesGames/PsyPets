<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

if($this_item['itemname'] == 'Purple-Dyed Egg')
{
  $items = array(
    'Page of Nina\'s Notes', 'Purple Lilac', 'Scabious',
    'Cheap Plastic Earrings', 'Cheap Plastic Bracelet', 'Kompeito', 'Kompeito',
    'Blue Cloth', 'Blueberry Ice Cream',
  );

  $n = mt_rand(1, 3);
}
else if($this_item['itemname'] == 'Green-Dyed Egg')
{
  $items = array(
    'Artichoke', 'Sour Lime', 'Celery', 'Celery',
    'Greenish Leaf', 'Greenish Leaf', 'Mint Leaves', 'Mint Leaves',
    'Sour Taffy', 'Green Taffy', 'Clover Leaf', 'Clover Leaf',
    '3-Leaf Clover', '3-Leaf Clover',
  );

  $n = mt_rand(mt_rand(1, 2), mt_rand(3, 5));
}
else if($this_item['itemname'] == 'Orange-Dyed Egg')
{
  $items = array(
    'Orange Taffy', 'Orange Taffy', 'Cream Cheese', 'Sour Cream',
    'Crispy Crunchy Chocolate Chew', 'Crispy Crunchy Chocolate Chew',
    'Pyrium', 'Ginger', 'Wild Oats', 'Wild Oats',
  );

  $n = mt_rand(1, 2);
}
else
  die('What kind of dyed egg is this!?');

$itemlist = array();

for($i = 0; $i < $n; ++$i)
{
  $itemname = $items[array_rand($items)];
  $itemlist[] = $itemname;
  add_inventory($user['user'], '', $itemname, 'Recovered from ' . $this_item['itemname'], $this_inventory['location']);
}

delete_inventory_byid($this_inventory['idnum']);
?>
<p>Opening the egg reveals: <?= implode(', ', $itemlist) ?>.</p>
