<?php
$FARM_CROPS = array('Barley', 'Fluff', 'Hops', 'Rice', 'Rye', 'Wheat', 'Wild Oats');
$FARM_SILO_SIZE = 100;

// feed => (key => progress, ...)
$ALLOWED_CHICKEN_FEED = array(
  'Acorn' => array('gargantuan_egg' => 21), // uncommon
  'Appleberry Seeds' => array('egg' => 15, 'speckled_egg' => 11, 'blue_egg' => 5, 'gargantuan_egg' => 4), // has more-important uses
  'Baked Pumpkin Seeds' => array('egg' => 4, 'speckled_egg' => 2),
  'Bamboo Seeds' => array('gargantuan_egg' => 53, 'blue_egg' => 38, 'speckled_egg' => 20, 'feather' => 11, 'egg' => 78, 'phoenix_down' => 20, 'rubber_chicken' => 10), // bought at Florist
  'Barley' => array('egg' => 6, 'feather' => 8, 'phoenix_down' => 1),
  'Camomile Seeds' => array('egg' => 11, 'feather' => 7, 'rubber_chicken' => 2), // traded for at Greenhouse
  'Cardamom' => array('egg' => 9, 'blue_egg' => 10, 'rubber_chicken' => 1), // uncommon
  'Chrysanthemum Seeds' => array('gargantuan_egg' => 48, 'blue_egg' => 32, 'speckled_egg' => 30, 'feather' => 14, 'egg' => 70, 'phoenix_down' => 19, 'rubber_chicken' => 7), // bought at Florist
  'Coffee Beans' => array('blue_egg' => 10, 'feather' => 20),
  'Fire Spice' => array('egg' => 2, 'gargantuan_egg' => 5, 'phoenix_down' => 9), // has more-important uses
  'Hops' => array('speckled_egg' => 4, 'feather' => 9, 'phoenix_down' => 2),
  'Nutmeg' => array('speckled_egg' => 10, 'blue_egg' => 7, 'rubber_chicken' => 3), // uncommon
  'Orchid Seeds' => array('gargantuan_egg' => 42, 'blue_egg' => 23, 'speckled_egg' => 31, 'feather' => 11, 'egg' => 94, 'phoenix_down' => 18, 'rubber_chicken' => 11), // bought at Florist
  'Coriander' => array('rubber_chicken' => 2),
  'Pecans' => array('egg' => 5, 'speckled_egg' => 2),
  'Plum Pit' => array('gargantuan_egg' => 62, 'blue_egg' => 21, 'speckled_egg' => 11, 'feather' => 18, 'egg' => 78, 'phoenix_down' => 21, 'rubber_chicken' => 9), // bought at Florist
  'Poppy Seeds' => array('egg' => 12, 'speckled_egg' => 8), // uncommon
  'Raw Pumpkin Seeds' => array('egg' => 4),
  'Reed' => array('egg' => 9, 'speckled_egg' => 12), // has more-important uses
  'Rye' => array('egg' => 5, 'speckled_egg' => 5, 'blue_egg' => 5),
  'Sesame Seeds' => array('speckled_egg' => 9, 'gargantuan_egg' => 11), // uncommon
  'Sunflower Seeds' => array('egg' => 2, 'phoenix_down' => 2),
  'Wheat' => array('egg' => 11, 'speckled_egg' => 4),
  'Wild Oats' => array('egg' => 7, 'speckled_egg' => 8),
);

// key => (item, difficulty, always_visible)
$chicken_coop_harvestables = array(
  'egg' => array('Egg', 21, true), // food-value: 21
  'speckled_egg' => array('Speckled Egg', 25, true), // food-value: 21
  'blue_egg' => array('Blue Egg', 44, false), // food value: 44
  'gargantuan_egg' => array('Gargantuan Egg', 64, false), // food value: 64
  'feather' => array('Feather', 12, false),
  'phoenix_down' => array('Phoenix Down', 53, false),
  'rubber_chicken' => array('Rubber Chicken With a Pulley in the Middle', 96, false),
);

function get_farm_if_exists($userid)
{
  $command = 'SELECT * FROM psypets_farms WHERE userid=' . $userid . ' LIMIT 1';
  return fetch_single($command, 'fetching farm');
}

function get_farm($userid)
{
  $command = 'SELECT * FROM psypets_farms WHERE userid=' . $userid . ' LIMIT 1';
  $farm = fetch_single($command, 'fetching farm');
  
  if($farm === false)
  {
    $command = 'INSERT INTO psypets_farms (userid) VALUES (' . $userid . ')';
    fetch_none($command, 'creating farm');

    $command = 'SELECT * FROM psypets_farms WHERE userid=' . $userid . ' LIMIT 1';
    $farm = fetch_single($command, 'fetching farm');
    
    if($farm === false)
      die('Failed to create new farm!');
  }
  
  return $farm;
}

function allow_farming_in_farm(&$farm)
{
  $farm['field_active'] = 'yes';
  
  $command = 'UPDATE psypets_farms SET field_active=\'yes\' WHERE idnum=' . $farm['idnum'] . ' LIMIT 1';
  fetch_none($command, 'saving farm');
}

function disallow_farming_in_farm(&$farm)
{
  $farm['field_active'] = 'no';

  $command = 'UPDATE psypets_farms SET field_active=\'no\' WHERE idnum=' . $farm['idnum'] . ' LIMIT 1';
  fetch_none($command, 'saving farm');
}

function take_grain_from_farm(&$farm, $quantity)
{
  $farm['silo_quantity'] -= $quantity;

  $command = 'UPDATE psypets_farms SET silo_quantity=silo_quantity-' . $quantity . ' WHERE idnum=' . $farm['idnum'] . ' LIMIT 1';
  fetch_none($command, 'claiming grains');
}

function change_crop_in_farm(&$farm, $crop)
{
  $farm['field_crop'] = $crop;

  $command = 'UPDATE psypets_farms SET field_crop=' . quote_smart($crop) . ' WHERE idnum=' . $farm['idnum'] . ' LIMIT 1';
  fetch_none($command, 'saving farm');
}

function work_at_farm(&$farm, $skill)
{
  global $FARM_SILO_SIZE;

  $whole_grains = (int)($skill / 5);
  $partial_grains = $skill % 5;

  if($partial_grains >= mt_rand(1, 5))
    $whole_grains++;

  if($whole_grains < 1)
    return 0;

  if($whole_grains + $farm['silo_quantity'] > $FARM_SILO_SIZE)
  {
    $whole_grains = $FARM_SILO_SIZE - $farm['silo_quantity'];
    if($whole_grains < 1)
      return false;
  }

  $farm['silo_quantity'] += $whole_grains;

  $command = 'UPDATE psypets_farms SET silo_quantity=silo_quantity+' . $whole_grains . ' WHERE idnum=' . $farm['idnum'] . ' LIMIT 1';
  fetch_none($command, 'harvesting grains');

  return $whole_grains;
}

function feed_chickens_at_farm(&$farm, $itemname)
{
  global $ALLOWED_CHICKEN_FEED;

  $updates = array();

  foreach($ALLOWED_CHICKEN_FEED[$itemname] as $key=>$value)
  {
    $farm['coop_' . $key] += $value;
    $updates[] = 'coop_' . $key . '=coop_' . $key . '+' . $value;
  }

  $farm['coop_feed_time'] = time();
  $updates[] = 'coop_feed_time=' . $farm['coop_feed_time'];

  $command = 'UPDATE psypets_farms SET ' . implode(', ', $updates) . ' WHERE idnum=' . $farm['idnum'] . ' LIMIT 1';
  fetch_none($command, 'feeding farm chickens');
}

function collect_from_chickens_at_farm(&$farm, $item, $amount)
{
  $farm['coop_' . $item] -= $amount;

  $command = 'UPDATE psypets_farms SET coop_' . $item . '=coop_' . $item . '-' . $amount . ' WHERE idnum=' . $farm['idnum'] . ' LIMIT 1';
  fetch_none($command, 'collecting from farm chickens');
}

function put_clock_in_farm(&$farm)
{
  $command = 'UPDATE psypets_farms SET coop_has_timer=\'yes\' WHERE idnum=' . $farm['idnum'] . ' LIMIT 1';
  fetch_none($command, 'collecting from farm chickens');
}
?>
