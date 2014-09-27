<?php
require_once 'commons/questlib.php';

$PATTERN_MOVEMENT_SEQUENCE_REWARDS = array(
  'NNNEES' => 'Strange Bow',
  'UWDNWN' => 'Strange Bit',
  'NESWWW' => 'Strange Pin'
);

function generate_maze_piece($name)
{
  switch($name)
  {
    case 'Maze Piece (E)': return '1011';
    case 'Maze Piece (ES)': return '1001';
    case 'Maze Piece (ESW)': return '1000';
    case 'Maze Piece (EW)': return '1010';
    case 'Maze Piece (N)': return '0111';
    case 'Maze Piece (NE)': return '0011';
    case 'Maze Piece (NES)': return '0001';
    case 'Maze Piece (NESW)': return '0000';
    case 'Maze Piece (NEW)': return '0010';
    case 'Maze Piece (NS)': return '0101';
    case 'Maze Piece (NSW)': return '0100';
    case 'Maze Piece (NW)': return '0110';
    case 'Maze Piece (S)': return '1101';
    case 'Maze Piece (SW)': return '1100';
    case 'Maze Piece (W)': return '1110';
    default: return false;
  }
}

function maze_move_user(&$user, $loc)
{
  $command = 'UPDATE monster_users SET mazeloc=' . $loc . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  fetch_none($command, 'moving player in maze');

  if($user['mazeloc'] > 0)
  {
    $command = 'UPDATE psypets_maze SET players=players-1 WHERE idnum=' . $user['mazeloc'] . ' LIMIT 1';
    fetch_none($command, 'decrementing player count on previous maze tile');
  }

  $command = 'UPDATE psypets_maze SET players=players+1 WHERE idnum=' . $loc . ' LIMIT 1';
  fetch_none($command, 'incrementing player count on current maze tile');

  $user['mazeloc'] = $loc;
}

function get_random_obstacle()
{
  global $user;
  
  if($user['idnum'] > 0)
    $extra = 'AND monster_users.idnum!=' . (int)$user['idnum'];
  else
    $extra = '';
    

  $possible_items = fetch_multiple('
    SELECT monster_inventory.forsale AS min_price,monster_items.itemname
    FROM
      monster_inventory
      JOIN monster_users
      JOIN monster_items
    WHERE
      monster_inventory.user=monster_users.user
      AND monster_items.itemname=monster_inventory.itemname
      AND monster_inventory.forsale>0
      AND monster_users.openstore=\'yes\'
      AND monster_items.can_pawn_with=\'yes\'
      AND monster_items.can_recycle=\'yes\'
      ' . $extra . '
    ORDER BY RAND()
    LIMIT 6
  ');

  $pick = false;
  
  foreach($possible_items as $item)
  {
    if($pick === false || $item['min_price'] < $pick['value'])
      $pick = array('value' => $item['min_price'], 'item' => $item['itemname']);
  }
    
  return $pick['item'];
}

function add_gate($x, $y, $z)
{
  $owners = array(
    'The Rabbit\'s', 'The Dragon\'s', 'Keresaspa\'s',
    'Rizi Vizi\'s', 'Gizubi\'s', 'Kaera\'s', 'Ki Ri Kashu\'s', 'Mercury\'s',
    'The Fox\'s', 'The Desikh\'s', 'Pleiades\'', 'The Serpent\'s',
    'The Philosopher\'s', 'The Fairy\'s', 'The Wizard\'s'
  );
  $states = array(
    'Third', 'First', 'Second', 'Last', 'Penultimate',
    'Glittering', 'Ruined', 'Blessed', 'Cursed', 'Original', 'New',
    'Forgotten', 'Lost', 'Enchanted', 'Mythic'
  );
  $adjectives = array(
    'Clover', 'Ribbon', 'Yogurt', 'Rose', 'Lotus', 'Blood',
    'Cinnamon', 'Mushroom', 'Chocolate', 'Coconut', 'Bronze', 'Donut', 'Moon',
    'Gossamer', 'Fire Spice', 'Fluff', 'Gold', 'Taffy', 'Cherub', 'Paper',
    'Gemuline', 'Gemerald', 'Giamond', 'Lemon', 'Lilac', 'Mango', 'NUL',
    'Orichalcum', 'Pearl', 'Pyrestone', 'Rice', 'Smoke', 'Sugar', 'Sun',
    'Turnip', 'Vector', 'Vinegar', 'Wheat'
  );
  
  $gate_name = $owners[array_rand($owners)] . ' ' .
    $states[array_rand($states)] . ' ' .
    $adjectives[array_rand($adjectives)] . ' Gate';

  $command = 'INSERT INTO psypets_maze_gates (name, x, y, z) VALUES ' .
    '(' . quote_smart($gate_name) . ', ' . $x . ', ' . $y . ', ' . $z . ')';
  fetch_none($command, 'adding gate record');
}

function add_maze($x, $y, $z, $tile)
{
  global $EASTER;

  if(mt_rand(1, 2500) == 1) // 1 gate/50x50 area
  {
    $feature = 'gate';
    $obstacle = 'none';
    
    add_gate($x, $y, $z);
  }
  else if(mt_rand(1, 1225) == 1) // 1 shop/35x35 area
  {
    $feature = 'shop';
    $obstacle = 'none';
  }
  else
  {
    $feature = 'none';
    $obstacle = get_random_obstacle();
  }

  $t = mt_rand(1, 100);

  if($tile == '1111')
  {
    $obstacle = 'none';
    $treasure = '';
  }
  else if($t == 1)              // 1% chance
    $treasure = 'Patently-Rare And Valuable Treasure';

  else if($t == 2)              // 1% chance
    $treasure = 'Wand of Wonder';

  else if($t == 3)              // 1% chance
    $treasure = 'Stack of Tiles';

  else if($t >= 4 && $t <= 7)   // 4% chance
    $treasure = 'Adventure';

  else if($t == 8)              // 1% chance
    $treasure = 'Bag of Rupees';
    
  else if($t >= 9 && $t <= 14)  // 6% chance
    $treasure = 'Key Ring';

  else if($t >= 15 && $t <= 19) // 5% chance
  {
    $stones = array('Sardonyx', 'Ruby', 'Sapphire', 'Opal', 'Topaz',
      'Turquoise', 'Garnet', 'Amethyst', 'Bloodstone');
    $treasure = $stones[array_rand($stones)];
  }

  else if($t >= 20 && $t <= 24) // 5% chance
    $treasure = 'Orc Totem';

  else if($t >= 25 && $t <= 30) // 6% chance
    $treasure = 'Prospector\'s Shovel';

  else if($t >= 31 && $t <= 34) // 4% chance
    $treasure = 'Conductor\'s Baton';
    
  else if($t >= 35 && $t <= 37) // 3% chance
    $treasure = 'Incredible Healing Potion';

  else if($t >= 38 && $t <= 39) // 2% chance
    $treasure = 'Magic Voucher';

  else if($t >= 40 && $t <= 45) // 6% chance
    $treasure = 'Vanilla Candle';

  else if($t >= 46 && $t <= 53) // 8% chance
    $treasure = 'Dice Bag';

  else if($t >= 54 && $t <= 58) // 5% chance
    $treasure = 'Clairvoyance Scroll';

  else if($t >= 59 && $t <= 62) // 4% chance
    $treasure = 'Scroll of Local Teleportation';

  else if($t >= 63 && $t <= 66) // 4% chance
    $treasure = 'Moneys Pouch';

  else if($t == 67)             // 1% chance
    $treasure = 'Edelweiss';

  else if($t == 68)             // 1% chance
    $treasure = 'Dark Matter';

  else if($t == 69)             // 1% chance
    $treasure = 'Bronze';

  else if($t == 70)             // 1% chance
    $treasure = 'Bag of Tricks';

  else if($t == 71)             // 1% chance
    $treasure = 'Shouldered Tablet Headstone';

  else if($t == 72)             // 1% chance
    $treasure = 'Domed Tablet Headstone';

  else if($t == 73)             // 1% chance
    $treasure = 'Amethyst Rose Bush';

  else if($t == 74)             // 1% chance
    $treasure = 'Appleberry Bush';

  else if($t >= 75 && $t <= 80) // 6% chance
  {
    $tiles = array('East Wind', 'South Wind', 'West Wind', 'North Wind');
    $treasure = $tiles[array_rand($tiles)];
  }
  else                          // 20% chance
    $treasure = 'Maze Piece Summoning Scroll';

  if(mt_rand(1, 10) <= $EASTER)
    $treasure = 'Easter Basket';

  $command = 'INSERT INTO psypets_maze (`x`, `y`, `z`, `tile`, `treasure`, `obstacle`, `feature`) ' .
             "VALUES ($x, $y, $z, '" . $tile . "', " . quote_smart($treasure) . ', ' . quote_smart($obstacle) . ', ' . quote_smart($feature) . ')';
  fetch_none($command, 'add_maze');

  return $GLOBALS['database']->InsertID();
}

function get_maze_bycoord($x, $y, $z)
{
  return fetch_single('SELECT * FROM psypets_maze WHERE x=' . (int)$x . ' AND y=' . (int)$y . ' AND z=' . (int)$z . ' LIMIT 1');
}

function get_maze_byid($tileid)
{
  return fetch_single('SELECT * FROM psypets_maze WHERE idnum=' . $tileid . ' LIMIT 1');
}

function maze_clear_tile($idnum)
{
  fetch_none('UPDATE psypets_maze SET obstacle=\'none\' WHERE idnum=' . $idnum . ' LIMIT 1');
}

function maze_add_ladder_up($idnum)
{
  fetch_none('UPDATE psypets_maze SET feature=\'ladder_up\' WHERE idnum=' . (int)$idnum . ' LIMIT 1');
}

function maze_add_ladder_down($idnum)
{
  fetch_none('UPDATE psypets_maze SET feature=\'ladder_down\' WHERE idnum=' . (int)$idnum . ' LIMIT 1');
}

function maze_create_random_empty_tile_with_feature($x, $y, $z, $feature = 'none')
{
  do
  {
    $tile = '';
    for($i = 0; $i < 4; ++$i)
      $tile .= mt_rand(0, 1);
  } while($tile == '1111');
    
  fetch_none('
    INSERT INTO psypets_maze
    (
      x, y, z,
      tile,
      feature
    )
    VALUES
    (
      ' . (int)$x . ', ' . (int)$y . ', ' . (int)$z . ',
      \'' . $tile . '\',
      ' . quote_smart($feature) . '
    )
  ');
  
  return $GLOBALS['database']->InsertID();
}

function maze_create_tile_with_ladder_down($x, $y, $z)
{
  return maze_create_random_empty_tile_with_feature($x, $y, $z, 'ladder_down');
}

function maze_create_tile_with_ladder_up($x, $y, $z)
{
  return maze_create_random_empty_tile_with_feature($x, $y, $z, 'ladder_up');
}

function book_code_maze_pattern($user)
{
  $translations = array('8' => 'N', '9' => 'U', '4' => 'W', '6' => 'E', '2' => 'S', '3' => 'D');
  
  $code = book_code_number($user);
  
  $pattern = '';
  
  for($i = 0; $i < strlen($code); ++$i)
    $pattern .= $translations[$code[$i]];
    
  return $pattern;
}

function award_maze_movement_sequence(&$user, $sequence)
{
  global $PATTERN_MOVEMENT_SEQUENCE_REWARDS;
  
  $rewards = $PATTERN_MOVEMENT_SEQUENCE_REWARDS;

  // add crazy, per-user book code pattern
  $rewards[book_code_maze_pattern($user)] = 'Teeny-tiny Book Key';
  
  if(array_key_exists($sequence, $rewards))
  {
    $reward = $rewards[$sequence];
    
    $quest_value_name = 'Got ' . $reward . ' from The Pattern';

    $quest_value = get_quest_value($user['idnum'], $quest_value_name);
    if($quest_value === false)
    {
      add_quest_value($user['idnum'], $quest_value_name, 1);
      add_inventory($user['user'], '', $reward, 'Found in The Pattern', 'storage/incoming');
    }
  }
}
?>
