<?php
$CLOUD_GRAPHICS = array(
  'cloud/cloud_1.png',
  'cloud/cloud_2.png',
  'cloud/cloud_3.png',
  'cloud/cloud_4.png',
  'cloud/cloud_5.png',
  'cloud/cloud_6.png',
  'cloud/cloud_7.png',
);

$GALAXY_GRAPHICS = array(
  'spiral' => array('galaxy/spiral_1.png', 'galaxy/spiral_2.png', 'galaxy/spiral_3.png', 'galaxy/spiral_4.png', 'galaxy/spiral_5.png'),
  'elliptical' => array('galaxy/elliptical_1.png', 'galaxy/elliptical_2.png', 'galaxy/elliptical_3.png'),
);

$STAR_GRAPHICS = array(
  'unary' => array('stars/unary_1.png', 'stars/unary_2.png'),
  'binary' => array('stars/binary_1.png'),
  'blackhole' => array('stars/blackhole_1.png'),
  'blackhole_binary' => array('stars/blackhole_binary_1.png'),
);

$STAR_TYPE_NAMES = array(
  'dwarf' => 'Dwarf',
  'reddwarf' => 'Red Dwarf',
  'whitedwarf' => 'White Dwarf',
  'blackhole' => 'Black Hole',
);

function RomanNumber($num)
{
  // Make sure that we only use the integer portion of the value
  $n = intval($num);
  $result = '';

  // Declare a lookup array that we will use to traverse the number:
  $lookup = array(
    'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,
    'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,
    'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1
  );

  foreach ($lookup as $roman => $value)
  {
    // Determine the number of matches
    $matches = intval($n / $value);

    // Store that many characters
    $result .= str_repeat($roman, $matches);

    // Substract that from the number
    $n = $n % $value;
  }

  // The Roman numeral should be built, return it
  return $result;
}

function get_universe_by_id($idnum)
{
  $command = 'SELECT * FROM psypets_universes WHERE idnum=' . $idnum . ' LIMIT 1';
  return fetch_single($command, 'fetching universe #' . $idnum);
}

function get_universe($userid)
{
  $command = 'SELECT * FROM psypets_universes WHERE ownerid=' . $userid . ' LIMIT 1';
  return fetch_single($command, 'fetching universe for player #' . $userid);
}

function get_galaxy($objectid)
{
  $command = 'SELECT * FROM psypets_galactic_objects WHERE idnum=' . $objectid . ' LIMIT 1';
  return fetch_single($command, 'fetching galaxy/cloud #' . $objectid);
}

function habitability_description($class)
{
  switch($class)
  {
    case 'deadly': return 'deadly';
    case 'desolate': return 'desolate';
    case 'habitable': return 'habitable';
    case 'eden': return 'green';
    default: return '???';
  }
}

function population_description($population)
{
  if($population == 0)
    return 'unknown';
  else if($population < 1000)
    return $population . ' million';
  else
    return round($population / 1000, 1) . ' billion';
}

function life_description($life)
{
  switch($life)
  {
    case 0: return 'no life';
    case 1: return 'single-celled organisms';
    case 2: return 'aquatic creatures';
    case 3: return 'land-dwelling creatures';
    case 4: return 'sentient creatures';
    case 5: return 'civilization';
    default: return '???';
  }
}

function get_solar_system_object($objectid)
{
  $command = 'SELECT * FROM psypets_planets WHERE idnum=' . $objectid . ' LIMIT 1';
  return fetch_single($command, 'fetching system object #' . $objectid);
}


function create_asteroid_belt($universeid, &$system, $x)
{
  $command = '
    INSERT INTO psypets_planets
    (universeid, systemid, `type`, `class`, size, name, x, y, image, image_size)
    VALUES
    (
      ' . $universeid . ',
      ' . $system['idnum'] . ', \'belt\', \'deadly\', 0,
      ' . quote_smart($name) . ', ' . $x . ', 0,
      \'belt.png\', 8
    )
  ';
  fetch_none($command, 'inserting asteroid belt');

  $planetid = $GLOBALS['database']->InsertID();

  $command = '
    UPDATE psypets_stellar_objects
    SET object_count=object_count+1
    WHERE idnum=' . $system['idnum'] . '
    LIMIT 1
  ';
  fetch_none($command, 'updating system object count');

  $command = '
    UPDATE psypets_universes
    SET total_object_count=total_object_count+1
    WHERE idnum=' . $universeid . '
    LIMIT 1
  ';
  fetch_none($command, 'updating universe object count');

  $system['object_count']++;

  log_universe_event($universeid, 'The ' . $name . ' Asteroid Belt was born in <a href="universe_viewsystem.php?id=' . $system['idnum'] . '">' . $system['name'] . '</a>.');

  return $planetid;
}

function create_rocky_planet($universeid, &$system, $x, $y, $rocks)
{
  $life = ($x >= 20 && $x <= 196 + $rocks * 2);
  $water = ($x >= 48 + $rocks && $x <= 83 + $rocks);

  if($rocks == 2)
    $size = mt_rand(25, 200);
  else
  {
    $average = (($rocks - 2) * 5 + 1) * 100;
    $size = mt_rand($average - $rocks * 75, $average + $rocks * 75);
  }

  if($life)
  {
    if($water)
      $class = 'eden';
    else
      $class = 'habitable';

    $life_level = 1 + (mt_rand(1, 20) == 1 ? 1 : 0) + (mt_rand(1, 50) == 1 ? 1 : 0);
  }
  else
  {
    if($x < 10)
      $class = 'deadly';
    else
      $class = 'desolate';

    $life_level = 0;
  }

  // 8, 12, or 16, based on number of rocks
  $image_size = ceil(($rocks - 1) / 4) * 4 + 4;

  if($water)
    $colors = array('blue', 'green', 'brown');
  else
    $colors = array('silver', 'red', 'gray', 'brown');

  $color = $colors[array_rand($colors)];
  
  $image = $image_size . '_' . $color . '.png';

  $name = $system['name'] . ' ' . RomanNumber($system['object_count'] + 1);

  $command = '
    INSERT INTO psypets_planets
    (universeid, systemid, `type`, `class`, size, life, name, x, y, image, image_size)
    VALUES
    (
      ' . $universeid . ',
      ' . $system['idnum'] . ', \'planet\', \'' . $class . '\', ' . $size . ',
      ' . $life_level . ', ' . quote_smart($name) . ', ' . $x . ', ' . $y . ',
      ' . quote_smart($image) . ', ' . $image_size . '
    )
  ';
  fetch_none($command, 'inserting planet');
  
  $planetid = $GLOBALS['database']->InsertID();
  
  $command = '
    UPDATE psypets_stellar_objects
    SET object_count=object_count+1
    WHERE idnum=' . $system['idnum'] . '
    LIMIT 1
  ';
  fetch_none($command, 'updating system object count');

  $command = '
    UPDATE psypets_universes
    SET total_object_count=total_object_count+1
    WHERE idnum=' . $universeid . '
    LIMIT 1
  ';
  fetch_none($command, 'updating universe object count');

  $system['object_count']++;

  log_universe_event($universeid, 'The planet ' . $name . ' was born in <a href="universe_viewsystem.php?id=' . $system['idnum'] . '">' . $system['name'] . '</a>.');

  return $planetid;
}

function create_comet($universeid, &$system, $x, $y)
{
}

// size: 1-5
function create_gas_giant($universeid, &$system, $x, $y, $gas)
{
//  $life = ($x >= 20 + $gas * 5 && $x <= 200 + $gas * 10);

  $average = $gas * 500 - 400;
  $size = mt_rand($average - 300, $average + 300);

  if($x < 10 + $gas * 5)
    $class = 'deadly';
  else
    $class = 'desolate';

  // 32, 36, 40, 44, or 48
  $image_size = $gas * 4 + 28;

  $colors = array('blue', 'green', 'red', 'yellow', 'white', 'purple');

  $color = $colors[array_rand($colors)];

  $image = $image_size . '_gas_' . $color . '.png';

  $name = $system['name'] . ' ' . RomanNumber($system['object_count'] + 1);

  $command = '
    INSERT INTO psypets_planets
    (universeid, systemid, `type`, `class`, size, name, x, y, image, image_size)
    VALUES
    (
      ' . $universeid . ',
      ' . $system['idnum'] . ', \'giant\', \'' . $class . '\', ' . $size . ',
      ' . quote_smart($name) . ', ' . $x . ', ' . $y . ',
      ' . quote_smart($image) . ', ' . $image_size . '
    )
  ';
  fetch_none($command, 'inserting gas giant');

  $planetid = $GLOBALS['database']->InsertID();

  $command = '
    UPDATE psypets_stellar_objects
    SET object_count=object_count+1
    WHERE idnum=' . $system['idnum'] . '
    LIMIT 1
  ';
  fetch_none($command, 'updating system object count');

  $command = '
    UPDATE psypets_universes
    SET total_object_count=total_object_count+1
    WHERE idnum=' . $universeid . '
    LIMIT 1
  ';
  fetch_none($command, 'updating universe object count');

  $system['object_count']++;

  log_universe_event($universeid, 'The planet ' . $name . ' was born in <a href="universe_viewsystem.php?id=' . $system['idnum'] . '">' . $system['name'] . '</a>.');

  return $planetid;
}

function get_universe_galactic_objects(&$universe)
{
  $command = '
    SELECT *
    FROM psypets_galactic_objects
    WHERE universeid=' . $universe['idnum'] . '
    LIMIT ' . $universe['galactic_object_count'] . '
  ';
  return fetch_multiple($command, 'fetching galactic objects');
}

function create_universe($userid)
{
  $command = '
    INSERT INTO psypets_universes (ownerid, lastupdate)
    VALUES (' . $userid . ', ' . time() . ')
  ';
  fetch_none($command, 'creating universe');

  $universe = get_universe($userid);
  
  log_universe_event($universe['idnum'], 'Your universe has entered the Inflation stage of development.');

  return $universe;
}

function universe_galaxy_space_is_empty($galaxyid, $x, $y)
{
  if($x >= 300 - 48 && $x <= 300 + 48 && $y >= 300 - 48 && $y <= 300 + 48)
    return false;

  $command = '
    SELECT idnum
    FROM psypets_stellar_objects
    WHERE
      galaxyid=' . $galaxyid . ' AND
      x>=' . ($x - 48) . ' AND
      x<=' . ($x + 48) . ' AND
      y>=' . ($y - 48) . ' AND
      y<=' . ($y + 48) . '
    LIMIT 1
  ';
  $overlapping_object = fetch_single($command, 'fetching overlapping stellar object');

  return($overlapping_object === false);
}

function universe_space_is_empty($universeid, $x, $y)
{
  $command = '
    SELECT idnum
    FROM psypets_galactic_objects
    WHERE
      universeid=' . $universeid . ' AND
      x>=' . ($x - 48) . ' AND
      x<=' . ($x + 48) . ' AND
      y>=' . ($y - 48) . ' AND
      y<=' . ($y + 48) . '
    LIMIT 1
  ';
  $overlapping_object = fetch_single($command, 'fetching overlapping galactic object');
  
  return($overlapping_object === false);
}

function solar_system_belt_is_clear($systemid, $x)
{
  $command = '
    SELECT idnum
    FROM psypets_planets
    WHERE
      systemid=' . $systemid . ' AND
      x+image_size>=' . ($x - 8) . ' AND
      x-image_size<=' . ($x + 8) . '
    LIMIT 1
  ';
  $overlapping_object = fetch_single($command, 'fetching overlapping solar objects');

  return($overlapping_object === false);
}

function solar_system_space_contains_belt($systemid, $x, $size)
{
  $command = '
    SELECT idnum
    FROM psypets_planets
    WHERE
      systemid=' . $systemid . ' AND
      `type`=\'belt\' AND
      x+8>=' . ($x - $size) . ' AND
      x-8<=' . ($x + $size) . '
    LIMIT 1
  ';
  $overlapping_object = fetch_single($command, 'fetching overlapping belts');

  return($overlapping_object !== false);
}

function solar_system_space_is_empty($systemid, $x, $y, $size)
{
  $command = '
    SELECT idnum
    FROM psypets_planets
    WHERE
      systemid=' . $systemid . ' AND
      `type`!=\'belt\' AND
      x+image_size>=' . ($x - $size) . ' AND
      x-image_size<=' . ($x + $size) . ' AND
      y+image_size>=' . ($y - $size) . ' AND
      y-image_size<=' . ($y + $size) . '
    LIMIT 1
  ';
  $overlapping_object = fetch_single($command, 'fetching overlapping planet/comet');

  return($overlapping_object === false && !solar_system_space_contains_belt($systemid, $x, $size));
}

function age_star($starid)
{
}

function random_star_mass($nova = false)
{
  $a = mt_rand(1, 50);

  // ranges from 75 to 3200; /100 = sun masses
  $mass = (pow($a, 6) / 500000000 + 0.1) * 100;

  if(mt_rand(1, 1000) == 1)
    $mass = mt_rand(3000, mt_rand(5000, 12000));

  if($nova && $mass < 900)
    $mass += 900;

  return $mass;
}

// 250+ solar masses
function supernova_no_explosion($universeid, $galaxyid, $systemid, $starid)
{
}

// 130-250 solar masses
function supernova_pair_instability($universeid, $galaxyid, $systemid, $starid)
{
}

// 10-130 solar masses
function supernova_normal($universeid, $galaxyid, $systemid, $starid)
{
  $command = '
    DELETE FROM psypets_solar_system_objects
    WHERE systemid=' . $systemid . '
  ';
  fetch_none($command, 'deleting objects in solar system');
  
  $deleted = $GLOBALS['database']->AffectedRows();
  
  $command = '
    DELETE FROM psypets_stars
    WHERE
      systemid=' . $systemid . ' AND
      idnum!=' . $starid . '
  ';
  fetch_none($command, 'deleting other stars in solar system');

  $deleted += $GLOBALS['database']->AffectedRows();
  
  if($deleted > 0)
  {
    $command = '
      UPDATE psypets_universes
      SET
        total_object_count=total_object_count-' . $deleted . '
      WHERE idnum=' . $universeid . '
      LIMIT 1
    ';
    fetch_none($command, 'updating universal quantities');
  }
  
  $command = '
    UPDATE psypets_stars
    SET
      type=\'blackhole\',
      mass=' . mt_rand(138, 146) . '
    WHERE idnum=' . $starid . '
    LIMIT 1
  ';
  fetch_none($command, 'changing star to blackhole');
}

function create_solar_system($universeid, $galaxyid, $x, $y, $nova = false)
{
  global $STAR_GRAPHICS;

  $name = universe_random_name();

  $star_mass = random_star_mass($nova);

  if($star_mass >= 7000)
    $type = 'hypergiant';
  else if($star_mass >= 1000)
    $type = 'supergiant';
  else if($star_mass >= 40)
  {
    if(mt_rand(1, 10) == 1)
      $type = 'giant';
    else if(mt_rand(1, 20) == 1 && $star_mass < 140)
    {
      $type = 'whitedwarf';
      $star_mass = ceil($star_mass * 0.95);
    }
    else
      $type = 'dwarf';
  }
  else
    $type = 'reddwarf';

  $image = $STAR_GRAPHICS['unary'][array_rand($STAR_GRAPHICS['unary'])];

  $now = time();

  $command = '
    INSERT INTO psypets_stellar_objects
    (universeid, galaxyid, image, creationdate, name, x, y)
    VALUES
    (
      ' . $universeid . ',
      ' . $galaxyid . ', \'' . $image . '\', ' . $now . ',
      ' . quote_smart($name) . ', ' . $x . ', ' . $y . '
    )
  ';
  fetch_none($command, 'creating solar system');
  
  $system_id = $GLOBALS['database']->InsertID();
  
  $command = '
    INSERT INTO psypets_stars
    (universeid, systemid, `type`, mass, name, creationdate)
    VALUES
    (
      ' . $universeid . ',
      ' . $system_id . ', \'' . $type . '\', ' . $star_mass . ',
      ' . quote_smart($name) . ', ' . $now . '
    )
  ';
  fetch_none($command, 'creating star');

  $star_id = $GLOBALS['database']->InsertID();

  $command = '
    UPDATE psypets_galactic_objects
    SET
      solar_system_count=solar_system_count+1
    WHERE idnum=' . $galaxyid . '
    LIMIT 1
  ';
  fetch_none($command, 'updating galaxy quantities');

  // 1 object: a star
  $command = '
    UPDATE psypets_universes
    SET
      total_object_count=total_object_count+1
    WHERE idnum=' . $universeid . '
    LIMIT 1
  ';
  fetch_none($command, 'updating universal quantities');

  log_universe_event($universeid, 'The ' . $name . ' System has been created.');

  return array($system_id, $star_id);
}

function get_solar_system_stars(&$system)
{
  $command = '
    SELECT *
    FROM psypets_stars
    WHERE systemid=' . $system['idnum'] . '
    LIMIT ' . $system['star_count'] . '
  ';
  return fetch_multiple($command, 'fetching solar system stars');
}

function get_solar_system_objects(&$system)
{
  $command = '
    SELECT *
    FROM psypets_planets
    WHERE systemid=' . $system['idnum'] . '
    LIMIT ' . $system['object_count'] . '
  ';
  return fetch_multiple($command, 'fetching solar system objects');
}

function universe_place_nova($universeid, $galaxyid, $x, $y)
{
  $command = '
    UPDATE psypets_universes
    SET
      supernova=supernova-1
    WHERE idnum=' . $universeid . '
    LIMIT 1
  ';
  fetch_none($command, 'updating universal quantities');

  list($systemid, $starid) = create_solar_system($universeid, $galaxyid, $x, $y, true);
  supernova($universeid, $galaxyid, $systemid, $starid);
}

function universe_place_spiral_galaxy($universeid, $x, $y)
{
  global $GALAXY_GRAPHICS;

  $graphic = $GALAXY_GRAPHICS['spiral'][array_rand($GALAXY_GRAPHICS['spiral'])];

  $name = universe_random_name();

  $command = '
    UPDATE psypets_universes
    SET
      galactic_object_count=galactic_object_count+1,
      total_object_count=total_object_count+1
    WHERE idnum=' . $universeid . '
    LIMIT 1
  ';
  fetch_none($command, 'updating universal quantities');

  if($GLOBALS['database']->AffectedRows() == 0)
    return;

  $command = '
    INSERT INTO psypets_galactic_objects
    (universeid, `type`, image, x, y, name, creationdate)
    VALUES
    (
      ' . $universeid . ', \'spiral\', \'' . $graphic . '\',
      ' . $x . ', ' . $y . ', ' . quote_smart($name) . ',
      ' . time() . '
    )
  ';
  fetch_none($command, 'creating cloud');

  log_universe_event($universeid, 'The ' . $name . ' Spiral Galaxy was created.');
}

function universe_gain(&$universe, $currency, $amount)
{
  $universe[$currency] += $amount;

  $command = '
    UPDATE psypets_universes
    SET
      ' . $currency . '=' . $currency . '+' . $amount . '
    WHERE idnum=' . $universe['idnum'] . '
    LIMIT 1
  ';
  fetch_none($command, 'updating universe currency');
}

function universe_spend(&$universe, $currency, $amount)
{
  if($amount >= $universe[$currency])
  {
    $universe[$currency] = 0;

    $command = '
      UPDATE psypets_universes
      SET
        ' . $currency . '=0
      WHERE idnum=' . $universe['idnum'] . '
      LIMIT 1
    ';
  }
  else
  {
    $universe[$currency] -= $amount;

    $command = '
      UPDATE psypets_universes
      SET
        ' . $currency . '=' . $currency . '-' . $amount . '
      WHERE idnum=' . $universe['idnum'] . '
      LIMIT 1
    ';
  }
  
  fetch_none($command, 'updating universe currency');
}

function universe_place_cloud($universeid, $x, $y)
{
  global $CLOUD_GRAPHICS;
  
  $graphic = $CLOUD_GRAPHICS[array_rand($CLOUD_GRAPHICS)];

  $name = universe_random_name();

  $command = '
    UPDATE psypets_universes
    SET
      galactic_object_count=galactic_object_count+1,
      total_object_count=total_object_count+1
    WHERE idnum=' . $universeid . '
    LIMIT 1
  ';
  fetch_none($command, 'updating universal quantities');

  if($GLOBALS['database']->AffectedRows() == 0)
    return;

  $command = '
    INSERT INTO psypets_galactic_objects
    (universeid, `type`, image, x, y, name, creationdate)
    VALUES
    (
      ' . $universeid . ', \'cloud\', \'' . $graphic . '\',
      ' . $x . ', ' . $y . ', ' . quote_smart($name) . ',
      ' . time() . '
    )
  ';
  fetch_none($command, 'creating cloud');

  log_universe_event($universeid, 'The ' . $name . ' Cloud was created.');
}

function galaxy_type(&$object)
{
  if($object['type'] == 'cloud')
    return 'cloud';
  else if($object['type'] == 'spiral')
    return 'galaxy';
  else if($object['type'] == 'spiral_agn')
    return 'galaxy';
  else if($object['type'] == 'elliptical')
    return 'galaxy';
  else if($object['type'] == 'elliptical_agn')
    return 'galaxy';
  else
    return '???';
}

function planet_type(&$object)
{
  switch($object['type'])
  {
    case 'planet': return 'planet';
    case 'giant': return 'gas giant';
    case 'comet': return 'comet';
    case 'belt': return 'asteroid belt';
  }
}

function galaxy_full_name(&$object)
{
  if($object['type'] == 'cloud')
    return $object['name'] . ' Cloud';
  else if($object['type'] == 'spiral')
    return $object['name'] . ' Spiral Galaxy';
  else if($object['type'] == 'spiral_agn')
    return $object['name'] . ' Spiral Galaxy (AGN)';
  else if($object['type'] == 'elliptical')
    return $object['name'] . ' Elliptical Galaxy';
  else if($object['type'] == 'elliptical_agn')
    return $object['name'] . ' Elliptical Galaxy (AGN)';
  else
    return $object['name'] . ' ???';
}

function spend_hydrogen(&$universe, $hydrogen)
{
  if($universe['hydrogen'] <= $hydrogen)
  {
    $universe['hydrogen'] = 0;

    $command = '
      UPDATE psypets_universes
      SET hydrogen=0
      WHERE idnum=' . $universe['idnum'] . '
      LIMIT 1
    ';
  }
  else
  {
    $universe['hydrogen'] -= $hydrogen;
  
    $command = '
      UPDATE psypets_universes
      SET hydrogen=hydrogen-' . $hydrogen . '
      WHERE idnum=' . $universe['idnum'] . '
      LIMIT 1
    ';
  }

  fetch_none($command, 'spending hydrogen');
}

function solar_system_full_name(&$system)
{
  return $system['name'] . ' System';
}

function get_solar_system($systemid)
{
  $command = '
    SELECT *
    FROM psypets_stellar_objects
    WHERE idnum=' . $systemid . '
    LIMIT 1
  ';
  return fetch_single($command, 'fetching system #' . $systemid);
}

function get_solar_systems(&$galaxy)
{
  $command = '
    SELECT *
    FROM psypets_stellar_objects
    WHERE galaxyid=' . $galaxy['idnum'] . '
    LIMIT ' . $galaxy['solar_system_count'] . '
  ';
  return fetch_multiple($command, 'fetching stellar objects');
}

function feed_galaxy_hydrogen(&$galaxy, $hydrogen)
{
  if($galaxy['type'] == 'spiral_agn' || $galaxy['type'] == 'elliptical_agn')
    $hydrogen = floor($hydrogen * 1.2);

  $galaxy['stardust'] += $hydrogen;
  
  $command = '
    UPDATE psypets_galactic_objects
    SET stardust=stardust+' . $hydrogen . '
    WHERE idnum=' . $galaxy['idnum'] . '
    LIMIT 1
  ';
  fetch_none($command, 'feeding hydrogen');
}

function update_universe(&$universe)
{
  global $UNIVERSE_MESSAGES;

  $updates = array();
  
  if($universe['stage'] == 'inflation')
  {
    $universe['stage'] = 'recombination';
    $universe['hydrogen'] += 5;

    $updates[] = 'stage=\'recombination\'';
    $updates[] = 'hydrogen=hydrogen+5';

    log_universe_event($universe['idnum'], 'Your universe has entered the Recombination stage of development.');

    $UNIVERSE_MESSAGES[] = '<span class="success">You received 5 Hydrogen! <i>(Not the item - this is Hydrogen you can only use within your universe.  Though you can use the Hydrogen items to add Hydrogen to your universe.  NOW YOU KNOW.)</i></p>';
  }
  else if($universe['stage'] == 'recombination')
  {
    $universe['stage'] = 'formation';
    $universe['stars']++;

    $updates[] = 'stage=\'formation\'';
    $updates[] = 'stars=stars+1';

    log_universe_event($universe['idnum'], 'Your universe has entered the Star Formation stage of development.');

    $UNIVERSE_MESSAGES[] = '<span class="success">You received 1 Star! <i>(This is also not an item.  You can use star-like items to add more stars to your universe, however.)</i></p>';
  }
  else if($universe['stage'] == 'formation')
  {
    $universe['stage'] = 'gameplay';
    $updates[] = 'stage=\'gameplay\'';

    log_universe_event($universe['idnum'], 'Your universe has entered the Galaxy Formation stage of development.');

    $UNIVERSE_MESSAGES[] = 'Your universe is beginning to take shape!  Guided by dark matter, galaxies and giant, star-forming clouds are beginning to form... things are finally starting to get interesting!';

    if(mt_rand(1, 2) == 1)
    {
      $universe['clouds']++;
      $updates[] = 'clouds=clouds+1';
      $UNIVERSE_MESSAGES[] = '<span class="success">You received 1 Cloud!  Use it to start building your universe!</p>';
    }
    else
    {
      $universe['galaxies']++;
      $updates[] = 'galaxies=galaxies+1';
      $UNIVERSE_MESSAGES[] = '<span class="success">You received 1 Galaxy!  Use it to start building your universe!</p>';
    }
  }
  else if($universe['stage'] == 'gameplay' && $universe['galactic_object_count'] > 0)
  {
    $actions = strlen(pow($universe['total_object_count'], 2));
    
    perform_random_actions($universe, $actions);
  }

  $universe['lastupdate'] = time();

  $updates[] = 'lastupdate=' . $universe['lastupdate'];

  $command = 'UPDATE psypets_universes SET ' . implode(',', $updates) . ' WHERE idnum=' . $universe['idnum'];
  fetch_none($command, 'saving your universe');

  return $universe;
}

function perform_random_actions(&$universe, $actions)
{
  global $UNIVERSE_MESSAGES;

  $types = array('universe', 'galaxy', 'system', 'star', 'planet', 'civilization');

  if($universe['ownerid'] == 1)
    $UNIVERSE_MESSAGES[] = 'Performing ' . $actions . ' actions...';

  for($x = 0; $x < $actions; ++$x)
  {
    $done = false;
  
    while(!$done)
    {
      $type_i = array_rand($types);
      $type = $types[$type_i];

      switch($type)
      {
        case 'universe':
          if($universe['ownerid'] == 1)
            $UNIVERSE_MESSAGES[] = '[universe]';
          $done = perform_random_universe_action($universe);
          break;
        case 'galaxy':
          if($universe['ownerid'] == 1)
            $UNIVERSE_MESSAGES[] = '[galaxy]';
          $done = perform_random_galaxy_action($universe);
          break;
        case 'system':
          if($universe['ownerid'] == 1)
            $UNIVERSE_MESSAGES[] = '[solar system]';
          $done = perform_random_solar_system_action($universe);
          break;
        case 'star':
          if($universe['ownerid'] == 1)
            $UNIVERSE_MESSAGES[] = '[star]';
          $done = perform_random_star_action($universe);
          break;
        case 'planet':
          if($universe['ownerid'] == 1)
            $UNIVERSE_MESSAGES[] = '[planet]';
          $done = perform_random_planet_action($universe);
          break;
        case 'civilization':
          if($universe['ownerid'] == 1)
            $UNIVERSE_MESSAGES[] = '[civilization]';
          $done = perform_random_civilization_action($universe);
          break;
        default:
          if($universe['ownerid'] == 1)
            $UNIVERSE_MESSAGES[] = '[' . $type . '?]';
          $done = true;
          break;
      }
      
      if(!$done)
      {
        unset($types[$type_i]);
        if(count($types) == 0)
          $done = true;
      }
    }
  }
}

function perform_random_universe_action(&$universe)
{
  // * an interaction between your and someone else's universe?

  switch(mt_rand(1, 2))
  {
    case 1:

    // get some hydrogen
    case 2:
      $hydrogen = mt_rand(2, 5);
      universe_gain($universe, 'hydrogen', $hydrogen);
      log_universe_event($universe['idnum'], 'Some Hydrogen is accumulating!  You gain ' . $hydrogen . ' Hydrogen.');
      break;
  }

  return true;
}

function perform_random_galaxy_action(&$universe)
{
  global $GALAXY_GRAPHICS;
  
  $command = '
    SELECT *
    FROM psypets_galactic_objects
    WHERE universeid=' . $universe['idnum'] . '
    ORDER BY RAND()
    LIMIT 1
  ';
  $galaxy = fetch_single($command, 'fetching galaxy');
  
  if($galaxy === false)
    return false;
  
  $actions = array('collide', 'starformation');
  
  if($galaxy['type'] == 'spiral_agn' || $galaxy['type'] == 'elliptical_agn')
    $actions[] = 'deAGNify';

  $action = $actions[array_rand($actions)];
  
  switch($action)
  {
    case 'starformation':
      $stardust = mt_rand(1, 4);
      
      $command = '
        UPDATE psypets_galactic_objects
        SET
          stardust=stardust+' . $stardust . '
        WHERE idnum=' . $galaxy['idnum'] . '
        LIMIT 1
      ';
      fetch_none($command, 'star formation!');

      log_universe_event($universe['idnum'], '<a href="universe_viewgalaxy.php?id=' . $galaxy['idnum'] . '">' . galaxy_full_name($galaxy) . '</a> is undergoing star formation!');

      return true;

    case 'collide':
      $stardust = mt_rand(5, 15);

      if($galaxy['type'] == 'cloud')
      {
        if(mt_rand(1, 3) == 1)
        {
          $new_image = $GALAXY_GRAPHICS['spiral'][array_rand($GALAXY_GRAPHICS['spiral'])];

          $command = '
            UPDATE psypets_galactic_objects
            SET
              type=\'spiral_agn\',
              image=' . quote_smart($new_image) . ',
              stardust=stardust+' . $stardust . '
            WHERE idnum=' . $galaxy['idnum'] . '
            LIMIT 1
          ';
          fetch_none($command, 'growing cloud into spiral (agn) galaxy');
        
          log_universe_event($universe['idnum'], '<a href="universe_viewgalaxy.php?id=' . $galaxy['idnum'] . '">' . galaxy_full_name($galaxy) . '</a> collided with another Cloud and beaome a Spiral Galaxy!  Star formation is really picking up!');
        }
        else
        {
          $new_image = $GALAXY_GRAPHICS['elliptical'][array_rand($GALAXY_GRAPHICS['elliptical'])];

          $command = '
            UPDATE psypets_galactic_objects
            SET
              type=\'elliptical_agn\',
              image=' . quote_smart($new_image) . ',
              stardust=stardust+' . $stardust . '
            WHERE idnum=' . $galaxy['idnum'] . '
            LIMIT 1
          ';
          fetch_none($command, 'growing cloud into elliptical (agn) galaxy');

          log_universe_event($universe['idnum'], '<a href="universe_viewgalaxy.php?id=' . $galaxy['idnum'] . '">' . galaxy_full_name($galaxy) . '</a> collided with another Cloud and became an Elliptical Galaxy!  Star formation is really picking up!');
        }
      }
      else
      {
        $old_galaxy = $galaxy;

        if(mt_rand(1, 4) == 1)
        {
          if($galaxy['type'] == 'spiral')
            $galaxy['type'] = 'elliptical';
          else if($galaxy['type'] == 'spiral_agn')
            $galaxy['type'] = 'elliptical_agn';
        }

        if(mt_rand(1, 3) == 1)
        {
          if($galaxy['type'] == 'spiral')
            $galaxy['type'] = 'spiral_agn';
          else if($galaxy['type'] == 'elliptical')
            $galaxy['type'] = 'elliptical_agn';
        }

        if($old_galaxy['type'] != $galaxy['type'])
        {
          if($galaxy['type'] == 'elliptical' || $galaxy['type'] == 'elliptical_agn')
            $new_image = $GALAXY_GRAPHICS['elliptical'][array_rand($GALAXY_GRAPHICS['elliptical'])];
          else
            $new_image = $GALAXY_GRAPHICS['spiral'][array_rand($GALAXY_GRAPHICS['spiral'])];

          $command = '
            UPDATE psypets_galactic_objects
            SET
              type=' . quote_smart($galaxy['type']) . ',
              image=' . quote_smart($new_image) . ',
              stardust=stardust+' . $stardust . '
            WHERE idnum=' . $galaxy['idnum'] . '
            LIMIT 1
          ';
          fetch_none($command, 'updating galactic object');

          log_universe_event($universe['idnum'], '<a href="universe_viewgalaxy.php?id=' . $galaxy['idnum'] . '">' . galaxy_full_name($galaxy) . '</a> collided with another galaxy, forming a single ' . galaxy_type($galaxy) . '!  Star formation is picking up!');
        }
        else
        {
          $command = '
            UPDATE psypets_galactic_objects
            SET
              stardust=stardust+' . $stardust . '
            WHERE idnum=' . $galaxy['idnum'] . '
            LIMIT 1
          ';
          fetch_none($command, 'updating galactic object');

          log_universe_event($universe['idnum'], '<a href="universe_viewgalaxy.php?id=' . $galaxy['idnum'] . '">' . galaxy_full_name($galaxy) . '</a> collided with another galaxy!  Star formation is picking up!');
        }
      }

      return true;

    case 'deAGNify':
      if($galaxy['type'] == 'spiral_agn')
      {
        $new_type = 'spiral';
        $new_image = $GALAXY_GRAPHICS['spiral'][array_rand($GALAXY_GRAPHICS['spiral'])];
      }
      else if($galaxy['type'] == 'elliptical_agn')
      {
        $new_type = 'elliptical';
        $new_image = $GALAXY_GRAPHICS['elliptical'][array_rand($GALAXY_GRAPHICS['elliptical'])];
      }
      else
        return false;

      $command = '
        UPDATE psypets_galactic_objects
        SET
          type=' . quote_smart($new_type) . ',
          image=' . quote_smart($new_image) . '
        WHERE idnum=' . $galaxy['idnum'] . '
        LIMIT 1
      ';
      fetch_none($command, 'updating galaxy');
      
      log_universe_event($universe['idnum'], 'The ' . $galaxy['name'] . ' Galaxy\'s active nucleus has calmed down.');

      return true;
  }
  
  return false;
}

function perform_random_solar_system_action(&$universe)
{
  // * matter from the oort cloud bombards the solar system
  // * a comet!
  // * if the solar system is empty, generate a handful of planets!

  $command = '
    SELECT *
    FROM psypets_stellar_objects
    WHERE universeid=' . $universe['idnum'] . '
    ORDER BY RAND()
    LIMIT 1
  ';
  $system = fetch_single($command, 'fetching solar system');
  
  if($system === false)
    return false;

  $actions = array('comet', 'nothing');
  
  if($system['object_count'] == 0)
    $actions[] = 'accrete';
  else
    $actions[] = 'oort';

  $action = $actions[array_rand($actions)];

  switch($action)
  {
    case 'accrete':
      $x = mt_rand(16, 300 - 16);
      $y = mt_rand(16, 300 - 16);

      $rocks = mt_rand(2, 8);

      create_rocky_planet($universe['idnum'], $system, $x, $y, $rocks);

      $x = mt_rand(300 + 32, 600 - 32);
      $y = mt_rand(32, 300 - 32);

      $gas = mt_rand(1, 5);

      create_gas_giant($universe['idnum'], $system, $x, $y, $gas);

      return true;

    case 'comet':
      $x = mt_rand(8, 600 - 8);
      $y = mt_rand(8, 300 - 8);
      
      if(solar_system_space_is_empty($system['idnum'], $x, $y, 16))
      {
        create_comet($universe['idnum'], $system, $x, $y);
        return true;
      }
      // else, peform an oort cloud action instead

    case 'oort':

    case 'nothing':
      return true;

  }
}

function perform_random_star_action(&$universe)
{
  // * the star ages, perhaps supernova'ing
  //   (a supernova allows any present civilizations to make an escape attempt)
  // * if the star is part of a binary system, and is the larger of the two,
  //   matter is siphoned from one to the other
  // * nothing happens (this is counted as a "successful" action)

  $command = '
    SELECT *
    FROM psypets_stars
    WHERE universeid=' . $universe['idnum'] . '
    ORDER BY RAND()
    LIMIT 1
  ';
  $star = fetch_single($command, 'fetching planet');

  if($star === false)
    return false;

  $actions = array('nothing', 'age', 'ss');

  $action = $actions[array_rand($actions)];
  
  switch($action)
  {
    case 'nothing':
      return true;

    case 'ss':
      return perform_random_solar_system_action($universe);

    case 'age':
      return true;
  }

  return false;
}

function perform_random_planet_action(&$universe)
{
  // * life evolves, if present and capable. if life evolves to level 5, a
  //   civilization is created, with population dependent on planet habitability
  // * population increases; growth depends on planet habitability
  //   (requires life level: 5)
  // * if the planet is very close to another on the x-axis, the two collide!
  //   (the larger planet eats the other, gaining part of its mass, and giving
  //   you a few rocks (depending on the lost mass, if any))

  $command = '
    SELECT *
    FROM psypets_planets
    WHERE universeid=' . $universe['idnum'] . '
    ORDER BY RAND()
    LIMIT 1
  ';
  $planet = fetch_single($command, 'fetching planet');
  
  if($planet === false)
    return false;

  // if the "planet" is an asteroid belt, it produces a rock
  if($planet['type'] == 'belt')
  {
    universe_gain($universe, 'rocks', 1);
    log_universe_event($universe['idnum'], '<a href="universe_viewobject.php?id=' . $planet['idnum'] . '">The ' . $planet['name'] . ' Asteroid Belt</a> produced 1 Rock (you gain 1 Rock).');

    return true;
  }
  // if the "planet" is a comet, it vanishes (is deleted)
  else if($planet['type'] == 'comet')
  {
    $command = 'DELETE FROM psypets_planets WHERE idnum=' . $planet['idnum'] . ' LIMIT 1';
    fetch_none($command, 'deleting comet');

    $command = 'UPDATE psypets_stellar_objects SET object_count=object_count-1 WHERE idnum=' . $planet['systemid'] . ' LIMIT 1';
    fetch_none($command, 'updating solar system object count');

    log_universe_event($universe['idnum'], 'The ' . $planet['name'] . ' Comet vanishes into the oort cloud, never to be seen again...');

    return true;
  }
  else
  {
    $actions = array();
    
    if($planet['life'] < 5 && $planet['class'] == 'eden')
      $actions[] = 'evolve';
    if($planet['life'] >= 5)
      $actions[] = 'popgrowth';
    if($planet['civilizationid'] > 0)
      $actions[] = 'wealth';

    if(count($actions) == 0 && $planet['type'] == 'gasgiant')
      $actions[] = 'hydrogen';
      
    if(count($actions) == 0)
      return true;

    $action = $actions[array_rand($actions)];
    
    switch($action)
    {
      case 'hydrogen':
        universe_gain($universe, 'hydrogen', 1);
        log_universe_event($universe['idnum'], '<a href="universe_viewobject.php?id=' . $planet['idnum'] . '">The ' . $planet['name'] . ' Gas Giant</a> produced 1 Hydorgen (you gain 1 Hydrogen).');
        return true;

      case 'wealth':
        $command = 'UPDATE psypets_civilizations SET wealth=wealth+1 WHERE idnum=' . $planet['civilizationid'] . ' LIMIT 1';
        fetch_none($command, 'updating civilization wealth');

        log_universe_event($universe['idnum'], 'Planet <a href="universe_viewobject.php?id=' . $planet['idnum'] . '">' . $planet['name'] . '</a> produced Wealth for <a href="universe_viewciv.php?id=' . $civ['idnum'] . '">its civilization</a>.');

        return true;

      case 'evolve':
        $planet['life']++;

        $command = 'UPDATE psypets_planets SET life=life+1 WHERE idnum=' . $planet['idnum'] . ' LIMIT 1';
        fetch_none($command, 'update planet population');

        if($planet['life'] == 5)
        {
          $civ = create_civilization($universe, $planet);

          log_universe_event($universe['idnum'], 'Life is evolving on <a href="universe_viewobject.php?id=' . $planet['idnum'] . '">' . $planet['name'] . '</a>!  The <a href="universe_viewciv.php?id=' . $civ['idnum'] . '">' . $civ['name'] . ' civilization</a> has been born!');
        }
        else
          log_universe_event($universe['idnum'], 'Life is evolving on <a href="universe_viewobject.php?id=' . $planet['idnum'] . '">' . $planet['name'] . '</a>!  It now hosts ' . life_description($planet['life']) . '!');

        return true;

      case 'popgrowth':
        $growth = mt_rand(floor($planet['population'] / 5), ceil($planet['population'] / 3));

        if($planet['class'] == 'desolate')
          $growth *= 0.33;
        else if($planet['class'] == 'habitable')
          $growth *= 0.66;

        $growth = floor($growth);

        $planet['population'] += $growth;

        $command = 'UPDATE psypets_planets SET population=population+' . $growth . ' WHERE idnum=' . $planet['idnum'] . ' LIMIT 1';
        fetch_none($command, 'update planet population');

        $command = 'UPDATE psypets_civilizations SET total_population=total_population+' . $growth . ' WHERE idnum=' . $planet['civilizationid'] . ' LIMIT 1';
        fetch_none($command, 'update civilization population');

        log_universe_event($universe['idnum'], 'The population of <a href="universe_viewobject.php?id=' . $planet['idnum'] . '">' . $planet['name'] . '</a> has grown!');

        return true;
    }
  }
}

/* tech levels:
     1: stone age
     2: imperial age
     3: industrial age
     4: computer age
     5: space age (settles a nearby planet, or builds a space station)
     6+: space age (+X)

   philosophies:
     there is a god
     we are in a computer system
     athiest
*/
function perform_random_civilization_action(&$civilization)
{
  // * tech level increases (max: 15, wealth: 4)
  // * builds ark to escape solar system (if solar system contains a giant star,
  //   and tech level is at least 5)
  // * settles a nearby planet, or builds a space station (tech level: 5+, wealth: 3)
  // * settles a planet in a nearby solar system (tech level: 7+, wealth: 7)
  // * settles a planet in a nearby galaxy (tech level: 10+, wealth: 15)
  // * becomes neutral with a nearby, unknown civilization (tech level: 5)
  // * goes to war with a nearby, neutral civilization; attacks one of their planets (wealth: X)
  // * forms a treaty with a nearby, neutral civilization
  // * becomes neutral with a nearby, peaceful or warring civilization
  // * joins with a peaceful civilization
  // * attacks a nearby planet of a warring civilization (wealth: X)
  // * builds; wealth +1
  // * harvests a comet: wealth +2
  // * harvests an asteroid belt: wealth +2 (1% chance that the asteroid belt is consumed)
  // * trades with a civilization at peace: wealth +2, other civ gets wealth +1

  // attacking costs wealth: same system: 1, different systems: 3, different galaxies: 9
  // attack successes are based on relative tech levels and populations
  // both sides lose population; the more successful side loses less
  // success steals: 1 wealth, the city, 1 tech (cummulative, depending on grade of success)

  // civs do not declare war on civs of significantly higher tech level

  return false;
}

function create_civilization(&$universe, &$homeworld)
{
  $name = species_random_name();
  
  $command = '
    INSERT INTO psypets_civilizations
    (universeid, name, homeworldid, total_population)
    VALUES
    (
      ' . $universe['idnum'] . ', ' . quote_smart($name) . ',
      ' . $homeworld['idnum'] . ', ' . $homeworld['population'] . '
    )
  ';
  fetch_none($command, 'creating civilization');
  
  $id = $GLOBALS['database']->InsertID();

  return array('idnum' => $id, 'name' => $name);
}

function species_random_name()
{
  $starting_words = array(
    'mono', 'pachy', 'octo', 'porcu', 'dino', 'ele', 'pango', 'leo', 'fe', 'ro',
    'proto', 'ma', 'pri', 'platy', 'cani', 'rhino',
  );
  
  $ending_words = array(
    'treme', 'derm', 'pus', 'pine', 'saur', 'phant', 'mite', 'lin', 'pard', 'line',
    'dent', 'theria', 'mal', 'mate', 'dae', 'tile', 'nid', 'zard', 'ko', 'ceros',
  );

  return $starting_words[array_rand($starting_words)] . $ending_words[array_rand($ending_words)];
}

function universe_random_name()
{
  $base_words = array(
    'milky', 'andro', 'meda', 'way', 'tad', 'pole', 'cart', 'wheel', 'pin',
    'whirl', 'pool', 'sun', 'flower', 'som', 'brero', 'o', 'mega', 'cen',
    'taur', 'trian', 'gulum', 'bla', 'zar', 'sey', 'fert', 'qua', 'sar',
    'vir', 'go', 'sagi', 'tarius', 'can', 'may', 'all', 'star', 'fish', 'mono',
    'ceros',
  );
  
  $words = array_rand($base_words, 2);
  
  return ucfirst($base_words[$words[0]] . $base_words[$words[1]]);
}

function get_universe_history_pages($universeid)
{
  $command = '
    SELECT COUNT(idnum) AS c
    FROM psypets_universe_history
    WHERE universeid=' . $universeid . '
  ';
  $data = fetch_single($command, 'getting history count');
  
  return ceil($data['c'] / 20);
}

function get_universe_history($universeid, $page)
{
  $command = '
    SELECT *
    FROM psypets_universe_history
    WHERE universeid=' . $universeid . '
    ORDER BY idnum DESC
    LIMIT ' . (($page - 1) * 20) . ',20
  ';
  return fetch_multiple($command, 'fetching universe history');
}

function log_universe_event($universeid, $event)
{
  global $UNIVERSE_MESSAGES;

  $UNIVERSE_MESSAGES[] = $event;

  $command = '
    INSERT INTO psypets_universe_history
    (universeid, timestamp, event)
    VALUES
    (' . $universeid . ', ' . time() . ', ' . quote_smart($event) . ')
  ';
  fetch_none($command, 'logging universe history');
}

function harvest_galaxy_stardust(&$galaxy)
{
  $stars = floor($galaxy['stardust'] / 12);
  
  if($stars < 1)
    return;

  if($stars * 12 == $galaxy['stardust'])
    $command = '
      UPDATE psypets_galactic_objects
      SET stardust=0
      WHERE idnum=' . $galaxy['idnum'] . '
      LIMIT 1
    ';
  else
    $command = '
      UPDATE psypets_galactic_objects
      SET stardust=stardust-' . ($stars * 12) . '
      WHERE idnum=' . $galaxy['idnum'] . '
      LIMIT 1
    ';

  fetch_none($command, 'expending stardust');

  $command = '
    UPDATE psypets_universes
    SET stars=stars+' . $stars . '
    WHERE idnum=' . $galaxy['universeid'] . '
    LIMIT 1
  ';
  fetch_none($command, 'giving stars to universe');
}
?>
