<?php
if($okay_to_be_here !== true)
  exit();

delete_inventory_byid($this_inventory['idnum']);

$AGAIN_WITH_ANOTHER = true;

// one whole turkey
$items = array('Whole Turkey');

// three to five sides
$sides = mt_rand(3, 5);

for($x = 0; $x < $sides; ++$x)
{
  switch(mt_rand(1, 3))
  {
    case 1: $items[] = 'Mashed Potatoes'; break;
    case 2: $items[] = 'Cranberry Sauce'; break;
    case 3: $items[] = 'Candied Yam'; break;
  }
}

// two deserts
for($x = 0; $x < 2; ++$x)
{
  switch(mt_rand(1, 5))
  {
    case 1: $items[] = 'Apple Pie'; break;
    case 2: $items[] = 'Pumpkin Pie'; break;
    case 3: $items[] = 'Pecan Pie'; break;
    case 4: $items[] = 'Blueberry Pie'; break;
    case 5: $items[] = 'Redsberry Pie'; break;
  }
}

foreach($items as $item)
  add_inventory($user['user'], '', $item, '', $this_inventory['location']);

echo '<p>You read the scroll, and before your eyes, a dinner appears!  You see...</p><ul><li>',
     implode('</li><li>', $items),
     '</li></ul>';
?>