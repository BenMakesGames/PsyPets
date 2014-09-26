<?php
if($okay_to_be_here !== true)
  exit();

if($_GET['step'] == 2)
{
  $AGAIN_WITH_ANOTHER = true;
  
  $descript2 = array(
    'revealing',
    'uncovering',
    'exposing',
  );
  
  if($this_item["itemname"] == 'Gold-Dyed Egg')
  {
    $items = array(
      'Wand of Wonder',
      'Wand of Wonder',

      'Ears',
      'Ears',
      'Ears',

      'Edelweiss',
      'Edelweiss',

      'Gold Tumbleweed',
      'Gold Tumbleweed',
      'Gold Tumbleweed',

      'Maze Piece Summoning Scroll',
      'Maze Piece Summoning Scroll',
    );
    
    $num_items = mt_rand(1, 2);
  }
  else if($this_item['itemname'] == 'Silver-Dyed Egg')
  {
    $items = array(
      'Chocolate Bunny',
      'The Dancing Men',
      'Hamlet: Act I Scene II',
      'Gossamer',
      'Minipalm',
      'Fugly Stick',
    );

    $num_items = mt_rand(1, mt_rand(2, 3));
  }
  else if($this_item['itemname'] == 'Copper-Dyed Egg')
  {
    $items = array(
      'Baking Chocolate',
      'Sugar Beet',
      'Baking Soda',
      'Egg',
      'Cream of Tartar',
      'Raw Milk',
      'Food-Summoning Scroll',
      'Flour',
      'Fugly Stick',
    );

    $num_items = mt_rand(6, 8);
  } 
  else
    die($this_item['itemname'] . '?');

  delete_inventory_byid($this_inventory['idnum']);
  
  $itemnames = array();
  
  for($i = 0; $i < $num_items; ++$i)
    $itemnames[] = $items[array_rand($items)];
  
  sort($itemnames);
  
  $items = 0;
  
  foreach($itemnames as $itemname)
  {
    add_inventory($user["user"], '', $itemname, 'Recovered from ' . $this_item["itemname"], $this_inventory["location"]);
    $items++;
  
    if($items > 1)
      $itemlist .= ($items == count($itemnames) ? ' and ' : ', ');
  
    $itemlist .= $itemname;
  }
  
  echo 'You open the ' . $this_item['itemname'] . ', ' . $descript2[array_rand($descript2)] . ' ' . $itemlist . '.';
}
else
{
?>
It's true that these may contain great treasures, but even the <?= $this_inventory['itemname'] ?> itself is worth a small fortune.</p>
<p>Do you really want to open it?</p>
<ul>
 <li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&step=2">Break it open, like a seagull breaks open clams!</a></li>
</ul>
<p class="size8"><i>(By dropping it on a rock?  What? >_>)</i>
<?php
}
?>
