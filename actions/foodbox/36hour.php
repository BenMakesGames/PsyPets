<?php
 if($okay_to_be_here !== true)
   exit();

$RECOUNT_INVENTORY = true;
$AGAIN_WITH_ANOTHER = true;

$two_hour = array(
  'Mintberry Swirls' => '',
  'Arugula' => '',
  'Peas' => '',
  'Mint Tea' => '',
);

$four_hour = array(
  'Hot Grits' => '',
  'Pecans' => '',
);

$six_hour = array(
  'Coconut Meat' => '',
  'Apple Sauce' => '',
  'Pomegranate' => '',
);

$eight_hour = array(
  'Wheat' => '',
  'Purple Jelly' => '',
  'Swirled Jelly' => '',
  'Wild Oats' => '',
);

$twelve_hour = array(
  'Prickly Green' => '',
  'Chocolate Yogurt' => '',
);

$one_day = array(
  'Lemon-Buttered Fish' => '',
  'Pecan Pie' => '',
);

$items = array();

$hours = 36;

while($hours >= 24)
{
  switch(mt_rand(1, 4))
  {
    case 1: $items[] = array_rand($one_day); $hours -= 24; break;
    case 2: $items[] = array_rand($twelve_hour); $hours -= 12; break;
    case 3: $items[] = array_rand($eight_hour); $hours -= 8; break;
    case 4: $items[] = array_rand($six_hour); $hours -= 6; break;
  }
}

while($hours >= 12)
{
  switch(mt_rand(1, 4))
  {
    case 1: $items[] = array_rand($twelve_hour); $hours -= 12; break;
    case 2: $items[] = array_rand($eight_hour); $hours -= 8; break;
    case 3: $items[] = array_rand($six_hour); $hours -= 6; break;
    case 4: $items[] = array_rand($four_hour); $hours -= 4; break;
  }
}

while($hours >= 6)
{
  switch(mt_rand(1, 3))
  {
    case 1: $items[] = array_rand($six_hour); $hours -= 6; break;
    case 2: $items[] = array_rand($four_hour); $hours -= 4; break;
    case 3: $items[] = array_rand($two_hour); $hours -= 2; break;
  }
}

while($hours >= 4)
{
  switch(mt_rand(1, 2))
  {
    case 1: $items[] = array_rand($four_hour); $hours -= 4; break;
    case 2: $items[] = array_rand($two_hour); $hours -= 2; break;
  }
}

while($hours >= 2)
{
  $items[] = array_rand($two_hour);
  $hours -= 2;
}

delete_inventory_byid($this_inventory['idnum']);

foreach($items as $itemname)
  add_inventory($user['user'], '', $itemname, 'Found in a ' . $this_inventory['itemname'] . '.', $this_inventory["location"]);

require_once 'commons/statlib.php';
record_stat($user['idnum'], 'Opened a Food Box', 1);
record_stat($user['idnum'], 'Food Hours Received from Food Boxes', 36);
?>
<p>You open the box, revealing:</p><ul><li><?= implode('</li><li>', $items) ?></li></ul>
