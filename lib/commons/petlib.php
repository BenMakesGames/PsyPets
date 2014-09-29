<?php
require_once 'commons/globals.php';
require_once 'commons/grammar.php';
require_once 'commons/petgraphics.php';
require_once 'commons/flavorlib.php';
require_once 'commons/petbadges.php';
require_once 'commons/moonphase.php';

require_once 'libraries/db_messages.php';

function delete_pet(&$pet)
{
  fetch_none('DELETE FROM monster_pets WHERE idnum=' . $pet['idnum'] . ' LIMIT 1');
  fetch_none('DELETE FROM psypets_pet_level_logs WHERE petid=' . $pet['idnum']);
  fetch_none('DELETE FROM psypets_pet_market WHERE petid=' . $pet['idnum'] . ' LIMIT 1');
  fetch_none('DELETE FROM psypets_pet_relationships WHERE petid=' . $pet['idnum']);
  fetch_none('DELETE FROM psypets_petstats WHERE petid=' . $pet['idnum'] . ' LIMIT 1');
  fetch_none('DELETE FROM monster_petlogs WHERE petid=' . $pet['idnum']);

  delete_pet_badges($pet['idnum']);
}

$PET_ID_CACHE = array();

$ASCEND_STATS = array(
  'ascend_adventurer', 'ascend_hunter', 'ascend_inventor',
  'ascend_artist', 'ascend_gatherer', 'ascend_smith', 'ascend_tailor', 'ascend_leather',
  'ascend_fisher', 'ascend_lumberjack', 'ascend_miner', 'ascend_carpenter',
  'ascend_jeweler', 'ascend_painter', 'ascend_sculptor', 'ascend_mechanic',
  'ascend_binder', 'ascend_chemist', 'ascend_vhagst',
);

$PET_SKILLS = array(
  'str', 'dex', 'sta', 'per', 'int', 'wit', 'bra',
  'athletics', 'stealth', 'sur', 'gathering', 'fishing', 'mining',
  'cra', 'painting', 'carpentry', 'jeweling', 'sculpting',
  'eng', 'mechanics', 'chemistry', 'smi', 'tai', 'leather', 'binding',
  'pil', 'astronomy', 'music',
);

$PET_STAT_DESCRIPTIONS = array(
  'str' => 'strength',
  'dex' => 'dexterity',
  'sta' => 'stamina',
  'int' => 'intelligence',
  'wit' => 'wits',
  'per' => 'perception',
  'athletics' => 'athletics',
  'stealth' => 'stealth',
  'bra' => 'combat', 
  'sur' => 'survival',
  'gathering' => 'nature',
  'fishing' => 'fishing',
  'mining' => 'mining',
  'cra' => 'handicrafts',
  'painting' => 'painting',
  'carpentry' => 'carpentry',
  'jeweling' => 'jeweling',
  'sculpting' => 'sculpting',
  'eng' => 'electronics',
  'mechanics' => 'mechanics',
  'chemistry' => 'chemistry',
  'smi' => 'smithing',
  'tai' => 'tailory',
  'leather' => 'leather-working',
  'binding' => 'magic-binding',
  'pil' => 'piloting',
  'astronomy' => 'astronomy',
  'music' => 'music',
);

$KNACKS = array(
  'knack_mechanics' => 'mechanics',
  'knack_electronics' => 'electronics',
  'knack_hunting' => 'hunting',
  'knack_gathering' => 'gathering',
  'knack_smithing' => 'smithing',
  'knack_tailoring' => 'tailoring',
  'knack_leather' => 'leather working',
  'knack_adventuring' => 'adventuring',
  'knack_crafting' => 'crafting',
  'knack_painting' => 'painting',
  'knack_carpentry' => 'carpentry',
  'knack_sculpting' => 'sculpting',
  'knack_jeweling' => 'jeweling',
  'knack_mining' => 'mining',
  'knack_lumberjacking' => 'lumberjacking',
  'knack_fishing' => 'fishing',
  'knack_binding' => 'magic-binding',
  'knack_chemistry' => 'chemistry',
  'knack_gardening' => 'gardening',
  'knack_videogames' => 'video games',
);

$PET_MERITS = array(
  'merit_steady_hands',
  'merit_light_sleeper',
  'merit_acute_senses',
  'merit_catlike_balance',
  'merit_tough_hide',
  'merit_lightning_calculator',
  'merit_silver_tongue',
  'merit_lucky',
  'merit_medium',
  'merit_berserker',
  'merit_predicts_earthquakes',
  'merit_ravenous',
  'merit_careful_with_equipment',
  'merit_transparent',
  'merit_pruriency',
  'merit_sleep_walker',
);

function add_pet_feeling($petid, $modifies, $modifier, $decay, $description)
{
  fetch_none('
    INSERT INTO psypets_pet_feelings (petid, description, `modifies`, modifier, decay)
    VALUES (' . $petid . ', ' . quote_smart($description) . ', ' . quote_smart($modifies) . ', ' . $modifier . ', ' . $decay . ')
  ');
}

function pet_level(&$this_pet)
{
  return
    $this_pet['str'] +
    $this_pet['dex'] +
    $this_pet['sta'] +
    $this_pet['per'] +
    $this_pet['int'] +
    $this_pet['wit'] +
    $this_pet['bra'] +
    $this_pet['athletics'] +
    $this_pet['stealth'] +
    $this_pet['sur'] +
    $this_pet['gathering'] +
    $this_pet['fishing'] +
    $this_pet['mining'] +
    $this_pet['cra'] +
    $this_pet['painting'] +
    $this_pet['carpentry'] +
    $this_pet['jeweling'] +
    $this_pet['sculpting'] +
    $this_pet['eng'] +
    $this_pet['mechanics'] +
    $this_pet['chemistry'] +
    $this_pet['smi'] +
    $this_pet['tai'] +
    $this_pet['leather'] +
    $this_pet['binding'] +
    $this_pet['pil'] +
    $this_pet['astronomy'] +
    $this_pet['music']
  ;
}

function train_pet(&$this_pet, $stat, $amount, $hour = 0, $force = false, $immediate = false)
{
  $increase = 0;

  if(
    $force === true ||
    (
      ($this_pet['energy'] > 0 || $this_pet['caffeinated'] > 0) &&
      $this_pet['food'] > 0 &&
      ($this_pet['safety'] > 0 || $this_pet['drunk'] > 0) &&
      $this_pet['love'] > 0 &&
      ($this_pet['esteem'] > 0 || $this_pet['drunk'] > 0)
    )
  )
  {
    if($this_pet['drunk'] > 0 && $force !== true)
      $amount = floor($amount / 5);
  
    if($amount <= 0)
      return 0;
  
    $new_training = $this_pet[$stat . '_count'] + $amount;
    while($new_training >= level_stat_exp($this_pet[$stat]))
    {
      $new_training -= level_stat_exp($this_pet[$stat]);
      $this_pet[$stat]++;
      $increase++;
    }

    $this_pet[$stat . '_count'] = $new_training;

    if($immediate)
      save_pet($this_pet, array($stat, $stat . '_count'));

    if($increase > 0)
      log_level_up($this_pet, $stat, $increase, $hour);
  }
  
  return $increase;
}

function log_level_up(&$pet, $stat, $amount, $hour)
{
  global $PET_STAT_DESCRIPTIONS;

  if($amount > 1)
    $message = '<b>' . $pet['petname'] . '\'s ' . $PET_STAT_DESCRIPTIONS[$stat] . ' increased by ' . $amount . '!</b>';
  else
    $message = '<b>' . $pet['petname'] . '\'s ' . $PET_STAT_DESCRIPTIONS[$stat] . ' increased!</b>';

  $owner = get_user_byuser($pet['user'], 'idnum');

  add_db_message($owner['idnum'], FLASH_MESSAGE_PET_PROGRESS, $message);
  add_logged_event($owner['idnum'], $pet['idnum'], $hour, ($hour == 0 ? 'realtime' : 'hourly'), false, $message);

  fetch_none('
    INSERT INTO psypets_pet_level_logs
    (timestamp, petid, answer)
    VALUES
    (
      ' . time() . ',
      ' . $pet['idnum'] . ',
      ' . quote_smart($message) . '
    )
  ');
}

function are_immediate_family_members($pet1, $pet2)
{
	return(
        $pet1['motherid'] == $pet2['motherid']
		|| $pet1['motherid'] == $pet2['fatherid']
		|| $pet1['fatherid'] == $pet2['motherid']
		|| $pet1['fatherid'] == $pet2['fatherid']
		|| $pet1['motherid'] == $pet2['idnum']
		|| $pet1['fatherid'] == $pet2['idnum']
		|| $pet1['idnum'] == $pet2['motherid']
		|| $pet1['idnum'] == $pet2['fatherid']
	);
}

function sex_suggest($this_pet, $other_pet, $relationship, $other_relationship)
{
  $divider = ($this_pet['merit_pruriency'] == 'yes' ? 3.5 : 5);

	if(are_immediate_family_members($this_pet, $other_pet))
		$divider += 2;
	
  return ($relationship['passion'] / 100 * ($relationship['passion'] * 1.5 + $relationship['intimacy'] + $relationship['commitment'] / 2)) / $divider;
}

function sex_agree($this_pet, $other_pet, $relationship, $other_relationship)
{
  $divider = ($this_pet['merit_pruriency'] == 'yes' ? 3.5 : 5);

	if(are_immediate_family_members($this_pet, $other_pet))
		$divider += 2;

  return (($relationship['passion'] + $relationship['intimacy'] + $relationship['commitment']) / 200 * ($relationship['passion'] * 1.5 + $relationship['intimacy'] + $relationship['commitment'] / 1.5)) / $divider;
}

function random_blood_type()
{
  $letters = array('A', 'B', 'O');
  $rhs = array('+', '-');
  
  $letter = $letters[array_rand($letters)] . $letters[array_rand($letters)];
  $rh = $rhs[array_rand($rhs)] . $rhs[array_rand($rhs)];
  
  return $letter . $rh;
}

function inherit_blood_type($mother, $father)
{
  $letter = $mother{mt_rand(0, 1)} . $father{mt_rand(0, 1)};
  $rh = $mother{mt_rand(2, 3)} . $father{mt_rand(2, 3)};

  return $letter . $rh;
}

function say_blood_type($type)
{
  $letters = substr($type, 0, 2);
  $rhs = substr($type, 2, 2);

  switch($letters)
  {
    case 'AA':
    case 'AO':
    case 'OA':
      $letter = 'A';
      break;
    case 'BB':
    case 'BO':
    case 'OB':
      $letter = 'B';
      break;
    case 'OO':
      $letter = '0';
      break;
    case 'AB':
    case 'BA';
      $letter = 'AB';
      break;
    default:
      $letter = 'nd';
      break;
  }
  
  switch($rhs)
  {
    case '++':
    case '+-':
    case '-+':
      $rh = '+';
      break;
    case '--';
      $rh = '-';
      break;
    default:
      $rh = '?';
      break;
  }
  
  return $letter . $rh;
}

function pet_graphic($mypet, $link = true, $extra = '')
{
  global $SETTINGS;

  $classes = array();

  if($mypet['dead'] != 'no')
    $classes[] = 'ghost';

  if($mypet['graphic_flip'] == 'yes')
    $classes[] = 'flip-horizontal';

  if($mypet['graphic_size'] > 48)
    $mypet['graphic_size'] = 48;
    
  $margin = floor((48 - $mypet['graphic_size']) / 2);

  if($margin > 0)
    $extra .= ' style="margin:' . floor($margin * 1.75) . 'px ' . $margin . 'px 0;"';

  if($mypet['eggplant'] == 'yes')
    $xhtml = '<img src="//' . $SETTINGS['static_domain'] . '/gfx/pets/eggplant.png" class="' . implode(' ', $classes) . '" width="' . $mypet['graphic_size'] . '" height="' . $mypet['graphic_size'] . '" alt="" ' . $extra . ' />';
  else if($mypet['changed'] == 'yes')
    $xhtml = '<img src="//' . $SETTINGS['static_domain'] . '/gfx/pets/were/form_' . ($mypet['idnum'] % 2 + 1) . '.png" class="' . implode(' ', $classes) . '" width="' . $mypet['graphic_size'] . '" height="' . $mypet['graphic_size'] . '" alt="" ' . $extra . ' />';
  else if($mypet['zombie'] == 'yes')
    $xhtml = '<img src="//' . $SETTINGS['static_domain'] . '/gfx/pets/zombie/form_' . ($mypet['idnum'] % 3 + 1) . '.png" class="' . implode(' ', $classes) . '" width="' . $mypet['graphic_size'] . '" height="' . $mypet['graphic_size'] . '" alt="" ' . $extra . ' />';
  else
    $xhtml = '<img src="/gfx/pets/' . $mypet['graphic'] . '" class="' . implode(' ', $classes) . '" width="' . $mypet['graphic_size'] . '" height="' . $mypet['graphic_size'] . '" alt="" ' . $extra . ' /></a>';

  if($link)
    $xhtml = '<a href="/petprofile.php?petid=' . $mypet['idnum'] . '">' . $xhtml . '</a>';

  if($mypet['special_lightning'] == 'yes')
    $xhtml = '<div style="background-image: url(\'//' . $SETTINGS['static_domain'] . '/gfx/pets/lightning.gif\'); width:48px; height:48px;">' . $xhtml . '</div>';

  if($mypet['special_sparkles'] == 'yes')
    $xhtml = '<div style="background-image: url(\'//' . $SETTINGS['static_domain'] . '/gfx/pets/sparkle!.gif\'); width:48px; height:48px;">' . $xhtml . '</div>';

  if($mypet['special_love'] == 'yes')
    $xhtml = '<div style="background-image: url(\'//' . $SETTINGS['static_domain'] . '/gfx/pets/auras/love.png\'); width:48px; height:48px;">' . $xhtml . '</div>';

  if($mypet['special_digital'] == 'yes')
    $xhtml = '<div style="background-image: url(\'//' . $SETTINGS['static_domain'] . '/gfx/pets/digital!.gif\'); width:48px; height:48px;">' . $xhtml . '</div>';

  return $xhtml;
}

function get_user_pets_for_simulation($username, $max_pets)
{
	return $GLOBALS['database']->FetchMultipleBy('
	  SELECT *
		FROM monster_pets
		WHERE
			user=' . quote_smart($username) . '
			AND location=\'home\'
			AND dead=\'no\'
			ORDER BY orderid ASC
			LIMIT ' . $max_pets . '
	', 'idnum');
}

function get_pets_byuser($user, $location, $orderby = false)
{
  if($orderby === false)
    $command = 'SELECT * FROM monster_pets WHERE user=' . quote_smart($user) . ' AND location=' . quote_smart($location);
  else
    $command = 'SELECT * FROM monster_pets WHERE user=' . quote_smart($user) . ' AND location=' . quote_smart($location) . ' ORDER BY ' . $orderby;

	return $GLOBALS['database']->FetchMultipleBy($command, 'idnum');
}

function get_pet_byid($idnum, $select = '*')
{
  global $PET_ID_CACHE;

  if(!is_array($PET_ID_CACHE[$select]) || !array_key_exists($idnum, $PET_ID_CACHE[$select]))
  {
    $command = 'SELECT ' . $select . ' FROM monster_pets WHERE idnum=' . quote_smart($idnum) . ' LIMIT 1';

    $PET_ID_CACHE[$select][$idnum] = fetch_single($command, 'get_pet_byid');
  }
     
  return $PET_ID_CACHE[$select][$idnum];
}

function pet_size($mypet)
{
  return ceil(1 + $mypet['str'] * 7.5 + $mypet['sta'] * 10 + $mypet['athletics'] * 5);
}

function create_random_pet($owner)
{
  global $PET_GRAPHICS;

  return create_offspring($owner, 1, $PET_GRAPHICS, random_blood_type(), random_blood_type(), false);
}

function create_random_offspring($owner, $generation, $possible_graphics, $free_rename = false)
{
  return create_offspring($owner, $generation, $possible_graphics, random_blood_type(), random_blood_type(), $free_rename);
}

function create_offspring($owner, $generation, $possible_graphics, $bloodtype1, $bloodtype2, $free_rename = false)
{
  global $now;
  global $LAST_NEW_PET_NAME, $FLAVORS, $COLORS, $KNACKS, $PET_MERITS, $SETTINGS;

  $petgender = (rand() % 2 == 0 ? '' : 'fe') . 'male';
  $petname = random_name($petgender);
  $graphic = $possible_graphics[array_rand($possible_graphics)];
  $bloodtype = inherit_blood_type($bloodtype1, $bloodtype2);
  
  $last_check = $now;
  
  if($owner == $SETTINGS['site_ingame_mailer'])
    $last_check += mt_rand(-12 * 60 * 60, 12 * 60 * 60) + (7 * 24 * 60 * 60);

  $extraverted   = mt_rand(2, 8);
  $open          = mt_rand(2, 8);
  $conscientious = mt_rand(2, 8);
  $playful       = mt_rand(2, 8);
  $independent   = mt_rand(2, 8);

  $energy = 6;
  $food   = 12;
  $safety = 12;
  $love   = 12;
  $esteem = 12;

  if($free_rename === true)
    $rename = 'yes';
  else
    $rename = 'no';

  if($owner == $SETTINGS['site_ingame_mailer'])
    $prolific = 'no';
  else
    $prolific = 'yes';

  list($likes_flavor, $dislikes_flavor) = array_rand($FLAVORS, 2);

  $favorite_color = $COLORS[array_rand($COLORS)];

  $knack_count = mt_rand(1, 3);
  $pet_knacks = array_rand($KNACKS, $knack_count);

  if(!is_array($pet_knacks))
    $pet_knacks = array($pet_knacks);

  $knack_values = array();

  for($x = 0; $x < $knack_count; ++$x)
    $knack_values[] = mt_rand(1, mt_rand(1, 3));

  if($petgender == 'male')
	{
		$attracted_to_males = mt_rand(0, mt_rand(0, 100));
		$attracted_to_females = mt_rand(mt_rand(0, 50), mt_rand(50, 100));
	}
	else if($petgender == 'female')
	{
		$attracted_to_males = mt_rand(mt_rand(0, 50), mt_rand(50, 100));
		$attracted_to_females = mt_rand(0, mt_rand(0, 100));
	}

  $merit_i = array_rand($PET_MERITS, 4 - $knack_count);
  foreach($merit_i as $i)
    $merits[$PET_MERITS[$i]] = 'yes';

  $merits['merit_moonkin'] = (mt_rand(1, 100) <= moon_phase_power(time()) ? 'yes' : 'no');

  fetch_none('
    INSERT INTO monster_pets
    (
      `user`, `petname`,
      `generation`, `birthday`,
      `bloodtype`, `gender`, `prolific`,
      `attraction_to_males`, `attraction_to_females`,
      `graphic`,
      `extraverted`, `open`, `conscientious`, `playful`, `independent`,
      `energy`, `food`, `safety`, `love`, `esteem`,
      `likes_flavor`, `dislikes_flavor`, `likes_color`,
      `last_check`, `protected`, `free_rename`,
      `' . implode('`, `', $pet_knacks) . '`,
      `' . implode('`, `', array_keys($merits)) . '`
    )
    VALUES
    (
      ' . quote_smart($owner) . ', ' . quote_smart($petname) . ',
      ' . $generation . ', ' . $now . ',
      ' . quote_smart($bloodtype) . ', ' . quote_smart($petgender) . ', ' . quote_smart($prolific) . ',
      ' . $attracted_to_males . ', ' . $attracted_to_females . ',
      ' . quote_smart($graphic) . ',
      ' . $extraverted . ', ' . $open . ', ' . $conscientious . ', ' . $playful . ', ' . $independent . ',
      ' . $energy . ', ' . $food . ', ' . $safety . ', ' . $love . ', ' . $esteem . ',
      ' . $likes_flavor . ', ' . $dislikes_flavor . ', ' . quote_smart($favorite_color) . ',
      ' . $last_check . ', \'no\', ' . quote_smart($rename) . ',
      ' . implode(', ', $knack_values) . ',
      \'' . implode('\', \'', $merits) . '\'
    )
  ');

  $LAST_NEW_PET_NAME = $petname;

  $petid = $GLOBALS['database']->InsertID();
  
  create_pet_badges($petid);

  return $petid;
}

function get_pet_badges($petid)
{
  $command = 'SELECT * FROM psypets_petbadges WHERE petid=' . $petid . ' LIMIT 1';
  $badges = fetch_single($command, 'fetching pet badges');
  
  if($badges === false)
  {
    create_pet_badges($petid);
    return get_pet_badges($petid);
  }
  else
    return $badges;
}

function delete_pet_badges($petid)
{
  $command = 'DELETE FROM psypets_petbadges WHERE petid=' . $petid . ' LIMIT 1';
  fetch_none($command, 'deleting pet badges');
}

function create_pet_badges($petid)
{
  $command = 'INSERT INTO psypets_petbadges (petid) VALUES (' . $petid . ')';
  fetch_none($command, 'creating pet badges');
}

function set_pet_badge(&$pet, $badge)
{
  global $PET_BADGE_DESC;

  $command = 'UPDATE psypets_petbadges SET `' . $badge . '`=\'yes\' WHERE petid=' . $pet['idnum'] . ' LIMIT 1';
  fetch_none($command, 'setting pet badge');
  
  if($GLOBALS['database']->AffectedRows() == 1)
  {
    $owner = get_user_byuser($pet['user'], 'idnum');
    add_db_message($owner['idnum'], FLASH_MESSAGE_PET_BADGE, $pet['petname'] . ' received the ' . $PET_BADGE_DESC[$badge] . ' badge!');
  }
}

function save_pet(&$mypet, $stats)
{
  if($mypet['zombie'] == 'yes')
  {
    $mypet['sleeping'] = 'no';
    $mypet['caffeinated'] = 0;
    $mypet['energy'] = 10;
    $mypet['food'] = 0;
    $mypet['safety'] = 10;
    $mypet['love'] = 10;
    $mypet['esteem'] = 10;
    $mypet['pregnant_asof'] = 0;
    $mypet['pregnant_by'] = '';
    $mypet['dead'] = 'no';
  }

  foreach($stats as $stat)
    $updates[] = '`' . $stat . '`=' . quote_smart($mypet[$stat]);

  $command = '
    UPDATE monster_pets
    SET ' . implode(', ', $updates) . '
    WHERE idnum=' . $mypet['idnum'] . '
    LIMIT 1
  ';
  fetch_none($command, 'saving pet');
}

function max_energy(&$mypet)
{
  return 12 + ($mypet['sta'] * 2) + $mypet['athletics'] + $mypet['str'];
}

function max_food(&$mypet)
{
  return ($mypet['merit_ravenous'] == 'yes' ? 24 : 12) + ($mypet['sta'] + $mypet['sur']) * 2;
}

function max_safety(&$mypet)
{
  return 24;
}

function max_love(&$mypet)
{
  return 48 + $mypet['extraverted'] * 2;
}

function max_esteem($mypet)
{
  return 48 + $mypet['conscientious'] * 2;
}

function gain_caffeine(&$mypet, $amount)
{
  if($amount > $mypet['caffeinated'])
    $mypet['caffeinated'] = $amount;
}

function gain_healing(&$mypet, $amount)
{
  $amount = $amount - $mypet['healing'];
  
  $mypet['healing'] += $amount;
  $mypet['nasty_wound'] = max(0, $mypet['nasty_wound'] - $amount);
  
  return $amount;
}

function gain_food(&$mypet, $amount)
{
  // excess food is wasted
  if($amount + $mypet['food'] > max_food($mypet))
    $amount = max_food($mypet) - $mypet['food'];

  $mypet['food'] += $amount;

  return $amount;
}

function gain_energy(&$mypet, $amount)
{
  if($amount + $mypet['energy'] > max_energy($mypet))
    $amount = max_energy($mypet) - $mypet['energy'];

  $mypet['energy'] += $amount;

  return $amount;
}

function gain_safety(&$mypet, $amount)
{
  if($mypet['food'] > 0 && ($mypet['energy'] > 0 || $mypet['caffeinated'] > 0))
  {
    if($amount + $mypet['safety'] > max_safety($mypet))
      $amount = max_safety($mypet) - $mypet['safety'];
  
    $mypet['safety'] += $amount;

    return $amount;
  }
  else
    return 0;
}

function gain_love(&$mypet, $amount)
{
  if($mypet['food'] > 0 && ($mypet['energy'] > 0 || $mypet['caffeinated'] > 0) && $mypet['safety'] > 0)
  {
    if($amount + $mypet['love'] > max_love($mypet))
      $amount = max_love($mypet) - $mypet['love'];
  
    $mypet['love'] += $amount;

    return $amount;
  }
  else
    return 0;
}

function gain_esteem(&$mypet, $amount)
{
  if($mypet['food'] > 0 && ($mypet['energy'] > 0 || $mypet['caffeinated'] > 0) && $mypet['safety'] > 0 && $mypet['love'] > 0)
  {
    if($amount + $mypet['esteem'] > max_esteem($mypet))
      $amount = max_esteem($mypet) - $mypet['esteem'];
  
    $mypet['esteem'] += $amount;

    return $amount;
  }
  else
    return 0;
}

function gain_love_level(&$mypet, $desc, $hour = 0, $immediate = false)
{
  $exp = level_exp($mypet['love_level']);
  
  if($mypet['love_exp'] < $exp)
    return false;
    
  $mypet['love_exp'] -= $exp;
  $mypet['love_level']++;
  
  $owner = get_user_byuser($mypet['user'], 'idnum');
  
  add_logged_event($owner['idnum'], $mypet['idnum'], $hour, ($hour == 0 ? 'realtime' : 'hourly'), false, '<b class="success">' . $desc . '</b>');

  fetch_none('
    INSERT INTO psypets_pet_level_logs
    (timestamp, petid, answer)
    VALUES
    (
      ' . time() . ',
      ' . $mypet['idnum'] . ',
      ' . quote_smart($desc) . '
    )
  ');
  
  if($immediate)
    save_pet($mypet, array('love_exp', 'love_level'));
    
  return true;
}

function gain_love_exp(&$mypet, $amount, $hour = 0, $immediate = false)
{
  if($amount <= 0 || $mypet['zombie'] == 'yes' || $mypet['changed'] == 'yes')
    return;

  if($mypet['food'] > 0 && ($mypet['energy'] > 0 || $mypet['caffeinated'] > 0) && $mypet['safety'] > 0 && $mypet['love'] > 0 && $mypet['esteem'] > 0)
  {
    $mypet['love_exp'] += $amount;

    if($immediate)
      fetch_none('UPDATE monster_pets SET love_exp=love_exp+' . $amount . ' WHERE idnum=' . $mypet['idnum'] . ' LIMIT 1');

    return $amount;
  }
}

function lose_stat(&$mypet, $stat, $amount)
{
  if($amount <= 0)
    return 0;

  $mypet[$stat] -= $amount;

  return $amount;
//   print " (lost " . $amount . " " . $stat . ") ";
}

function karma_for_reincarnating($masteries)
{
  return ($masteries + 1) * ($masteries / 2);
}

function level_exp($level)
{
  return ($level * $level) + $level + 8;
}

function level_stat_exp($l)
{
  return ($l + 1) * ($l + 2) * 10 - 10;
}

function get_pet_friends($petid)
{
  $command = 'SELECT * FROM psypets_pet_relationships WHERE petid=' . $petid;
  return fetch_multiple($command, 'fetching pet #' . $petid . '\'s friends');
}

function PetAge($birthday, $now)
{
  $pet_seconds = $now - $birthday;

  $pet_age = '';

  if($pet_seconds > (60 * 60 * 24 * 365))
  {
   $pet_years = floor($pet_seconds / (60 * 60 * 24 * 365));
    $pet_seconds -= $pet_years * (60 * 60 * 24 * 365);
    $pet_age .= "$pet_years year" . ($pet_years > 1 ? 's' : '') . ' ';
  }
  else
    $pet_years = 0;

  if($pet_seconds > (60 * 60 * 24 * (365 / 12)))
  {
    $pet_months = floor($pet_seconds / (60 * 60 * 24 * (365 / 12)));
    $pet_seconds -= $pet_months * (60 * 60 * 24 * (365 / 12));

    $pet_age .= "$pet_months month" . ($pet_months > 1 ? 's' : '') . ' ';
  }

  if($pet_seconds > (60 * 60 * 24 * 7))
  {
    $pet_weeks = floor($pet_seconds / (60 * 60 * 24 * 7));
    $pet_seconds -= $pet_weeks * (60 * 60 * 24 * 7);

    $pet_age .= "$pet_weeks week" . ($pet_weeks > 1 ? 's' : '') . ' ';
  }

  if($pet_seconds > (60 * 60 * 24))
  {
    $pet_days = floor($pet_seconds / (60 * 60 * 24));
    $pet_seconds -= $pet_days * (60 * 60 * 24);

    $pet_age .= "$pet_days day" . ($pet_days > 1 ? 's' : '') . ' ';
  }

  if($pet_age == '')
    $pet_age = 'less than a day';
    
  return $pet_age;
}

function PetYears($birthday, $now)
{
  $pet_seconds = $now - $birthday;

  $pet_years = floor($pet_seconds / (60 * 60 * 24 * 365));

  return $pet_years;
}

function render_choose_pet_xhtml($pets, $filter = array(), $choose_multiple = false)
{
  if(count($pets) == 0)
    return '<p class="failure">You have no pets!</p>';

  $xhtml = '<table><tbody>';

  $rowclass = begin_row_class();

  foreach($pets as $pet)
  {
    $classes = array($rowclass);
    $bad_states = array();
  
    // dead and zombie pets are not alive :P
    if(($pet['dead'] != 'no' || $pet['zombie'] != 'no') && in_array('alive', $filter))
    {
      $classes[] = 'dim';
      $bad_states[] = 'dead';
    }
      
    // werepets are not sane
    if($pet['changed'] != 'no' && in_array('sane', $filter))
    {
      $classes[] = 'dim';
      $bad_states[] = 'crazed';
    }

    // sleeping pets are not awake...
    if($pet['sleeping'] != 'no' && in_array('awake', $filter))
    {
      $classes[] = 'dim';
      $bad_states[] = 'sleeping';
    }

    if(count($bad_states) > 0)
      $disabled = ' disabled="disabled"';
    else
      $disabled = '';
    
    $xhtml .= '<tr class="' . implode(' ', $classes) . '">';
    
    if($choose_multiple)
      $xhtml .= '<td><input type="checkbox" name="petid[]" value="' . $pet['idnum'] . '"' . $disabled . ' /></td>';
    else
      $xhtml .= '<td><input type="radio" name="petid" value="' . $pet['idnum'] . '"' . $disabled . ' /></td>';

    $xhtml .= '<td>' . pet_graphic($pet) . '<td><td>' . $pet['petname'] . '</td><td>' . implode(', ', $bad_states) . '</td></tr>';
      
    $rowclass = alt_row_class($rowclass);
  }
  
  $xhtml .= '</tbody></table>';
  
  return $xhtml;
}
?>
