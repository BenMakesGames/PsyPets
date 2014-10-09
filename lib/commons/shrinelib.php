<?php
// DO NOT RE-INDEX!
// candle and spell data is stored in database (by shrine) using these indicies
$CANDLE_LIST = array(
   1 => 'Amethyst Rose Candle',
   2 => 'Black Candle',
   3 => 'Blueberry Candle',
   4 => 'Brown Sugar Candle',
   5 => 'Candle',
  11 => 'Chocolate Candle',
   6 => 'Fire Spice Candle',
   7 => 'Mint Tea Candle',
   8 => 'Red Candle',
   9 => 'Silver Candle',
  12 => 'Vanilla Candle',
  10 => 'Yellow Candle',
);

$SPELL_DETAILS = array(
   1 => array(40, 'Blood Moon', 'bloodmoon.php'),
   2 => array(30, 'The Harvest', 'harvest.php'),
   3 => array(30, 'Feed the Flames', 'flames.php'),
   4 => array(5, 'Patience\'s Reward', 'plusexp.php'),
   5 => array(25, 'Sigil\'s Spectrum', 'sigils.php'),
   6 => array(30, 'Chocolate Chaos', 'cchaos.php'),
   7 => array(15, 'Dancing Light', 'love.php'),
   8 => array(20, 'Raise Dead', 'raisezombie.php'),
   9 => array(20, 'Summon Scroll', 'scroll.php'),
   10 => array(30, 'Imp Gate', 'imp.php'),
   11 => array(8, 'Angel\'s Choir', 'getback_gossamer.php'),
   12 => array(7, 'Fiery Reclamation', 'getback_pyrestone.php'),
   13 => array(6, 'Ferrum Vindicatum', 'getback_iron.php'),
   14 => array(5, 'Gimme Back Mah Tin!', 'getback_tin.php'),
   15 => array(4, 'Look Into The Void', 'getback_skull.php'),
);

$SPELL_LIST = array(
  '8,2,2,8' => 1,
  '10,*,4' => 2,
  '10,6,8' => 3,
  '12,5,7' => 4,
  '8,6,10,7,3,1' => 5,
  '11,*,*,11' => 6,
  '*,*,9' => 7,
  '5,2,12,2,5' => 8,
  '9,5,1' => 9,
  '2,8,*,8,2' => 10,
  '5,5,12' => 11,
  '1,6,6' => 12,
  '3,9,3,5' => 13,
  '2,10,2' => 14,
  '8,11,11,8' => 15,
);

function count_patterns($pattern, $sequence)
{
  $count = 0;

  while(preg_match('/' . $pattern . '/', $sequence))
  {
    $fragment = explode($pattern, $sequence);
    $trimsize = strlen($fragment[0]) + 1;
    $sequence = '*' . substr($sequence, $trimsize);

    $count++;
  }

  return $count;
}

function simulate_shrine($userid)
{
  $shrine = get_shrine_byuserid($userid);

  if($shrine === false)
    return;

  global $now;

  $hours = floor(($now - $shrine['lastcheck']) / (60 * 60));
  
  if($hours < 1)
    return 0;
  else if($hours > 24)
    $effective_hours = 24;
  else
    $effective_hours = $hours;

  if(strlen($shrine['candles']) > 0)
  {
    $candles = explode(';', $shrine['candles']);
    $spells = array();

    // take the spells apart into an array: spell id as key, progress as value
    if(strlen($shrine['spells']) > 0)
    {
      $spelldata = explode(';', $shrine['spells']);
      foreach($spelldata as $data)
      {
        $raw = explode(',', $data);
        $spells[$raw[0]] = $raw[1];
      }
    }

    while($effective_hours > 0)
    {
      for($x = 0; $x < 10; ++$x)
      {
        if(strlen($candles[$x]) > 0)
        {
          $candle = explode(',', $candles[$x]);

          $candle_list[] = $candle[0];

          if($candle[1] > 1)
            $candles[$x] = $candle[0] . ',' . ($candle[1] - 1);
          else
            $candles[$x] = '';
        }
        else
          $candle_list[] = '';
      }
      
      // find all the spells we're matching, and build them up
      $rituals = find_spells(implode(',', $candle_list));
      
      if(count($rituals) > 0)
      {
        foreach($rituals as $ritual=>$times)
          $spells[$ritual] += $times;
      }

      $effective_hours--;
    }
    
    $candle_data = implode(';', $candles);

    // put the spell array back together as a string
    if(count($spells) > 0)
    {
      $data = array();
      foreach($spells as $spell=>$time)
        $data[] = $spell . ',' . $time;

      $spell_data = implode(';', $data);
    }
    else
      $spell_data = '';
  }
  else
  {
    $candle_data = ';;;;;;;;;';
    $spell_data = $shrine['spells'];
  }

  $command = 'UPDATE psypets_shrines SET lastcheck=lastcheck+' . ($hours * 60 * 60) . ',candles=' . quote_smart($candle_data) . ',spells=' . quote_smart($spell_data) . ' WHERE userid=' . $shrine['userid'] . ' LIMIT 1';
  fetch_none($command, 'update shrine');
  
  return $hours;
}

function find_spells($spellstring)
{
  global $SPELL_LIST;
  
  $spell_matches = array();
  
  foreach($SPELL_LIST as $pattern=>$id)
  {
    $preg = str_replace('*', '[0-9]+', $pattern);
    $matches = count_patterns($preg, $spellstring);

    if($matches > 0)
      $spell_matches[$id] += $matches;
  }
  
  return $spell_matches;
}

function get_shrine_byuserid($userid)
{
  $command = 'SELECT * FROM psypets_shrines WHERE userid=' . $userid . ' LIMIT 1';
  return fetch_single($command, 'fetching resident\'s shrine');
}

function create_shrine($userid)
{
  global $now;

  $command = 'INSERT INTO psypets_shrines (userid, lastcheck) VALUES ' .
             '(' . $userid . ', ' . $now . ')';
  fetch_none($command, 'creating resident\'s shrine');
  
  return get_shrine_byuserid($userid);
}
