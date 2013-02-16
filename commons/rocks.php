<?php
// remember: if you change this, you have to change the recycling components (and
// recycle_fractions) of the rock items
$ROCK_ITEMS = array(
  'Clay', 'Clay', 'Clay', 'Clay', // 26.6%
  'Iron', 'Iron', 'Iron',         // 20.0%
  'Copper', 'Copper',             // 13.3%
  'Tin', 'Tin',                   // 13.3%
  'Silver',                       //  6.6%
  'Gold',                         //  6.6%
  'Glass',                        //  6.6%
  'Zinc',                         //  6.6%
);

function GenerateItemsFromRocks($num_items)
{
  $itemnames = array();

  if($num_items > 0)
  {
    global $ROCK_ITEMS;

    for($i = 0; $i < $num_items; ++$i)
      $itemnames[] = $ROCK_ITEMS[array_rand($ROCK_ITEMS)];

    if(mt_rand(1, 120) == 1)
      $itemnames[] = 'Pyrestone';

    if(mt_rand(1, 240) == 1)
      $itemnames[] = 'Skull';

    // 480?

    if(mt_rand(1, 960) == 1)
      $itemnames[] = 'Crystal Skull';

    sort($itemnames);
  }

  return $itemnames;
}
?>
