<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;
$RECOUNT_INVENTORY = true;

delete_inventory_byid($this_inventory['idnum']);

$possible_items = array(
  'A Partridge In a Pear Tree',
  'April Showers',
  'Bamboo',
  'Bat Wing Bag',
  'Call of the Marlin',
  'Clear Lava Lamp',
  'Death by Cactus',
  'Eiffel Tower Blueprint',
  'Electrical Well',
  'Enlil',
  'Excalibur',
  'Fox Glove',
  'Gourmet Popcorn Maker',
  'Greater Scroll of Divination',
  'Highly Capricious Easy-Cake Oven',
  'Icicle Bow',
  'Ides of March',
  'Indoor Swimming Pool Blueprint',
  'Invisibility Cloak',
  'Iridescent Cup',
  'King of Clubs',
  'Lafayette',
  'Lightning Rod',
  'Loom',
  'Lycanthrope, Form of The Adventurer',
  'Lycanthrope II, Form of The Gatherer',
  'Maaliskuu',
  'Macrowave',
  'Magic Mirror',
  'Midwinter Scepter',
  'Muffin Tree',
  'Nimaj Neb\'s Resplendent Mace',
  'OP Hammer',
  'Pile of Leaves',
  'Rain Bow',
  'Rainbowblade',
  'Slot Machine',
  'Small Blue Box',
  'Snow Shovel',
  'So',
  'Sooty-lure Fishing Pole',
  'Summer Bow',
  'Valentine Plushy',
  'Wand of Berries',
  'WeTH',
  'White PsyPod',
  'Windmill Blueprint',
  'Zodiac Tiger Plushy',
);

$itemname = $possible_items[array_rand($possible_items)];

add_inventory($user['user'], '', $itemname, 'Summoned by ' . $user['display'], $this_inventory['location']);

echo '<p>The wand flickers briefly, then vanishes, replaced by ' . item_text_link($itemname) . '!</p>';
?>
