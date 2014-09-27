<?php
if($yes_yes_that_is_fine !== true)
  exit();

$FINISHED_CASTING = true;

$num_items = mt_rand(2, 8);

for($x = 0; $x < $num_items; ++$x)
  add_inventory_cached($user['user'], 'u:' . $user['idnum'], 'Blood', 'Summoned by ' . $user['display'], 'home');

process_cached_inventory();

echo '<p>' . $num_items . ' Blood oozes from the shrine.  <i>(Find them in your Common Room.)</i></p>';
?>
