<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/rocks.php';

$AGAIN_WITH_ANOTHER = true;
$RECOUNT_INVENTORY = true;

  require_once 'commons/questlib.php';

if($this_item['itemname'] == 'Small Rock' || $this_item['itemname'] == 'Stone Block')
{
  $num_items = mt_rand(0, mt_rand(1, 3));

  $first_small_rock = get_quest_value($user['idnum'], 'first small rock');
  if($first_small_rock === false)
  {
    add_quest_value($user['idnum'], 'first small rock', 1);
    $num_items = 2;
  }
}
else if($this_item['itemname'] == 'Large Rock')
{
  $num_items = mt_rand(mt_rand(0, 1), mt_rand(5, 6));

  $first_large_rock = get_quest_value($user['idnum'], 'first large rock');
  if($first_large_rock === false)
  {
    add_quest_value($user['idnum'], 'first large rock', 1);
    $num_items = 5;
  }
}
else if($this_item['itemname'] == 'Really Enormously Tremendous Rock')
{
  $num_items = mt_rand(1, mt_rand(8, 10));

  $first_enormous_rock = get_quest_value($user['idnum'], 'first enormous rock');
  if($first_enormous_rock === false)
  {
    add_quest_value($user['idnum'], 'first enormous rock', 1);
    $num_items = 8;
  }
}
else if($this_item['itemname'] == 'Small Geminid')
  $num_items = mt_rand(1, 3);
else
  die($this_item['itemname'] . '?! report to an admin! report to an admin!! :P  oh, but, no, seriously: probably an admin needs to know.  this is an error of some kind.');

delete_inventory_byid($this_inventory['idnum']);

$itemnames = GenerateItemsFromRocks($num_items);

$descript = array(
  'break apart',
  'crack open',
  'chisel away',
);

$descript2 = array(
  'eventually recovering',
  'revealing',
  'exposing',
);

if(count($itemnames) > 0)
{
  $items = 0;
  foreach($itemnames as $itemname)
  {
    add_inventory($user['user'], '', $itemname, 'Recovered from ' . $this_item['itemname'], $this_inventory['location']);

    $items++;

    if($items > 1)
      $itemlist .= ($items == count($itemnames) ? ' and ' : ', ');

    $itemlist .= $itemname;
  }

  $message = 'You ' . $descript[array_rand($descript)] . ' the ' . $this_item['itemname'] . ', ' . $descript2[array_rand($descript2)] . ' '. $itemlist . '.';
}
else
  $message = 'You ' . $descript[array_rand($descript)] . ' the ' . $this_item['itemname'] . ', ' . $descript2[array_rand($descript2)] . ' nothing!  How unfortunate.';

echo '<p>', $message, '</p>';
?>
