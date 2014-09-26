<?php
 if($okay_to_be_here !== true)
   exit();

$RECOUNT_INVENTORY = true;
$AGAIN_WITH_ANOTHER = true;

$two_hour = array(
  'Sugar Cookies' => '',
  'Arugula' => '',
  'Mint Tea' => '',
  'Orange Tea' => '',
);

$four_hour = array(
  'Redsberries' => '',
  'Pecans' => '',
);

$six_hour = array(
  'Coconut Meat' => '',
  'Apple Sauce' => '',
  'Chickpeas' => '',
);

$eight_hour = array(
  'Red Jelly' => '',
  'Purple Jelly' => '',
  'Swirled Jelly' => '',
  'White Radish' => '',
);

$items = array();
$set = rand(1, 5);

if($set == 1)
{
  $items[] = array_rand($eight_hour);
  $items[] = array_rand($four_hour);
}
else if($set == 2)
{
  $items[] = array_rand($six_hour);
  $items[] = array_rand($six_hour);
}
else if($set == 3)
{
  $items[] = array_rand($six_hour);
  $items[] = array_rand($four_hour);
  $items[] = array_rand($two_hour);
}
else if($set == 4)
{
  $items[] = array_rand($eight_hour);
  $items[] = array_rand($two_hour);
  $items[] = array_rand($two_hour);
}
else if($set == 5)
{
  $items[] = array_rand($four_hour);
  $items[] = array_rand($four_hour);
  $items[] = array_rand($four_hour);
}

delete_inventory_byid($this_inventory['idnum']);

foreach($items as $itemname)
  add_inventory($user["user"], '', $itemname, "Found in a " . $this_inventory["itemname"] . '.', $this_inventory["location"]);

require_once 'commons/statlib.php';
record_stat($user['idnum'], 'Opened a Food Box', 1);
record_stat($user['idnum'], 'Food Hours Received from Food Boxes', 12);
?>
<p>You open the box, revealing:</p><ul><li><?= implode('</li><li>', $items) ?></li></ul>
