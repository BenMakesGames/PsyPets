<?php
require_once 'commons/random_description.php';

/*
	whoa: anagrams:
	Stripes
	Persist
	Esprits
	Sprites
	Spriest
	Priests
	
	Gelatin
	Genital
	Elating
*/

$WORDS_TO_UNSCRAMBLE = array(
	'NICE',
	'MINT',
	'LEND',
	'MICE',
	'GIFT',
	'SOFT',
	'WARM',
	'WIND',
	'FIND',
	'MIND',
	'AIRY',
	'WAVE',
	'LOOK',
	'WAFT',
	'MAGIC',
	'PAPER',
	'APPLE',
	'SILKY',
	'TANGY',
	'CREST',
	'CRAFT',
	'SHINE',
	'GRACE',
	'TASTY',
	'FIZZY',
	'HAPPY',
	'CLIMB',
	'LUNCH',
	'AROMA',
	'THINK',
	'MUSIC',
	'LAUGH',
	'JUICY',
	'SUGAR',
	'BOUNCE',
	'STARRY',
	'LASERS',
	'KNIGHT',
	'NATURE',
	'NUTMEG',
	'BUBBLY',
	'SATURN',
	'BREEZE',
	'PURPLE',
	'CREAMY',
	'FLOWER',
	'SERENE',
	'FLUFFY',
	'SPRING',
	'GENTLE',
	'POLITE',
	'ORANGE',
	'SQUIRM',
	'MONKEY',
	'ZIPPER',
	'DRAGON',
	'SMOOTH',
	'SINCERE',
	'CALMING',
	'ELUSIVE',
	'ORDERLY',
	'JUPITER',
	'EXPLORE',
	'PUMPKIN',
	'RAINBOW',
);

function check_adventure_scramble(&$adventure, $test)
{
	return(get_adventure_scramble_word($adventure) == strtoupper(trim($test)));
}

function get_adventure_scramble(&$adventure)
{
	return str_shuffle(get_adventure_scramble_word($adventure));
}

function get_adventure_scramble_word(&$adventure)
{
	global $WORDS_TO_UNSCRAMBLE;

	mt_srand($adventure['next_adventure'] - $adventure['idnum']);
	
	// adventure difficulty ranges from 1 to 10
	$difficulty = floor(count($WORDS_TO_UNSCRAMBLE) / 9 * ($adventure['level'] - 1));
	$range = ceil(count($WORDS_TO_UNSCRAMBLE) / 6);
	$min = max(0, $difficulty - $range);
	$max = min(count($WORDS_TO_UNSCRAMBLE) - 1, $difficulty + $range);
	$i = mt_rand($min, $max);
	
	$word = $WORDS_TO_UNSCRAMBLE[$i];
	
	mt_srand();
	
	return $word;
}

function get_adventure($userid)
{
  return fetch_single('SELECT * FROM psypets_adventure WHERE userid=' . (int)$userid . ' LIMIT 1');
}

function delete_adventure($userid)
{
  return $GLOBALS['database']->FetchNone('DELETE FROM psypets_adventure WHERE userid=' . (int)$userid . ' LIMIT 1');
}

function adventure_progress(&$adventure, $progress)
{
  $adventure['progress'] += $progress;
}

function save_adventure_progress(&$adventure)
{
  $GLOBALS['database']->FetchNone('
    UPDATE psypets_adventure
    SET progress=' . (int)$adventure['progress'] . '
    WHERE idnum=' . (int)$adventure['idnum'] . '
    LIMIT 1
  ');
}

function create_adventure($userid, $level)
{
  global $now;

  if(get_adventure($userid) !== false)
    delete_adventure($userid);
  
  $difficulty = mt_rand($level * 7, $level * 9);
  
  list($stats, $description) = random_adventure_puzzle($level);
  $prize = random_adventure_prize($level);
  
  $GLOBALS['database']->FetchNone('
    INSERT INTO psypets_adventure
    (userid, level, next_adventure, difficulty, stats, description, prize)
    VALUES
    (
      ' . (int)$userid . ',
      ' . (int)$level . ',
      ' . ($now + 12 * 60 * 60) . ',
      ' . $difficulty . ',
      ' . quote_smart(implode(',', $stats)) . ',
      ' . quote_smart($description) . ',
      ' . quote_smart($prize) . '
    )
  ');
}

function random_adventure_prize($level)
{
  if($level == 1 || $level == 2)
    return 'token:plastic';
  else if($level == 3 || $level == 4 || $level == 5)
    return 'token:copper';
  else if($level == 6 || $level == 7)
    return 'token:silver';
  else if($level == 8 || $level == 9)
    return 'token:gold';
  else if($level == 10)
    return 'token:platinum';
}

function render_adventure_prize($prize)
{
  switch($prize)
  {
    case 'token:plastic': $item = 'a Plastic Token'; break;
    case 'token:copper': $item = 'a Copper Token'; break;
    case 'token:silver': $item = 'a Silver Token'; break;
    case 'token:gold': $item = 'a Gold Token'; break;
    case 'token:platinum': $item = 'a Platinum Token'; break;
  }
  
  return '<p>Along the way, you find ' . $item . '!</p>';
}

function claim_prize(&$adventure)
{
  $prize = $adventure['prize'];
  $adventure['prize'] = '';

  $GLOBALS['database']->FetchNone('UPDATE psypets_adventure SET prize=\'\' WHERE idnum=' . $adventure['idnum'] . ' LIMIT 1');

  switch($prize)
  {
    case 'token:plastic': $GLOBALS['database']->FetchNone('UPDATE psypets_dailychallenge SET plastic=plastic+1 WHERE userid=' . $adventure['userid'] . ' LIMIT 1'); break;
    case 'token:copper': $GLOBALS['database']->FetchNone('UPDATE psypets_dailychallenge SET copper=copper+1 WHERE userid=' . $adventure['userid'] . ' LIMIT 1'); break;
    case 'token:silver': $GLOBALS['database']->FetchNone('UPDATE psypets_dailychallenge SET silver=silver+1 WHERE userid=' . $adventure['userid'] . ' LIMIT 1'); break;
    case 'token:gold': $GLOBALS['database']->FetchNone('UPDATE psypets_dailychallenge SET gold=gold+1 WHERE userid=' . $adventure['userid'] . ' LIMIT 1'); break;
    case 'token:platinum': $GLOBALS['database']->FetchNone('UPDATE psypets_dailychallenge SET platinum=platinum+1 WHERE userid=' . $adventure['userid'] . ' LIMIT 1'); break;
    default: die('unknown challenge prize: ' . $prize);
  }
}

function random_adventure_puzzle($level)
{
  switch(mt_rand(1, 9))
  {
    case 1:
      $stats = array('str', 'sta', 'athletics', 'bra');
      $text = random_monster_text($level, $stats);
      break;

    case 2:
      $stats = array('mechanics', 'int', 'per', 'dex');
      $text = random_trap_text($level, $stats);
      break;

    case 3:
      $stats = array('str', 'dex', 'athletics');
      $text = random_athletics_text($level, $stats);
      break;

    case 4:
      $stats = array('per', 'int', 'gathering');
      $text = random_navigation_text($level, $stats);
      break;

    case 5:
      $stats = array('int', 'wit', 'sta', 'binding');
      $text = random_magic_binding_text($level, $stats);
      break;

    case 6:
      $stats = array('int', 'per', 'music');
      $text = random_music_puzzle_text($level, $stats);
      break;

    case 7:
      $stats = array('dex', 'stealth');
      $text = random_stealth_text($level, $stats);
      break;

    case 8:
      $stats = array('int', 'dex', 'tai');
      $text = random_tailory_text($level, $stats);
      break;

    case 9:
      $stats = array('int', 'dex', 'leather');
      $text = random_leather_working_text($level, $stats);
      break;
  }
  
  $replacements = array(
    '%attacked%' => array('attacked', 'waylaid', 'ambushed', 'set upon', 'caught unawares'),
    '%person-description%' => random_description(),
    '%requests%' => array('requests', 'asks for', 'needs'),
    '%help%' => array('help', 'assistance'),
    '%supplies%' => array('supplies', 'gear', 'food', 'tools'),
    '%him-or-her%' => array('him', 'her'),
    '%wizard-description%' => random_wizard_description(),
    '%sound-judgement%' => array('wise', 'prudent', 'best'),
  );

  foreach($replacements as $search=>$replace_options)
  {
    $replace = (is_array($replace_options) ? $replace_options[array_rand($replace_options)] : $replace_options);

    $text = str_replace($search, $replace, $text);
  }

  return array($stats, $text);
}

function random_music_puzzle_text($level, &$stats)
{
  return 'You wander into a cave, when suddenly a door closes behind you!  There doesn\'t look to be a way out, although there <eem>is</a> a rather ' . random_bad() . '-looking, cobweb-covered Pipe Organ made of bones; resting atop the organ is a yellowed piece of paper with some music notes scrawled crudely across the top.  Perhaps, if you play the notes correctly, you can escape!';
}

function random_stealth_text($level, &$stats)
{
  return 'You encounter a ' . random_monster_description($level, $stats) . ', fast asleep.  Rather than fight a needless battle, you attempt to sneak past it...';
}

function random_leather_working_text($level, &$stats)
{
  $articles = array('shoes' => 2, 'leather backpack' => 1, 'hiking boots' => 2, 'belt' => 1);
  
  $article = array_rand($articles);

  $it_them = ($articles[$article] == 2 ? 'them' : 'it');
  $develops = ($articles[$article] == 2 ? 'develop' : 'develops');
  
  return 'During your travels, your ' . $article . ' ' . $develops . ' a tear.  It seems %sound-judgement% to repair ' . $it_them . ' before continuing...';
}

function random_tailory_text($level, &$stats)
{
  return 'You hear someone crying just off the road.  Investigating reveals a princess who has torn her ball gown - and the ball has already started!  Can you repair her gown in time?';
}

function random_monster_text($level, &$stats)
{
  switch(mt_rand(1, 2))
  {
    case 1: return 'While traveling ' . random_wilderness_location($level, $dummy_stats) . ', you are %attacked% by a ' . random_monster_description($level, $stats) . ', and forced to defend yourself!';
    case 2: return ucfirst('%person-description% %requests% your %help%: a ' . random_monster_description($level, $stats) . ' has been terrorizing the area!  Can you put a stop to it?');
  }
}

function random_bad()
{
  $bads = array(
    'terrible', 'dark', 'shadowy',
    'mysterious'
  );
  
  return $bads[array_rand($bads)];
}

function random_magic_binding_text($level, &$stats)
{
  switch(mt_rand(1, 3))
  {
    case 1: return ucfirst('%wizard-description% %requests% your %help%: a summoned ' . random_monster_description($level, $stats) . ' must be sent back to whence it came!');
    case 2: return 'A magically-sealed door bars your way.  It must be dispelled in order to continue.';
    case 3: return 'You\'ve fallen victim to a ' . random_bad() . ' curse!  It must be dispelled!';
  }
}

function random_trap_description($level, &$stats)
{
  $locations = array(
    array('mouse trap', array()),
    array('bear trap', array('sur')),
    array('pit trap', array()),
    array('snake pit trap', array()),
    array('laser grid', array('eng')),
    array('falling ceiling', array()),
    array('flooding room', array()),
    array('magic door', array('binding')),
    array('Rube Goldberg machine', array()),
    array('bottomless pit filled with spikes', array()),
  );
  
  $location = $locations[mt_rand(floor($level / 2), $level - 1)];
  
  foreach($location[1] as $stat)
    $stats[] = $stat;
  
  return $location[0];
}

function random_trap_text($level, &$stats)
{
  return 'You nearly set off a trap - a ' . random_trap_description($level, $stats) . ' - but notice it just in time.  Still, it must be disarmed before you can continue...';
}

function random_athletics_text($level, &$stats)
{
  return 'A ' . random_monster_description($level, $dummy_stats) . ' snuck up on you, and ran off with your %supplies%!  You\'ve got to give chase, and get back your supplies!';
}

function random_navigation_text($level, &$stats)
{
  switch(mt_rand(1, 2))
  {
    case 1: return 'You\'ve become lost ' . random_wilderness_location($level, $stats) . '...';
    case 2: return ucfirst('%person-description% %requests% your %help%: their friend is lost somewhere ' . random_wilderness_location($level, $stats) . '!  Won\'t you help find %him-or-her%?');
  }
}

function random_wilderness_location($level, &$stats)
{
  $locations = array(
    array('in your own back yard', array()),
    array('on a local hiking trail', array()),
    array('in a hedge maze', array()),
    array('in an unfamiliar wood', array()),
    array('in a small cave system', array('mining')),
    array('in a thick jungle', array()),
    array('in a mountain pass', array()),
    array('in the desert', array('sur', 'sta')),
    array('in a dark swamp', array()),
    array('at sea', array('astronomy')),
  );

  $location = $locations[mt_rand(floor($level / 2), $level - 1)];
  
  foreach($location[1] as $stat)
    $stats[] = $stat;

  return $location[0];
}

function random_monster_description($level, &$stats)
{
  $adjectives = array('thieving', 'terrible', 'large', 'writhing', 'white', 'red', 'pleated', 'forgotten', 'remembered');
  $monsters = array('Ooze', 'Rock', 'Imp', 'Azukiarai', 'Kami', 'Minotaur', 'Monkey', 'Mapinguari', 'Ekimmu', 'Scolopendra');
  $titles = array('of legends', 'of the world', 'of the ages', 'of old', 'of dreams');
  
  $words = array();
  
  if($level > 3)
    $words[] = $adjectives[array_rand($adjectives)];
  
  $words[] = $monsters[array_rand($monsters)];
  
  if($level > 7)
    $words[] = $titles[array_rand($titles)];
    
  return implode(' ', $words);
}

function random_wizard_description()
{
  $random_personalities = array(
    'a calm',
    'a concerned',
    'a determined',
    'an emotional',
    'an emotionless',
    'an energetic',
    'a friendly',
    'a grim',
    'an impatient',
    'a nervous',
    'a proud',
    'a reserved',
    'a sincere',
    'a smiling',
    'a spirited',
    'a suspicious',
    'a wild',
    'a quiet',
  );

  $random_traits = array(
    'brown-haired', // hair
    'black-haired',
    'red-haired',
    'white-haired',
    'blonde',
    'bald',
    'blue-eyed',    // eyes
    'brown-eyed',
    'red-eyed',
    'green-eyed',
    'blind',
    'one-eyed',
    'skinny',       // physical build
    'muscular',
    'round',
    'tall',
    'short',
    'pale',         // skin
    'tanned',
    'dark-skinned',
    'exotic',
    'blue-skinned',
    'wizened',
    'smooth-skinned',
  );

  return
    $random_personalities[array_rand($random_personalities)] . ', ' .
    $random_traits[array_rand($random_traits)] . ' wizard'
  ;
}

function get_challenge_tokens($userid)
{
  $command = 'SELECT * FROM psypets_dailychallenge WHERE userid=' . (int)$userid . ' LIMIT 1';
  return fetch_single($command, 'fetching resident\'s challenge tokens');
}

function create_challenge_tokens($userid)
{
  $command = 'INSERT INTO psypets_dailychallenge (userid) VALUES (' . (int)$userid . ')';
  $GLOBALS['database']->FetchNone($command, 'initializing resident\'s challenge tokens');
}


function update_challenge_tokens(&$info)
{
  $command = 'UPDATE psypets_dailychallenge SET ' .
               'plastic=' . $info['plastic'] . ', ' .
               'copper=' . $info['copper'] . ', ' .
               'silver=' . $info['silver'] . ', ' .
               'gold=' . $info['gold'] . ', ' .
               'platinum=' . $info['platinum'] . ' ' .
             'WHERE userid=' . $info['userid'] . ' LIMIT 1';
  $GLOBALS['database']->FetchNone($command, 'updating challenge tokens progress');
}

?>
