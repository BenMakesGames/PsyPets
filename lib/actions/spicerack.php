<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;
$RECOUNT_INVENTORY = true;

delete_inventory_byid($this_inventory['idnum']);

$items = array(
  'Nutmeg', 'Cinnamon', 'Cardamom', 'Licorice Root',
);

$num_items = 2;

if(mt_rand(1, 4) == 1)
  $num_items++;

for($x = 0; $x <= $num_items; ++$x)
{
  $itemname = $items[array_rand($items)]; 
  $gimmes[] = $itemname;
  add_inventory($user['user'], '', $itemname, 'Recovered from ' . $this_item['itemname'], $this_inventory['location']);
} 

require_once 'commons/statlib.php';
record_stat($user['idnum'], 'Raided a Spice Rack', 1);

echo 'Jackpot!  ' . implode(', ', $gimmes) . '; they are yours now!';

if(mt_rand(1, 4) == 1)
{
  echo '  Mwa-ha-ha!';
  if(mt_rand(1, 2) == 1)
    echo ' >_>';
}
?>
