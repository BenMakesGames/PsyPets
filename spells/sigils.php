<?php
if($yes_yes_that_is_fine !== true)
  exit();

$FINISHED_CASTING = true;

$num_items = mt_rand(4, 8);

$possible_items = array(
  'Wand of Fire', 'Wand of Water', 'Wand of Wind',
);

for($x = 0; $x < $num_items; ++$x)
  add_inventory_cached($user['user'], 'u:' . $user['idnum'], $possible_items[array_rand($possible_items)], 'Summoned by ' . $user['display'], 'home');

process_cached_inventory();

echo '<p>' . $num_items . ' elemental wands clatter to the ground. <i>(Find them in your Common Room.)</i></p>';

require_once 'commons/statlib.php';
record_stat($user['idnum'], 'Cast Sigil\'s Spectrum', 1);
?>
