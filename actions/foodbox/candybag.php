<?php
 if($okay_to_be_here !== true)
   exit();

$RECOUNT_INVENTORY = true;
$AGAIN_WITH_ANOTHER = true;

$candies = array(
  'Candy Corn' => 16,
  'Wild Blueberry Twists' => 19,
  'Chocolate Drops' => 10,
  'Peach Gummies' => 12,
  'Pineapple Drop' => 11,
  'Watermelon Taffy' => 14,
  'Purple Taffy' => 13,
);

if($this_inventory['itemname'] == 'Small Bag of Candy')
  $candy_value = 40;
else if($this_inventory['itemname'] == 'Bag of Candy')
  $candy_value = 70;
else if($this_inventory['itemname'] == 'Box of Candy')
  $candy_value = 120;
else
  echo '<p>Error in item action for ' . $this_inventory['itemname'] . '.  Please notify That Guy Ben, so he can fix it.</p>';

delete_inventory_byid($this_inventory['idnum']);

$items = array();
$value = 0;

while($value < $candy_value - 5)
{
  $item = array_rand($candies);
  $items[] = $item;
  $value += $candies[$item];
}

foreach($items as $itemname)
  add_inventory($user['user'], '', $itemname, 'Found in a ' . $this_inventory['itemname'] . '.', $this_inventory['location']);

require_once 'commons/statlib.php';
record_stat($user['idnum'], 'Opened a Bag of Candy', 1);
?>
<p>You open the bag, revealing:</p><ul><li><?= implode('</li><li>', $items) ?></li></ul>
