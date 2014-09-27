<?php
// row name => array(0: itemname, 1: req. apiary level, 2: req. production)
$apiary_harvestables = array(
  'sugar' => array('Sugar', 1, 10),
  'wax' => array('Wax', 1, 15),
  'pansy' => array('Pansy', 2, 10),
  'blueprint' => array('Apiary Blueprint', 3, 10),
  'wand' => array('Bee Wand', 5, 20),
  'royaljelly' => array('Royal Jelly', 7, 35),
  'honeycomb' => array('Honeycomb', 10, 50),
);

function get_apiary_byuser($userid)
{
  $command = 'SELECT * FROM psypets_apiaries WHERE userid=' . $userid . ' LIMIT 1';
  return fetch_single($command, 'fetching apiary');
}

function create_apiary($userid)
{
  $command = 'INSERT INTO psypets_apiaries (userid) VALUES (' . $userid . ')';
  $GLOBALS['database']->FetchNone($command, 'initializing apiary');
}

function apairy_exp_needed($level)
{
  return 75 + $level * 25;
}

function feed_apiary(&$apiary, $clover)
{
  if($clover == '3-Leaf Clover')
  {
    $apiary['progress_sugar'] += 3;
    $apiary['progress_wax'] += 1;

    if($apiary['level'] < 10)
      $apiary['experience'] += 5;
  }
  else if($clover == '4-Leaf Clover' || $clover == 'May Flower')
  {
    $apiary['progress_sugar'] += 4;
    $apiary['progress_wax'] += 2;

    if($apiary['level'] >= 2)
      $apiary['progress_pansy'] += 1;

    if($apiary['level'] < 10)
      $apiary['experience'] += 10;

    if($apiary['level'] >= 10)
      $apiary['progress_honeycomb'] += 1;
  }
  else if($clover == '5-Leaf Clover' || $clover == 'Poppy Flower' ||
    $clover == 'Sesame Flower' || $clover == 'Hyacinth')
  {
    $apiary['progress_sugar'] += 5;
    $apiary['progress_wax'] += 3;

    if($apiary['level'] >= 2)
      $apiary['progress_pansy'] += 2;

    if($apiary['level'] >= 3)
      $apiary['progress_blueprint'] += 1;

    if($apiary['level'] >= 7)
      $apiary['progress_royaljelly'] += 1;

    if($apiary['level'] >= 10)
      $apiary['progress_honeycomb'] += 2;

    if($apiary['level'] < 10)
      $apiary['experience'] += 15;
  }
  else if($clover == 'Amethyst Rose' || $clover == 'Sunflower' || $clover == 'Fire Spice Flower')
  {
    $apiary['progress_sugar'] += 3;
    $apiary['progress_wax'] += 2;

    if($apiary['level'] >= 5)
      $apiary['progress_wand'] += 2;

    if($apiary['level'] >= 7)
      $apiary['progress_royaljelly'] += 1;

    if($apiary['level'] < 10)
      $apiary['experience'] += 10; 
  }
  else if($clover == 'Pansy')
  {
    if($apiary['level'] < 10)
      $apiary['experience'] += 20; 
  }
  else if($clover == 'White Lotus')
  {
    if($apiary['level'] >= 3)
      $apiary['progress_blueprint'] += 3;

    if($apiary['level'] >= 5)
      $apiary['progress_wand'] += 2;

    if($apiary['level'] >= 7)
      $apiary['progress_royaljelly'] += 2;

    if($apiary['level'] < 10)
      $apiary['experience'] += 20; 
  }
  else if($clover == 'Black Lotus')
  {
    if($apiary['level'] >= 3)
      $apiary['progress_blueprint'] += 4;

    if($apiary['level'] >= 5)
      $apiary['progress_wand'] += 3;

    if($apiary['level'] >= 7)
      $apiary['progress_royaljelly'] += 4;

    if($apiary['level'] < 10)
      $apiary['experience'] += 40; 
  }
  else if($clover == 'Honeysuckle')
  {
    $apiary['progress_sugar'] += 1;

    if($apiary['level'] >= 5)
      $apiary['progress_wand'] += 1;

    if($apiary['level'] < 10)
      $apiary['experience'] += 2;
  }
  else if($clover == 'Arbutus' || $clover == 'Narcissus' || $clover == 'Periwinkle' ||
    $clover == 'Primrose' || $clover == 'Purple Lilac' || $clover == 'Scabious' ||
    $clover == 'Yellow Acacia' || $clover == 'White Lily' || $clover == 'Poinsettia')
  {
    $apiary['progress_wax'] += 1;

    if($apiary['level'] >= 5)
      $apiary['progress_wand'] += 1;

    if($apiary['level'] < 10)
      $apiary['experience'] += 2; 
  }
  else if($clover == 'Common Dandelion' || $clover == 'Camomile Flowers')
  {
    $apiary['progress_wax'] += 3;
    $apiary['progress_sugar'] += 3;

    if($apiary['level'] < 10)
      $apiary['experience'] += 10;
  }
  else if($clover == 'Cactus Flower')
  {
    $apiary['progress_sugar'] += 1;

    if($apiary['level'] >= 7)
      $apiary['progress_royaljelly'] += 1;

    if($apiary['level'] >= 10)
      $apiary['progress_honeycomb'] += 4;

    if($apiary['level'] < 10)
      $apiary['experience'] += 15;
  }

  while($apiary['experience'] >= apairy_exp_needed($apiary['level']))
  {
    $apiary['experience'] -= apairy_exp_needed($apiary['level']);
    $apiary['level']++;
  }

  // 1.5 hours, -10 minutes for every level above the first (max level: 4)
  $apiary['nextfeeding'] = time() + 90 * 60 - min(4, $apiary['level'] - 1) * 10;

  $command = 'UPDATE psypets_apiaries SET ' .
               'level=' . $apiary['level'] . ', ' .
               'experience=' . $apiary['experience'] . ', ' .
               'progress_sugar=' . $apiary['progress_sugar'] . ', ' .
               'progress_wax=' . $apiary['progress_wax'] . ', ' .
               'progress_pansy=' . $apiary['progress_pansy'] . ', ' .
               'progress_blueprint=' . $apiary['progress_blueprint'] . ', ' .
               'progress_wand=' . $apiary['progress_wand'] . ', ' .
               'progress_royaljelly=' . $apiary['progress_royaljelly'] . ', ' .
               'progress_honeycomb=' . $apiary['progress_honeycomb'] . ', ' .
               'nextfeeding=' . $apiary['nextfeeding'] . ' ' .
             'WHERE userid=' . $apiary['userid'] . ' LIMIT 1';
  $GLOBALS['database']->FetchNone($command, 'updating apiary');
}
?>
