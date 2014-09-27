<?php
if($yes_yes_that_is_fine !== true)
  exit();

$FINISHED_CASTING = true;

if(mt_rand(1, 5) == 1)
  $num_items = 2;
else
  $num_items = 1;

$possible_items = array(
  'The Two Travelers and the Axe', 'The Astronomer',
  'The Two Travelers and the Axe', 'The Astronomer',
  'The Imp', 'The Imp',
  'The Imp', 'The Imp',
  'Clairvoyance Scroll', 'Nettling Scroll',
  'Nettling Scroll', 'Nettling Scroll',
  'Clairvoyance Scroll', 'Nettling Scroll',
  'Clairvoyance Scroll', 'Nettling Scroll',
  'The Earth Golem\'s Song', 'The Earth Golem\'s Song',
  'The Earth Golem\'s Song', 'The Earth Golem\'s Song',
  'The Earth Golem\'s Song', 'The Earth Golem\'s Song',

  'Scroll of Monster Summoning', 'Food-Summoning Scroll',
  'Scroll of Local Teleportation',
  'Seal of Solomon', 'Lesser Divination Scroll',
);

for($x = 0; $x < $num_items; ++$x)
{
  $item = $possible_items[array_rand($possible_items)];
  $items[] = $item;
  add_inventory_cached($user['user'], 'u:' . $user['idnum'], $item , 'Summoned by ' . $user['display'], 'home');
}

process_cached_inventory();

if($num_items == 1)
  echo '<p>A scroll - ' . $items[0] . ' - unfurls itself before you. <i>(Find it in your Common Room.)</i></p>';
else
  echo '<p>Two scrolls -  ' . $items[0] . ' and ' . $items[1] . ' - unfurl themselves before you. <i>(Find them in your Common Room.)</i></p>';
?>
