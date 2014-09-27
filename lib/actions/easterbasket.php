<?php
if($okay_to_be_here !== true)
  exit();

if($EASTER > 0)
{
  $AGAIN_WITH_ANOTHER = true;
  
  $descript2 = array(
    'revealing',
    'rewarding you with',
    'exposing',
  );
  
  $items = array(
    'Blue Egg',
    'Blue Egg',
  
    'Egg',
    'Speckled Egg',
  
    'Plastic Egg',
    'Plastic Egg',
    'Plastic Egg',
    'Plastic Egg',
    'Plastic Egg',
    'Plastic Egg',
  
    'Copper-Dyed Egg',
  );
  
  delete_inventory_byid($this_inventory['idnum']);
  
  $num_items = mt_rand(3, 7);
  
  echo 'You raid the ' . $this_item['itemname'] . ', ' . $descript2[array_rand($descript2)] . '...<ul>';
  
  for($x = 0; $x < $num_items; ++$x)
  {
    $itemname = $items[array_rand($items)];
  
    add_inventory($user['user'], '', $itemname, 'Recovered from ' . $this_item['itemname'], $this_inventory['location']);
  
    echo '<li>' . $itemname . '</li>';
  }
  
  echo '</ul>';

  require_once 'commons/statlib.php';
  record_stat($user['idnum'], 'Easter Baskets Opened', 1);
}
else
  echo 'But... it\'s not Easter....';
?>
