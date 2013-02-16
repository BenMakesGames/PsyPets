<?php
$AQUARIUM_NEED_ITEMS = array(
  'Fish', 'Seaweed', 'Clay', 'Stringy Rope', 'Small Rock', 'Gold Fish Crackers',
  'Silver Fish Crackers', 'Paper Boat'
);

function get_aquarium_if_exists($userid)
{
  $command = 'SELECT * FROM psypets_aquariums WHERE userid=' . $userid . ' LIMIT 1';
  return fetch_single($command, 'fetching aquarium');
}

function get_aquarium($userid)
{
  $command = 'SELECT * FROM psypets_aquariums WHERE userid=' . $userid . ' LIMIT 1';
  $aquarium = fetch_single($command, 'fetching aquarium');
  
  if($aquarium === false)
  {
    $command = 'INSERT INTO psypets_aquariums (userid, king_name) VALUES (' . $userid . ', ' . quote_smart(random_king_name_for_aquarium()) . ')';
    fetch_none($command, 'creating aquarium');

    $command = 'SELECT * FROM psypets_aquariums WHERE userid=' . $userid . ' LIMIT 1';
    $aquarium = fetch_single($command, 'fetching aquarium');
    
    if($aquarium === false)
      die('Failed to create new aquarium!');
  }
  
  return $aquarium;
}

function random_king_name_for_aquarium()
{
  $names = array(
    'Albacore', 'Alfonsino', 'Ayu', 'Barramundi', 'Betta', 'Blackchin',
    'Blenny', 'Bluegill', 'Bocaccio', 'Bonito', 'Bowfin', 'Bristlemouth',
    'Burbot', 'Candiru', 'Capelin', 'Catalufa', 'Cichlid', 'Coelacanth',
    'Coley', 'Dace', 'Danio', 'Dory', 'Elver', 'Escolar', 'Eulachon',
    'Flagfin', 'Garpike', 'Goby', 'Gouramie', 'Greeneye', 'Grunion', 'Gudgeon',
    'Menhaden', 'Gurnard', 'Haddock', 'Hake', 'Halosaur', 'Hoki', 'Ide',
    'Kahawai', 'Kaluga', 'Kokanee', 'Kappy', 'Lagena', 'Lenok', 'Longfin',
    'Louvar', 'Luderick', 'Lyretail', 'Mahseer', 'Marlin', 'Medaka', 'Mojarra',
    'Mooneye', 'Morwong', 'Mrigal', 'Mummichog', 'Muskellunge', 'Panga',
    'Pearleye', 'Pilchard', 'Píntano', 'Pleco', 'Pomfret', 'Porgy', 'Powen',
    'Rasbora', 'Ray', 'Redfin', 'Ridgehead', 'Rohu', 'Ronquil', 'Sábalo',
    'Sauger', 'Sculpin', 'Silverside', 'Sixgill', 'Splitfin', 'Sturgeon',
    'Taimen', 'Tang', 'Tapetail', 'Tarpon', 'Tui', 'Turbot', 'Vendance',
    'Wallago', 'Whiting', 'Wrasse', 'Yellowtail', 'Yellowfin', 'Zander', 'Ziege',
    'Zingel'
  );

  return $names[array_rand($names)];
}

function clear_aquarium_reward(&$aquarium)
{
  $command = 'UPDATE psypets_aquariums SET next_reward=\'\',happy=\'no\' WHERE idnum=' . $aquarium['idnum'] . ' LIMIT 1';
  fetch_none($command, 'clearing reward item');

  $aquarium['next_reward'] = '';
  $aquarium['happy'] = 'yes';
}

function reset_aquarium_needed_item(&$aquarium)
{
  global $AQUARIUM_NEED_ITEMS;

  $need = $AQUARIUM_NEED_ITEMS[array_rand($AQUARIUM_NEED_ITEMS)];

  $command = '
    UPDATE psypets_aquariums
    SET
      item_needed=' . quote_smart($need) . '
    WHERE idnum=' . $aquarium['idnum'] . '
    LIMIT 1
  ';
  fetch_none($command, 'resetting aquarium\'s needed item');
  
  $aquarium['item_needed'] = $need;
}

function dismiss_aquarium(&$aquarium)
{
  global $AQUARIUM_NEED_ITEMS;

  $need = $AQUARIUM_NEED_ITEMS[array_rand($AQUARIUM_NEED_ITEMS)];

  $when = time() + 44 * 60 * 60;

  $command = '
    UPDATE psypets_aquariums
    SET
      trouble_time=' . $when . ',
      item_needed=' . quote_smart($need) . ',
      happy=\'no\'
    WHERE idnum=' . $aquarium['idnum'] . '
    LIMIT 1
  ';
  fetch_none($command, 'helping aquarium');

  $aquarium['trouble_time'] = $when;
  $aquarium['item_needed'] = $need;
  $aquarium['happy'] = 'no';
}

function help_aquarium(&$aquarium, $first_time = false)
{
  global $AQUARIUM_NEED_ITEMS;

  $need = $AQUARIUM_NEED_ITEMS[array_rand($AQUARIUM_NEED_ITEMS)];

  $reward_items = array('Coral Ring', 'Nacrehammer', 'Tsunami Cloak');
  
  if($first_time)
  {
    $reward = '';
    $when = 1;
  }
  else
  {
    $reward = $reward_items[array_rand($reward_items)];
    $when = time() + 20 * 60 * 60;
  }
  
  $command = '
    UPDATE psypets_aquariums
    SET
      trouble_time=' . $when . ',
      item_needed=' . quote_smart($need) . ',
      next_reward=' . quote_smart($reward) . ',
      happy=\'yes\'
    WHERE idnum=' . $aquarium['idnum'] . '
    LIMIT 1
  ';
  fetch_none($command, 'helping aquarium');
  
  $aquarium['trouble_time'] = $when;
  $aquarium['item_needed'] = $need;
  $aquarium['next_reward'] = $reward;
  $aquarium['happy'] = 'yes';
}
?>
