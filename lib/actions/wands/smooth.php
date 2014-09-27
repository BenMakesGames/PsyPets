<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/statlib.php';
  
if($_GET['step'] == 2)
{
  $AGAIN_WITH_ANOTHER = true;
  $RECOUNT_INVENTORY = true;

  $num_items = mt_rand(10, mt_rand(15, 25));

  delete_inventory_byid($this_inventory['idnum']);

  $possible_items = array(
    'Amethyst Rose Candle',
    'Black Candle',
    'Blueberry Candle',
    'Brown Sugar Candle',
    'Candle',
    'Chocolate Candle',
    'Fire Spice Candle',
    'Mint Tea Candle',
    'Red Candle',
    'Silver Candle',
    'Yellow Candle'
  );

  for($x = 0; $x < $num_items; ++$x)
    $treasures[$possible_items[array_rand($possible_items)]]++;

  echo '
    <p>You give the wand a skilled flick, and ' . $num_items . ' Candles flicker into existence around you.</p>
    <p>As they clatter to the ground, you notice the wand has vanished!</p>
    <h5>Candles Received</h5>
    <ul>
  ';

  record_stat($user['idnum'], 'Summoned a Candle', $num_items);

  foreach($treasures as $itemname=>$quantity)
  {
    add_inventory_quantity($user['user'], 'u:' . $user['idnum'], $itemname, 'Summoned by a ' . $this_inventory['itemname'], $this_inventory['location'], $quantity);
    
    echo '<li>' . $quantity . '&times; ' . $itemname . '</li>';
  }

  echo '</ul>';
}
else
{
  echo '
    <p>Will you use the ' . $this_inventory['itemname'] . '?</p>
    <ul><li><a href="?idnum=' . $this_inventory['idnum'] . '&amp;step=2">I will indeed!</a></li></ul>
  ';
}
?>
