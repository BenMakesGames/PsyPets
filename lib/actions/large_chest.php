<?php
if($okay_to_be_here !== true)
  exit();

$info = explode(";", $this_inventory["data"]);

require_once "commons/itemlib.php";

$items = get_inventory_byuser($user['user'], $this_inventory['location']);

$key_skeleton = false;
$key_other = false;

foreach($items as $item)
{
  if($item["itemname"] == "Skeleton Key")
  {
    $key_skeleton = true;
    break;
  }
  else if($item["itemname"] == "Gold Key" || $item["itemname"] == "Silver Key" || $item["itemname"] == "Copper Key")
  {
    $key_other = true;
  }
}

if($key_skeleton)
{
  $AGAIN_WITH_ANOTHER = true;

  delete_inventory_byid($this_inventory['idnum']);
  delete_inventory_byname($user['user'], 'Skeleton Key', 1, $this_inventory['location']);

  echo "You put the skeleton key into the chest's lock.  Before you can even turn it there is a loud <em>click</em>, and the key crumbles to ashes.</p>" .
       '<p>Opening up the chest reveals ';

  $itemlist = array(
    'Sigil of Wind', 'Sigil of Fire', 'Sigil of Water',
    'Sigil of Wind', 'Sigil of Fire', 'Sigil of Water',
    'Wand of Wind', 'Wand of Fire', 'Wand of Water',
    'Wand of Wind', 'Wand of Fire', 'Wand of Water',
    'Skeleton Key Blade', 'Skeleton Key Blade',
    'Skeleton Key Blade',
    'Maze Piece Summoning Scroll',
    'Magic Hourglass',
    'Brisingamen',
  );

  $a = rand(7, 11);

  $items = array();

  for($i = 0; $i < $a; ++$i)
  {
    if($i > 0)
    {
      if($i == $a - 1)
        echo ' and ';
      else
        echo ', ';
    }

    $item = $itemlist[array_rand($itemlist)];
    $items[] = $item;

    echo $item;
  }
  
  echo '.';

  foreach($items as $item)
    add_inventory($user['user'], '', $item, 'Found in a Large Locked Chest', $this_inventory['location']);
}
else if($key_other)
{
  echo "You try all the keys you have, but none of them fit the lock.";
}
else
{
  echo "The chest is locked.  You need a key to open it.";
}
?>
