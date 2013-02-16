<?php
$chassis = array(
  'Dugout Canoe' => array('seats' => 1),
  'Pink Canoe' => array('seats' => 1),
  'Black Swan Boat' => array('seats' => 1),
  'Giant Peach' => array(),
  'Ornithopter' => array('movement' => array('direction' => 'line', 'distance' => 1)),
  'Cardboard Box' => array('armor' => -2),
  'Coffin' => array(),
  'Sled' => array(),
  'Swan Boat' => array('seats' => 1),
  'Traveler\'s Wagon' => array('armor' => 2),
  'Couch' => array('armor' => -2, 'seats' => 2),
  'Leather Couch' => array('armor' => -2, 'seats' => 2),
  'Olive Couch' => array('armor' => -2, 'seats' => 2),
  'Sunshine Couch' => array('armor' => -2, 'seats' => 2),
  'Large Paper Boat' => array('armor' => -3, 'passives' => array(array('effect' => 'anchor', 'power' => -1)), 'seats' => 1),
);

$parts = array(
  // testing
  '~debugging item' => array('power' => -7, 'armor' => 1),

  'Balloons' => array('movement' => array('direction' => 'diagonal', 'distance' => 1)),
//  'Liquid Nitrogen' => array('propulsion' => 4, 'special' => 1),
  'Propeller' => array('movement' => array('direction' => 'line', 'distance' => 1), 'power' => -1),
  'Large Propeller' => array('movement' => array('direction' => 'line', 'distance' => 2), 'power' => -2),
  'Oar Array' => array('movement' => array('direction' => 'knight', 'distance' => 3, 'power' => -4)),
  'Jet Engine' => array('movement' => array('direction' => 'line', 'distance' => 4, 'power' => -6)),
  'Hovercart' => array('armor' => 1, 'movement' => array('direction' => 'line', 'distance' => 1), 'passives' => array(array('effect' => 'anchor', 'power' => -1)), 'power' => -2),
  'Magic Broomstick' => array('movement' => array('direction' => 'line', 'distance' => 2, 'no-los' => true), 'mana' => -3),

  // difficulty to invent = (power + 5) / 2
  'Steam-Powered LumberBot' => array('seats' => -1, 'power' => 12, 'attack' => 4),
  'Sour Lime-powered Clock' => array('power' => 6),
  'Thermo-Magnetic Motor' => array('power' => 14),
  '4-Cylinder Combustion Engine' => array('power' => 19),
  'Reciprocating Steam Engine' => array('power' => 27),
  '6-Cylinder Combustion Engine' => array('power' => 24),
  'Inauspicious Inductor' => array('mana' => -3, 'power' => 5, 'attack' => 1),
//  'Tandem Compound Steam Engine' => array('power' => 25),
//  'Steam Turbine Engine' => array('power' => 28),

  // mana!
  'The Eye of Horus' => array('mana' => 2),
  'Hungry Cherub (level 1)' => array('mana' => 4),
  'Hungry Cherub (level 2)' => array('mana' => 6),
  'Hungry Cherub (level 3)' => array('mana' => 7),
  'Sated Cherub' => array('mana' => 8),
  'Blue Crystal Ball' => array('mana' => 5),
  'Red Crystal Ball' => array('mana' => 5),
  'Obelisk' => array('mana' => 20, 'armor' => 1),
  'Electrum-Crowned Obelisk' => array('mana' => 22, 'armor' => 1),

  // both!
  'Sparkling Doodilly' => array('power' => 4, 'mana' => 5, 'armor' => 1),

  // attack, defense, special bonuses = ~80% power requirement
  // (remember to consider weight, which will slow speed, which will in turn require power)

  // attack
  'Hookshot' => array('attack' => array('direction' => 'diagonal', 'distance' => 2, 'effects' => array(array('target' => 'target', 'effect' => 'pull', 'power' => 1), array('target' => 'target', 'effect' => 'stun')))),
  'Robot Claw Prosthetic' => array('attack' => array('direction' => 'line', 'distance' => 1, 'damage' => 2)),
  'Battering Ram' => array('attack' => array('direction' => 'line', 'distance' => 1, 'damage' => 3)),
  'Shiny Thingamadig' => array('power' => -7, 'attack' => array('direction' => 'diagonal', 'distance' => 2, 'damage' => 3)),
  'Chainsaw' => array('power' => -2, 'attack' => array('direction' => 'line', 'distance' => 1, 'damage' => 2)),
  'Catapult' => array('attack' => array('direction' => 'knight', 'distance' => 3, 'damage' => 4, 'no-los' => true)),
  'Cannon' => array('attack' => array('direction' => 'line', 'distance' => array(1, 4), 'damage' => 4)),
  'Laser Phaser Stun Gun' => array('power' => -10, 'attack' => array('direction' => 'line', 'distance' => array(1, 10), 'damage' => 2)),
  'Maaliskuu' => array('power' => -14, 'mana' => -14, 'attack' => array('distance' => 0, 'aoe' => 2, 'damage' => 3, 'self-immune' => true)),

  // special
  'Anchor' => array('passives' => array(array('effect' => 'anchor', 'power' => 1))),
  'Disco Ball' => array('power' => -1, 'attack' => array('distance' => 0, 'aoe' => 1, 'self-immune' => true, 'effects' => array(array('target' => 'target', 'effect' => 'push', 'power' => 1)))),
  'Heavy Anchor' => array('propulsion' => -5, 'passives' => array(array('effect' => 'anchor', 'power' => 10))),
  'EMP Array' => array('power' => -16, 'armor' => 3),
//  'PSYCHE' => array('power' => -14, 'attack' => 5, 'defense' => 5, 'special' => 4),  

  // defense
  'Gossamer' => array('mana' => -4, 'armor' => 1),
  'Dark Gossamer' => array('mana' => -7, 'armor' => 2),
  'Iron Plating' => array('armor' => 2),
  'Fog Machine' => array('power' => -2, 'passives' => array(array('effect' => 'immune-to-cc'))),
  'Forcefield Generator' => array('power' => -30, 'armor' => 4),

  // seats
  'Comfy Chair' => array('seats' => 2, 'armor' => 1),
  'Gaudy Chair' => array('seats' => 1, 'armor' => 2),
  'Lawn Chair' => array('seats' => 1),
  'Leather Chair' => array('seats' => 1, 'armor' => 3),
  'Sunshine Chair' => array('seats' => 1, 'armor' => 2),
  'Tie Dye Chair' => array('seats' => 1, 'armor' => 2),
  'Office Chair' => array('seats' => 1),
  'Pilot\'s Seat' => array('seats' => 1),
  'Small Couch' => array('seats' => 2, 'armor' => 2),
  'Small Blue Couch' => array('seats' => 2, 'armor' => 2),
  'Hammock' => array('seats' => 2),
  'Couch' => array('seats' => 3, 'armor' => 4),
  'Leather Couch' => array('seats' => 3, 'armor' => 4),
  'Olive Couch' => array('seats' => 3, 'armor' => 4),
  'Sunshine Couch' => array('seats' => 3, 'armor' => 4),
);  

$spell_names = array(
  'winds' => 'Favorable Winds',
  'rainfire' => 'Rain of Fire',
  'executive' => 'Executive Decision',
  'shield' => 'Shield and Horn',
  'rowan' => 'Strength of the Rowan',
  'buffalo' => 'Buffalo Buffalo',
  'crosswinds' => 'Crosswinds',
  'stasis' => 'Stasis Bubble',
  'spelljam' => 'Spelljam',
  'entropy' => 'Entropy Curse',
  'reflect' => 'Reflection',
  'rot' => 'Rot',
  'rust' => 'Rust',
  'shroud' => 'Shrouding Mist',
  'pressure' => 'Pressure',
  'gum' => 'Gum',
  'mismagnetic' => 'Mismagnetic Charm',
);

function airship_can_attack(&$airship)
{
  global $now;

  return($airship['disabled'] == 'no' && $airship['propulsion'] > 0 && $airship['crewids'] != '' && $airship['mana'] >= 0 && $airship['power'] >= 0 && $airship['seats'] >= 1 || $airship['returntime'] > $now);
}

function airship_link(&$airship)
{
  return '<a href="/myhouse/addon/airshipprofile.php?idnum=' . $airship['idnum'] . '">' . $airship['name'] . '</a>';
}

function airship_record_win(&$airship)
{
  $airship['wins']++;
  
  $command = 'UPDATE psypets_airships SET wins=wins+1 WHERE idnum=' . $airship['idnum'] . ' LIMIT 1';
  fetch_none($command, 'updating win count for ship #' . $airship['idnum']);
}

function airship_record_loss(&$airship)
{
  $airship['losses']++;
  
  $command = 'UPDATE psypets_airships SET losses=losses+1 WHERE idnum=' . $airship['idnum'] . ' LIMIT 1';
  fetch_none($command, 'updating win loss for ship #' . $airship['idnum']);
}

function apply_airship_crew_bonus(&$airship, $useruser)
{
  $bonus = airship_crew_linear_bonus($airship, $useruser);
/*
  $airship['attack'] += $bonus['attack'];
  $airship['defense'] += $bonus['defense'];
  $airship['special'] += $bonus['special'];
  $airship['propulsion'] += $bonus['propulsion'];
*/
}

function airship_crew_bonus(&$airship, $useruser)
{
  $ret = array();

  if(strlen($airship['crewids']) > 0)
  {
    $crew = explode(',', $airship['crewids']);

    foreach($crew as $petid)
    {
      $bonus = airship_pet_bonus($petid, $useruser);
      foreach($bonus as $stat=>$value)
      {
        if($stat != 'message')
          $ret[$stat] += $value;
      }
    }
  }

  return $ret;
}

function airship_crew_linear_bonus(&$airship, $useruser)
{
  $bonuses = airship_crew_bonus($airship, $useruser);

  $final = array();

  foreach($bonuses as $stat=>$bonus)
    $final[$stat] = floor($airship[$stat] * $bonus / 100);

  return $final;
}

function count_crew(&$airship)
{
  if(strlen($airship['crewids']) == 0)
    return 0;
  else
    return substr_count($airship['crewids'], ',') + 1;
}

function render_pet_bonuses($bonuses)
{
  $display = array();

  foreach($bonuses as $key=>$bonus)
  {
    if($key == 'message')
      $display[] = $bonus;
    else
      $display[] = '+' . $bonus . '% ' . $key;
  }
  
  return implode(', ', $display);
}

function airship_pet_bonus_direct(&$pet, $useruser)
{
  if($pet['user'] != $useruser)
    $ret['message'] = 'This pet does not belong to you.';
  else if($pet['dead'] != 'no' || $pet['zombie'] == 'yes')
    $ret['message'] = 'This pet is dead.';
  else if($pet['changed'] == 'yes')
    $ret['message'] = 'This pet is in wereform!';
  else
  {
    srand($pet['idnum']);
/*
    $ret['attack'] = ceil($pet['bra'] * 2 + $pet['str'] * 1.5 + $pet['pil'] + rand(-3, 3));
    $ret['defense'] = ceil($pet['smi'] * 2 + $pet['sta'] * 1.5 + $pet['pil'] + rand(-3, 3));
    $ret['special'] = ceil($pet['wit'] * 2 + $pet['stealth'] * 1.5 + $pet['pil'] + rand(-3, 3));
    $ret['propulsion'] = ceil($pet['eng'] * 2 + $pet['dex'] * 1.5 + $pet['pil'] + rand(-3, 3));
*/
    mt_srand();
  }

  return $ret;
}

function airship_pet_bonus($petid, $useruser)
{
  $pet = get_pet_byid($petid);

  return airship_pet_bonus_direct($pet, $useruser);
}

function new_airship($ownerid, $name, $item)
{
  global $chassis;

  if(!array_key_exists($item, $chassis))
    return false;

  $details = get_item_byname($item);

  if($details === false)
    return false;

  $seats = $chassis[$item]['seats'];
/*
  $attack = $chassis[$item]['attack'];
  $defense = $chassis[$item]['defense'];
  $special = $chassis[$item]['special'];
  $propulsion = $chassis[$item]['propulsion'];
*/
  $maxbulk = blimp_size($details['bulk']) * 10;
  $weight = $details['weight'];

  $command = 'INSERT INTO psypets_airships (ownerid, name, seats, weight, maxbulk, chassis) VALUES ' .
    '(' . $ownerid . ', ' . quote_smart($name) . ', ' . $seats . ', ' .
    $weight . ', ' . $maxbulk . ', ' . quote_smart($item) . ')';

  fetch_none($command, 'creating new airship');

  return true;
}

function get_airship_by_id($id)
{
  $command = 'SELECT * FROM psypets_airships WHERE idnum=' . (int)$id . ' LIMIT 1';
  return fetch_single($command, 'fetching airship #' . $id);
}

function set_airship_crew(&$airship, &$petids)
{
  $airship['crewids'] = implode(',', $petids);
  $command = 'UPDATE psypets_airships SET crewids=' . quote_smart($airship['crewids']) . ' WHERE idnum=' . $airship['idnum'] . ' LIMIT 1';
  fetch_none($command, 'updating airship crew');
}

function airship_time(&$airship, $bonus = 0)
{
  $speed = airship_speed($airship, $bonus);

  if($speed == 'none')
    return $speed;
  else
    return duration($speed, 2);
}

function airship_speed(&$airship, $bonus = 0)
{
  $total_propulsion = $airship['propulsion'] + $bonus;

  if($total_propulsion <= 0)
    return 'none';

  $weight = ($airship['weight'] / 30) - ($total_propulsion * 2);
  if($weight < -30)
    $weight = -30;

  return floor(60 * (60 + $weight));
}

function blimp_size($bulk)
{
  return floor(log($bulk * 6 / 10) * 12) + 10;
}

function spell_count(&$pvp)
{
  return count(take_apart(',', $pvp['spells']));
}

function apply_spell_bonuses(&$pvp, &$airship, &$defender)
{
/*
  if(strlen($pvp['spells']) > 0)
  {
    $spells = take_apart(',', $pvp['spells']);
    
    foreach($spells as $spell)
    {
      if($spell == 'winds')             // 4.8
        $airship['propulsion'] += 8;
      else if($spell == 'rainfire')     // 12.5 (15)
      {
        $airship['attack'] += 3;
        $airship['defense']--;
      }
      else if($spell == 'executive')    // 5, 10, 15...
      {
        $parts = take_apart(',', $airship['parts']);
        $chairs = 0;
        foreach($parts as $part)
        {
          if($part == 'Office Chair')
            $chairs++;
        }
        
        $airship['special'] += $chairs;
      }
      else if($spell == 'gum')
      {
        if(strlen($defender['parts']) > 0)
        {
          $ship_parts = explode(',', $defender['parts']);

          foreach($ship_parts as $part)
          {
            if($part == 'Propeller' || $part == 'Large Propeller' || $part == 'Jet Engine')
              $airship['propulsion'] += 5;
          }
        }
      }
      else if($spell == 'pressure')
      {
        $airship['defense']--;

        if(strlen($defender['parts']) > 0)
        {
          $ship_parts = explode(',', $defender['parts']);

          foreach($ship_parts as $part)
          {
            if($part == 'Balloons')
              $airship['defense']++;
          }
        }
      }
      else if($spell == 'shroud')
      {
        $matches = 0;
      
        if(strlen($defender['parts']) > 0)
        {
          $ship_parts = explode(',', $defender['parts']);

          foreach($ship_parts as $part)
          {
            if($part == 'Blue Crystal Ball' || $part == 'Red Crystal Ball' ||
              $part == 'Disco Ball' || $part == 'Maaliskuu')
              $matches++;
          }
        }
        
        if($matches >= 2)
        {
          $airship['attack'] += 2;
          $airship['defense'] += 2;
          $airship['special'] += 2;
        }
      }
      else if($spell == 'mismagnetic')
      {
        if(strlen($defender['parts']) > 0)
        {
          $ship_parts = explode(',', $defender['parts']);

          foreach($ship_parts as $part)
          {
            if($part == 'Thermo-Magnetic Motor' || $part == 'PSYCHE')
              $airship['attack']++;
            else if($part == 'Inauspicious Inductor' || $part == 'EMP Array')
              $airship['special']++;
          }
        }
      }
      else if($spell == 'shield')       // 10
      {
        $airship['defense']++;
        $airship['attack']++;
      }
      else if($spell == 'rowan')        // 10
        $airship['defense'] += 2;
      else if($spell == 'buffalo')      // 6.5
      {
        $airship['attack']++;
        $airship['propulsion'] += 2;
      }
      else if($spell == 'crosswinds')   // 9.5
      {
        $airship['attack']++;
        $airship['special']++;
        $airship['propulsion'] -= 5;
        
        if($defender['chassis'] == 'Ornithopter')
          $airship['defense']++;
      }
      else if($spell == 'stasis')       // 10 (15)
      {
        $airship['defense'] += 3;
        $airship['attack']--;
        $airship['special']--;
      }
      else if($spell == 'spelljam')     // 30
      {
        if(strlen($defender['parts']) > 0)
        {
          global $parts;
        
          $ship_parts = explode(',', $defender['parts']);

          foreach($ship_parts as $part)
          {
            foreach($parts[$part] as $stat=>$bonus)
            {
              if($stat == 'mana' && $bonus > 0)
              {
                $airship['defense']++;
                $airship['attack']++;
                $airship['special']++;
              }
            }
          }
        }
      } // spell: spelljam
      else if($spell == 'entropy')      // 30
      {
        if(strlen($defender['parts']) > 0)
        {
          global $parts;
        
          $ship_parts = explode(',', $defender['parts']);

          foreach($ship_parts as $part)
          {
            foreach($parts[$part] as $stat=>$bonus)
            {
              if($stat == 'power' && $bonus > 0)
              {
                $airship['defense']++;
                $airship['attack']++;
                $airship['special']++;
              }
            }
          }
        }
      } // spell: entropy curse
      else if($spell == 'reflect')      // 20
      {
        if(strlen($defender['parts']) > 0)
        {
          global $parts;
        
          $ship_parts = explode(',', $defender['parts']);

          foreach($ship_parts as $part)
          {
            $power = false;
            $attack = false;
          
            foreach($parts[$part] as $stat=>$bonus)
            {
              if(($stat == 'mana' || $stat == 'power') && $bonus < 0)
                $powered = true;
              else if($stat == 'attack' && $bonus > 0)
                $attack = true;
            }
            
            if($powered && $attack)
            {
              $airship['defense'] += 1;
              $airship['special'] += 1;
            }
          }
        }
      } // spell: reflection
      else if($spell == 'rot')          // 12.5
      {
        if($defender['chassis'] == 'Giant Peach')
        {
          $airship['attack']++;
          $airship['defense']++;
          $airship['special']++;
        }
      }
      else if($spell == 'rust')         // 17.5
      {
        if(strlen($defender['parts']) > 0)
        {
          $ship_parts = explode(',', $defender['parts']);

          foreach($ship_parts as $part)
          {
            if($part == 'Iron Plating')
              $airship['attack']++;
          }
        }
        
        if($defender['chassis'] == 'Traveler\'s Wagon')
          $airship['attack']++;
      }
    } // for each spell
  } // if you have any spells readied
*/
}

function list_spells(&$pvp)
{
/*
  if(strlen($pvp['spells']) > 0)
  {
    $spells = take_apart(',', $pvp['spells']);
    
    foreach($spells as $spell)
    {
      if($spell == 'winds')
        echo '<li>Favorable Winds (+8 propulsion)</li>';
      else if($spell == 'rainfire')
        echo '<li>Rain of Fire (+3 attack, -1 defense)</li>';
      else if($spell == 'executive')
        echo '<li>Executive Decision (+1 special for each Office Chair)</li>';
      else if($spell == 'gum')
        echo '<li>Gum (+5 propulsion for each Propeller, Large Propeller, or Jet Engine on defending ship)</li>';
      else if($spell == 'mismagnetic')
        echo '<li>Mismagnetic Charm (+1 attack for each Thermo-Magnetic Motor or PSYCHE on defending ship; +1 special for each EMP Array or Inauspicious Inductor on defending ship)</li>'; 
      else if($spell == 'pressure')
        echo '<li>Pressure (-1 defense, +1 defense for each Balloons on defending ship)</li>';
      else if($spell == 'shield')
        echo '<li>Shield and Horn (+1 defense, +1 attack)</li>';
      else if($spell == 'shroud')
        echo '<li>Shrouding Mist (+2 to each stat if defending ship has two or more Blue Crystal Balls, Red Crystal Balls, Disco Balls, and/or Maaliskuus)</li>';
      else if($spell == 'rowan')
        echo '<li>Strength of the Rowan (+2 defense)</li>';
      else if($spell == 'buffalo')
        echo '<li>Buffalo Buffalo (+1 attack, +2 propulsion)</li>';
      else if($spell == 'crosswinds')
        echo '<li>Crosswinds (+1 attack, +1 special, -5 propulsion)</li>';
      else if($spell == 'stasis')
        echo '<li>Stasis Bubble (+3 defense, -1 attack, -1 special)</li>';
      else if($spell == 'spelljam')
        echo '<li>Spelljam (+1 to each stat for each Mana-generating part on defending ship)</li>';
      else if($spell == 'entropy')
        echo '<li>Entropy Curse (+1 to each stat for each Power-generating part on defending ship)</li>';
      else if($spell == 'reflect')
        echo '<li>Reflection (+1 defense and +1 special for each Power or Mana-consuming part that boosts attack on defending ship)</li>';
      else if($spell == 'rot')
        echo '<li>Rot (+1 to each stat if defending ship has a Giant Peach chassis)</li>';
      else if($spell == 'rust')
        echo '<li>Rust (+1 attack for each Iron Plating on defending ship; +1 attack if defending ship has a Traveler\'s Wagon chassis)</li>';
      else
        echo '<li>undefined spell "' . $spell . '"</li>';
    }
  }
*/
}

function list_spell_bonuses(&$pvp, &$airship)
{
/*
  if(strlen($pvp['spells']) > 0)
  {
    $spells = take_apart(',', $pvp['spells']);
    
    foreach($spells as $spell)
    {
      if($spell == 'winds')
        echo '<li>Favorable Winds: +8 propulsion</li>';
      else if($spell == 'rainfire')
        echo '<li>Rain of Fire: +3 attack, -1 defense</li>';
      else if($spell == 'executive')
      {
        $parts = take_apart(',', $airship['parts']);
        $chairs = 0;
        foreach($parts as $part)
        {
          if($part == 'Office Chair')
            $chairs++;
        }
    
        echo '<li>Executive Decision: +1 special &times; number of Office Chairs = +' . $chairs . ' special</li>';
      }
      else if($spell == 'gum')
        echo '<li>Gum: +1 special for each Propeller, Large Propeller, or Jet Engine on defending ship</li>';
      else if($spell == 'mismagnetic')
        echo '<li>Mismagnetic Charm: +1 attack for each Thermo-Magnetic Motor or PSYCHE on defending ship; +1 special for each EMP Array or Inauspicious Inductor on defending ship</li>'; 
      else if($spell == 'pressure')
        echo '<li>Pressure: -1 defense, +1 defense for each Balloons on defending ship</li>';
      else if($spell == 'shield')
        echo '<li>Shield and Horn: +1 defense, +1 attack</li>';
      else if($spell == 'rowan')
        echo '<li>Strength of the Rowan: +2 defense</li>';
      else if($spell == 'buffalo')
        echo '<li>Buffalo Buffalo: +1 attack, +2 propulsion</li>';
      else if($spell == 'crosswinds')
        echo '<li>Crosswinds: +1 attack, +1 special, -5 propulsion</li>';
      else if($spell == 'shroud')
        echo '<li>Shrouding Mist: +2 to each stat if defending ship has two or more Blue Crystal Balls, Red Crystal Balls, Disco Balls, and/or Maaliskuus</li>';
      else if($spell == 'stasis')
        echo '<li>Stasis Bubble: +3 defense, -1 attack, -1 special</li>';
      else if($spell == 'spelljam')
        echo '<li>Spelljam: +1 to each stat for each Mana-generating part on defending ship</li>';
      else if($spell == 'entropy')
        echo '<li>Entropy Field: +1 to each stat for each Power-generating part on defending ship</li>';
      else if($spell == 'reflect')
        echo '<li>Reflection: +2 defense and +2 special for each Power or Mana-consuming part that boosts attack on defending ship</li>';
      else if($spell == 'rot')
        echo '<li>Rot: +1 to each stat if defending ship has a Giant Peach chassis</li>';
      else if($spell == 'rust')
        echo '<li>Rust: +1 attack for each Iron Plating on defending ship; +1 attack if defending ship has a Traveler\'s Wagon chassis</li>';
      else
        echo '<li>undefined spell "' . $spell . '"</li>';
    }
  }
*/
}

function has_any_spell_bonuses(&$pvp)
{
  return(strlen($pvp['spells']) > 0);
}

function render_airship_bonuses_as_list_xhtml($bonuses)
{
  $items = array();

  foreach($bonuses as $type=>$details)
  {
    switch($type)
    {
      case 'power':
        $items[] = ($details >= 0 ? '+' : '') . $details . ' Power';
        break;
      case 'mana':
        $items[] = ($details >= 0 ? '+' : '') . $details . ' Mana';
        break;
      case 'armor':
        $items[] = ($details >= 0 ? '+' : '') . $details . ' Armor';
        break;
      case 'seats':
        $items[] = ($details >= 0 ? '+' : '') . $details . ' Seat' . ($details == 1 || $details == -1 ? '' : 's');
        break;
      case 'movement':
        $items[] = 'Movement: <img src="//' . $SETTINGS['static_domain'] . '/gfx/airship/move-' . $details['direction'] . '-' . $details['distance'] . '.png" class="inlineimage" />';
        break;
      default:
        $items[] = '&lt;undefined&gt;';
        break;
    }
  }
  
  if(count($items))
    return '<li>' . implode('</li><li>', $items) . '</li>';
  else
    return '';
}
?>
