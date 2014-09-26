<?php
if($okay_to_be_here !== true)
  exit();

$info = explode(';', $this_inventory["data"]);

require_once 'commons/itemlib.php';

$items = $database->FetchMultipleBy('
  SELECT idnum,COUNT(idnum) AS qty,itemname
  FROM monster_inventory
  WHERE
    user=' . quote_smart($user['user']) . '
    AND location=' . quote_smart($this_inventory['location']) . '
    AND itemname IN (
      \'Skeleton Key\', \'Gold Key\', \'Silver Key\', \'Copper Key\',
      \'Small Locked Chest\', \'Locked Chest\', \'Over-Sized Locked Chest\'
    )
  GROUP BY itemname
', 'itemname');

$key_skeleton = 0;
$key_other = false;

$chests = array();

foreach($items as $item)
{
  if($item['itemname'] == 'Skeleton Key')
    $key_skeleton += $item['qty'];
  else if($item['itemname'] == 'Gold Key' || $item['itemname'] == 'Silver Key' || $item['itemname'] == 'Copper Key')
    $key_other = true;
  else if($item['itemname'] == 'Small Locked Chest')
    $chests[] = 'Small Locked Chest';
  else if($item['itemname'] == 'Locked Chest')
    $chests[] = 'Locked Chest';
  else if($item['itemname'] == 'Over-Sized Locked Chest')
    $chests[] = 'Over-Sized Locked Chest';
}

if($key_skeleton > 0)
{
  delete_inventory_byid($this_inventory['idnum']);
  delete_inventory_byname($user['user'], 'Skeleton Key', 1, $this_inventory['location']);
  
  $key_skeleton--;

  echo "You put the skeleton key into the chest's lock, and turn it.  There\'s a loud <em>click</em> as the chest unlocks, and a simultaneous <em>crack</em> as the bone key snapes in half.</p>" .
       '<p>Opening up the chest reveals...</p><ul>';

  if($this_inventory['itemname'] == 'Small Locked Chest')
  {
    $itemlist = array('Silly Totem', 'Small Fancy Pillow', 'Copper', 'Gold', 'Aging Root', 'Snow Owl Mask', 'Scroll of Monster Summoning', 'Maze Piece Summoning Scroll');

    $a = mt_rand(1, mt_rand(2, 3));
  }
  else if($this_inventory['itemname'] == 'Locked Chest')
  {
    $itemlist = array('Royal Purple Pillow', 'Maze Piece Summoning Scroll', 'Gold', 'Small Black Umbrella', 'Carafe of Water', 'Spice Rack');
    
    $a = mt_rand(1, mt_rand(3, 4));
  }
  else if($this_inventory['itemname'] == 'Over-Sized Locked Chest')
  {
    $itemlist = array('Tie Dye Chair', 'Platinum', 'Maze Piece Summoning Scroll', 'Food-Summoning Scroll', 'Gold', 'Baegura\'s Lance');
    
    $a = mt_rand(2, 4);
  }

  $items[$this_inventory['itemname']]['qty']--;
  
  if($items[$this_inventory['itemname']]['qty'] == 0)
    unset($chests[array_search($this_inventory['itemname'], $chests)]);
  
  $treasures = array();

  for($i = 0; $i < $a; ++$i)
  {
    $item = $itemlist[array_rand($itemlist)];
    $treasures[] = $item;
    
    echo '<li>' . item_text_link($item) . '</li>';
  }
  
  echo '</ul>';

  foreach($treasures as $item)
    add_inventory($user['user'], '', $item, 'Found inside ' . $this_inventory['itemname'], $this_inventory['location']);
}
else if($key_other)
{
  echo 'You try all the keys you have, but none of them fit the lock.';
}
else
{
  echo 'The chest is locked.  You need a key to open it.';
}

if(count($chests) > 0)
{
  echo '<h5>Other Chests in This Room</h5><p>(You have ' . $key_skeleton . ' Skeleton Key' . ($key_skeleton != 1 ? 's' : '') . ' remaining.)</p><ul>';

  foreach($chests as $chest)
    echo '<li><a href="/itemaction.php?idnum=' . $items[$chest]['idnum'] . '">' . $items[$chest]['qty'] . '&times; ' . $chest . '</a></li>';

  echo '</ul>';
}
?>
