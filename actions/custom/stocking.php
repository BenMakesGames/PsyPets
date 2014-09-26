<?php
if($okay_to_be_here !== true)
  exit();

$data = $this_inventory['data'];
$now = time();
$day = 60 * 60 * 24;

$numnum = false;

if(strlen($data) == 0)
  $data = $now + ($day * 1.5);
else if($now > $data)
{
  $data = $now + mt_rand($day, $day * 1.5);
  $numnum = true;
}

$command = 'UPDATE monster_inventory SET data=' . (int)$data . ' WHERE idnum=' . (int)$_GET["idnum"] . ' LIMIT 1';
$database->FetchNone($command, 'updating stocking');

if($numnum)
{
  $possible_items = array('Snappy Bricks', 'Coal', 'Candy Cane', 'Green Lollipop', 'Red Lollipop', 'Fake Plastic Glasses');
  $item = $possible_items[array_rand($possible_items)];

  add_inventory($user['user'], '', $item, 'Found inside a ' . $this_inventory['itemname'], $this_inventory['location']);
  echo 'You reach inside and feel something: ' . $item . ($item == 'Coal' ? '?!' : '!');
}
else
  echo 'You reach inside, but nothing\'s there.';

?>
