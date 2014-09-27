<?php
if($okay_to_be_here !== true)
  exit();

if(mt_rand(1, 100) <= 22)
{
  if($this_inventory['itemname'] == 'Black Stocking')
    $item = 'Black Cloth';
  else if($this_inventory['itemname'] == 'Green Stocking')
    $item = 'Green Cloth';
  else if($this_inventory['itemname'] == 'Purple Stocking')
    $item = 'Purple Cloth';
  else if($this_inventory['itemname'] == 'Red Stocking')
    $item = 'Red Cloth';
  else if($this_inventory['itemname'] == 'Yellow Stocking')
    $item = 'Yellow Cloth';
  else
    $item = false;

  delete_inventory_byid($this_inventory['idnum']);

  if($item !== false)
    add_inventory($user['user'], '', $item, 'Found inside a ' . $this_inventory['itemname'], $this_inventory['location']);

  echo '
    <p>You reach inside and feel something: ' . ($item === false ? '... nothing!' : $item) . '?!</p>
    <p>Oh, oops!  It was the stocking itself, which you have now turned inside out!</p>
    <p>Darn: once a stocking is inside out, it can <em>never</em> be restored to its former shape.  Sometimes truth is stranger than fiction.  So they say.</p>
  ';

  require_once 'commons/statlib.php';
  record_stat($user['idnum'], 'Ruined a Stocking', 1);

  $AGAIN_WITH_ANOTHER = true;
}
else
{
  $possible_items = array('Snappy Bricks', 'Coal', 'Candy Cane', 'Green Lollipop', 'Red Lollipop', 'Wereplushy', 'Tea-Drinker\'s Handbook', 'Holly');
  $item = $possible_items[array_rand($possible_items)];

  add_inventory($user['user'], '', $item, 'Found inside a ' . $this_inventory['itemname'], $this_inventory['location']);
  echo '<p>You reach inside and feel something: ' . $item . ($item == 'Coal' ? '?!' : '!') . '</p>';

  require_once 'commons/statlib.php';
  record_stat($user['idnum'], 'Found Something inside a Stocking', 1);

  $AGAIN_WITH_SAME = true;
}
?>
