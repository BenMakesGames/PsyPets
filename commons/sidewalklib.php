<?php
// row name => array(0: itemname, 1: req. production)
$sidewalk_harvestables = array(
  'brokenglass' => array('Broken Glass', 53),
  'dandelion' => array('Common Dandelion', 100),
  'emptycan' => array('Empty Can', 138),
  'clay' => array('Clay', 169),
  'brandy' => array('Brandy', 40),
  'skate' => array('Skate', 815),
  'cellphone' => array('Cellphone', 1178),
  'moneys' => array('Some Moneys', 100),
);

$sidewalk_feeds = array(
  'Chalk' => 6,
  'Chalk Outline' => 8,
  'Flying Chalk Outline' => 11,
  'Happy Chalk Outline' => 11,
  'Angry Chalk Outline' => 11,
  'Seal of Vassago' => 12,
  'Small Rock' => 73,
  'Clay' => 169,
);

function get_sidewalk_by_user($userid)
{
  $command = 'SELECT * FROM psypets_sidewalks WHERE userid=' . $userid . ' LIMIT 1';
  return fetch_single($command, 'fetching sidewalk');
}

function create_sidewalk($userid)
{
  $command = 'INSERT INTO psypets_sidewalks (userid) VALUES (' . $userid . ')';
  fetch_none($command, 'initializing apiary');
}

function remove_sidewalk_pigeons(&$sidewalk)
{
  $sidewalk['pigeons'] = 0;
  
  $command = 'UPDATE psypets_sidewalks SET pigeons=0 WHERE idnum=' . $sidewalk['idnum'] . ' LIMIT 1';
  fetch_none($command, 'removing sidewalk pigeons');
}

function add_sidewalk_pigeons(&$sidewalk)
{
  $command = 'SELECT `makes` FROM monster_recipes WHERE availability=\'standard\' ORDER BY RAND() LIMIT 1';
  $data = fetch_single($command, 'fetching prepared food');
  
  $items = explode(',', $data['makes']);

  $command = 'SELECT idnum FROM monster_items WHERE itemname=' . quote_smart($items[0]) . ' LIMIT 1';
  $data = fetch_single($command, 'fetching food item');

  $sidewalk['pigeons'] = $data['idnum'];

  $command = 'UPDATE psypets_sidewalks SET pigeons=' . $data['idnum'] . ' WHERE idnum=' . $sidewalk['idnum'] . ' LIMIT 1';
  fetch_none($command, 'adding sidewalk pigeons');
}

function harvest_sidewalk(&$sidewalk, &$user, $key)
{
  global $sidewalk_harvestables;

  $amount = floor($sidewalk['progress_' . $key] / $sidewalk_harvestables[$key][1]);

  if($amount > 0)
  {
    $sidewalk['progress_' . $key] -= $amount * $sidewalk_harvestables[$key][1];

    $command = 'UPDATE psypets_sidewalks SET progress_' . $key . '=' . $sidewalk['progress_' . $key] . ' WHERE idnum=' . $sidewalk['idnum'] . ' LIMIT 1';
    fetch_none($command, 'harvesting from sidewalk');

    if($key != 'moneys')
    {
      for($x = 0; $x < $amount; ++$x)
        add_inventory($user['user'], '', $sidewalk_harvestables[$key][0], 'Harvested from ' . $user['display'] . '\'s Hungry Sidewalk', 'home');
    }
    else
    {
      $amount = mt_rand(75 * $amount, 125 * $amount);
      
      give_money($user, $amount, 'Found on your Hungry Sidewalk');
    }
  }

  return $amount;
}

function feed_sidewalk(&$sidewalk, $value)
{
  global $sidewalk_harvestables;

  while($value > 0)
  {
    if($value <= 5)
      $amount = $value;
    else
      $amount = mt_rand(5, min($value, 50));

    $value -= $amount;

    $key = array_rand($sidewalk_harvestables);

    $sidewalk['progress_' . $key] += $amount;
  }

  $command = 'UPDATE psypets_sidewalks SET ' .
               'progress_brokenglass=' . $sidewalk['progress_brokenglass'] . ', ' .
               'progress_dandelion=' . $sidewalk['progress_dandelion'] . ', ' .
               'progress_clay=' . $sidewalk['progress_clay'] . ', ' .
               'progress_skate=' . $sidewalk['progress_skate'] . ', ' .
               'progress_cellphone=' . $sidewalk['progress_cellphone'] . ', ' .
               'progress_emptycan=' . $sidewalk['progress_emptycan'] . ', ' .
               'progress_brandy=' . $sidewalk['progress_brandy'] . ', ' .
               'progress_moneys=' . $sidewalk['progress_moneys'] . ' ' .
             'WHERE userid=' . $sidewalk['userid'] . ' LIMIT 1';
  fetch_none($command, 'updating sidewalk');
}
?>
