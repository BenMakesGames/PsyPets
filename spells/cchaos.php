<?php
if($yes_yes_that_is_fine !== true)
  exit();

$FINISHED_CASTING = true;

$command = 'SELECT idnum,location FROM monster_inventory WHERE location LIKE \'home%\' AND location NOT LIKE \'home/$%\' AND itemname=\'Baking Chocolate\' AND user=' . quote_smart($user['user']);
$chocolate = fetch_multiple($command, 'fetching baking chocolate');

$possible_items = array(
  'Redsberries', 'Blueberries', 'Goodberries',
  'Baking Powder', 'Baking Soda', 'Wax', 'Flour',
  'Baking Powder', 'Baking Soda', 'Wax', 'Flour',
);

if(count($chocolate) > 0)
{
  foreach($chocolate as $item)
  {
    $itemname = $possible_items[array_rand($possible_items)];
    if(mt_rand(1, 10000) == 1)
      $itemname = 'Shade Essence';
  
    delete_inventory_byid($item['idnum']);
    add_inventory_cached($user['user'], 'u:' . $user['idnum'], $itemname, 'Transmuted from Baking Chocolate by ' . $user['display'] . '!', $item['location']);
  }

  process_cached_inventory();

  if(count($chocolate) == 1)
    echo '<p>With an unsettling bubbling sound, the single Baking Chocolate in your house is transformed!</p>';
  else
    echo '<p>With an unsettling bubbling sound, all ' . count($chocolate) . ' of the Baking Chocolate in your house are transformed!</p>';
}
else
{
  echo '<p>You catch a whiff of chocolate, but it passes.</p>';
}

require_once 'commons/statlib.php';
record_stat($user['idnum'], 'Cast Chocolate Chaos', 1);
?>
