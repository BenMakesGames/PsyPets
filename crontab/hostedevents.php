<?php
/* hosts daily events
*/

$maintenance_when = (int)date('Gi');

// midnight through 12:20, we are in maintenance
if($maintenance_when >= 0 && $maintenance_when <= 20 && !$IGNORE_MAINTENANCE)
  die('Maintenance, fool!');

// --- BEGIN! ---

//ini_set('include_path', '/your/web/root');

require_once 'commons/dbconnect.php';
require_once 'commons/timelib.php';
require_once 'commons/parklib.php';
require_once 'commons/eventlib.php';
require_once 'commons/itemlib.php';

$force_event = ($_GET['force'] == 'yes');

$now = time();

list($now_day, $now_month, $now_year) = explode(' ', date('j n Y', $now));

if(is_thanksgiving())
{
  echo 'It\'s Thanksgiving Day!<br />';

  if(mt_rand(1, 30) == 1 || $force_event) // 1:30 chance per minute = 1/30 minutes = 2/hour
  {
    $host = 'ttgcorp';
    $size = 8 + mt_rand(0, 6) * 2;
    $min_level = mt_rand(1, 40);
    $max_level = $min_level + mt_rand(5, 10);
    
    $ttg_games = array('Hangman Minigame', 'Pong Minigame', 'Seven-by-Seven Minigame', 'Sokoban Minigame');
    
    $event_prereport = 'Buy a ' . $ttg_games[array_rand($ttg_games)] . ' today!  Just one of TTG Corp\'s many, fun, hand-held games!';
    $event_postreport = '';

    $item_ids = array();
    $item_names = array();

    for($x = 0; $x < $size; ++$x)
    {
      $item_ids[] = add_inventory('ttgcorp', '', 'Cranberry Sauce', '', 'storage/outgoing');
      $item_names[] = 'Cranberry Sauce';
    }

    $event_prizes = implode(',', $item_ids);
    $prize_descript = implode('<br />', $item_names) . '<br />';

    $event_descript = 'Teeny Tiny Games Corp. is proud to host this year\'s Thanksgiving Day events!';

    $command = '
      INSERT INTO `monster_events`
      (
        `name`,
        `descript`,
        `fee`,
        `prizes`,
        `prizedescript`,
        `minlevel`,
        `maxlevel`,
        `minparticipant`,
        `event`,
        `prereport`,
        `postreport`,
        `timestamp`,
        `host`,
        `graphic`
      )
      VALUES
      (
        \'Thanksgiving Day!\',
        ' . quote_smart($event_descript) . ',
        ' . 40 . ',
        ' . quote_smart($event_prizes) . ',
        ' . quote_smart($prize_descript) . ',
        ' . (int)$min_level . ',
        ' . (int)$max_level . ',
        ' . (int)$size . ',
        ' . quote_smart(array_rand($EVENT_TYPES)) . ',
        ' . quote_smart($event_prereport) . ',
        ' . quote_smart($event_postreport) . ',
        ' . $now . ',
        ' . quote_smart($host) . ',
        ' . quote_smart($EVENT_GRAPHICS[array_rand($EVENT_GRAPHICS)]) . '
      )
    ';
    $database->FetchNone($command, 'creating a TTG Corp thanksgiving event');
    
    echo 'Created a Thanksgiving event hosted by TTG Corp.<br />';
  }
  else
    echo 'No Thanksgiving event created.<br />';
}
else if(is_hanukkah())
{
  echo 'It\'s Hanukkah!<br />';

  if(mt_rand(1, 360) == 1 || $force_event) // 1:360 chance per minute = 1/360 minutes = 4/day = 32/hanukkah
  {
    $host = 'psypets';
    $size = 8 + mt_rand(0, 6) * 2;
    $min_level = mt_rand(5, 35);
    $max_level = $min_level + mt_rand(5, 10);

    $event_prereport = '';
    $event_postreport = '';

    $item_ids = array();
    $item_names = array();

    $possible_items = array('Candle', 'Silver Candle', 'Brown Sugar Candle', 'Red Candle', 'Blueberry Candle', 'Yellow Candle');

    for($x = 0; $x < $size; ++$x)
    {
      $this_item = $possible_items[array_rand($possible_items)];

      $item_ids[] = add_inventory('psypets', '', $this_item, '', 'storage/outgoing');
      $item_names[] = $this_item;
    }

    $event_prizes = implode(',', $item_ids);
    $prize_descript = implode('<br />', $item_names) . '<br />';

    $event_descript = '';

    $command = '
      INSERT INTO `monster_events`
      (
        `name`,
        `descript`,
        `fee`,
        `prizes`,
        `prizedescript`,
        `minlevel`,
        `maxlevel`,
        `minparticipant`,
        `event`,
        `prereport`,
        `postreport`,
        `timestamp`,
        `host`,
        `graphic`
      )
      VALUES
      (
        \'Hanukkah candles!\',
        ' . quote_smart($event_descript) . ',
        ' . 50 . ',
        ' . quote_smart($event_prizes) . ',
        ' . quote_smart($prize_descript) . ',
        ' . (int)$min_level . ',
        ' . (int)$max_level . ',
        ' . (int)$size . ',
        ' . quote_smart(array_rand($EVENT_TYPES)) . ',
        ' . quote_smart($event_prereport) . ',
        ' . quote_smart($event_postreport) . ',
        ' . $now . ',
        ' . quote_smart($host) . ',
        ' . quote_smart($EVENT_GRAPHICS[array_rand($EVENT_GRAPHICS)]) . '
      )
    ';
    $database->FetchNone($command, 'creating a Hanukkah event');

    echo 'Created a Hanukkah event hosted by psypets.<br />';
  }
  else
    echo 'No Hannukah event created.<br />';
}
else if($now_month == 2 && ($now_day == 13 || $now_day == 14 || $now_day == 15))
{
  echo 'It\'s Valentine\'s and/or Lupercalia!<br />';

  if(mt_rand(1, 720) == 1) // 1:720 chance per minute = 1/720 minutes = 2/day
  {
    $host = 'gijubi';

    $size = 6 + mt_rand(0, 4) * 2;
    $min_level = mt_rand(15, 30);
    $max_level = $min_level + mt_rand(5, 10);

    $event_prereport = '';
    $event_postreport = '';

    $item_ids = array();
    $item_names = array();

    $possible_items = array('Heart', 'Diamond');

    for($x = 0; $x < $size / 2; ++$x)
    {
      $this_item = $possible_items[array_rand($possible_items)];

      $item_ids[] = add_inventory($host, '', $this_item, '', 'storage/outgoing');
      $item_names[] = $this_item;
    }

    $event_prizes = implode(',', $item_ids);
    $prize_descript = implode('<br />', $item_names) . '<br />';

    $event_descript = '';

    $command = '
      INSERT INTO `monster_events`
      (
        `name`,
        `descript`,
        `fee`,
        `prizes`,
        `prizedescript`,
        `minlevel`,
        `maxlevel`,
        `minparticipant`,
        `event`,
        `prereport`,
        `postreport`,
        `timestamp`,
        `host`,
        `graphic`
      )
      VALUES
      (
        \'Intoxicating Lupercalia!\',
        ' . quote_smart($event_descript) . ',
        ' . 500 . ',
        ' . quote_smart($event_prizes) . ',
        ' . quote_smart($prize_descript) . ',
        ' . (int)$min_level . ',
        ' . (int)$max_level . ',
        ' . (int)$size . ',
        ' . quote_smart(array_rand($EVENT_TYPES)) . ',
        ' . quote_smart($event_prereport) . ',
        ' . quote_smart($event_postreport) . ',
        ' . $now . ',
        ' . quote_smart($host) . ',
        \'redflower.gif\'
      )
    ';
    $database->FetchNone($command, 'creating a Lupercalia event');

    echo 'Created a Lupercalia event hosted by ' . $host . '.<br />';
  }
  else
    echo 'No Lupercalia event created.<br />';

  if(($now_day == 14 || ($now_day == 15 && $now_year == 2011)) && mt_rand(1, 144) == 1) // 1:144 chance per minute = 1/144 minutes = 10/day
  {
    $host = 'vroselle';

    $size = 10 + mt_rand(0, 3) * 2;
    $min_level = mt_rand(5, 20);
    $max_level = $min_level + mt_rand(5, 15);

    $event_prereport = '';
    $event_postreport = '';

    $item_ids = array();
    $item_names = array();

    $possible_items = array('Candy Heart', 'Purple Lilac', 'Arbutus', 'Honeysuckle', 'Periwinkle');

    for($x = 0; $x < $size; ++$x)
    {
      $this_item = $possible_items[array_rand($possible_items)];

      $item_ids[] = add_inventory($host, '', $this_item, '', 'storage/outgoing');
      $item_names[] = $this_item;
    }

    $event_prizes = implode(',', $item_ids);
    $prize_descript = implode('<br />', $item_names) . '<br />';

    $event_descript = '';

    $command = '
      INSERT INTO `monster_events`
      (
        `name`,
        `descript`,
        `fee`,
        `prizes`,
        `prizedescript`,
        `minlevel`,
        `maxlevel`,
        `minparticipant`,
        `event`,
        `prereport`,
        `postreport`,
        `timestamp`,
        `host`,
        `graphic`
      )
      VALUES
      (
        ' . quote_smart('Happy Valentine\'s!') . ',
        ' . quote_smart($event_descript) . ',
        ' . 10 . ',
        ' . quote_smart($event_prizes) . ',
        ' . quote_smart($prize_descript) . ',
        ' . (int)$min_level . ',
        ' . (int)$max_level . ',
        ' . (int)$size . ',
        ' . quote_smart(array_rand($EVENT_TYPES)) . ',
        ' . quote_smart($event_prereport) . ',
        ' . quote_smart($event_postreport) . ',
        ' . $now . ',
        ' . quote_smart($host) . ',
        \'flower.gif\'
      )
    ';
    $database->FetchNone($command, 'creating a Hanukkah event');

    echo 'Created a Valentine event hosted by ' . $host . '.<br />';
  }
  else
    echo 'No Valentine event created.<br />';
}
else
{
  if(mt_rand(1, 4320) == 1) // about 1 event every 3 days
  {
    $host = 'psypets';
    
    $possible_titles = array(
      '1, 2, 3 - where\'s your breakfast!',
      'Ready, set, go!',
      'Stop, drop, and roll!',
      'Blood, sweat, and tears!',
      'Sex, drugs, and rock\'n\'roll!',
      'Rock, paper, scissors!',
      'Three little kittens lost their mittens!',
    );

    $size = 10 + mt_rand(0, 5) * 2;
    $min_level = mt_rand(10, 30);
    $max_level = $min_level + mt_rand(10, 20);

    $event_prereport = '';
    $event_postreport = '';

    $item_ids = array();
    $item_names = array();

    $possible_items = array('1st Place', '2nd Place', '3rd Place');

    foreach($possible_items as $this_item)
    {
      $item_ids[] = add_inventory($host, '', $this_item, '', 'storage/outgoing');
      $item_names[] = $this_item;
    }

    $event_prizes = implode(',', $item_ids);
    $prize_descript = implode('<br />', $item_names) . '<br />';

    $event_descript = '';

    $command = '
      INSERT INTO `monster_events`
      (
        `name`,
        `descript`,
        `fee`,
        `prizes`,
        `prizedescript`,
        `minlevel`,
        `maxlevel`,
        `minparticipant`,
        `event`,
        `prereport`,
        `postreport`,
        `timestamp`,
        `host`,
        `graphic`
      )
      VALUES
      (
        ' . quote_smart($possible_titles[array_rand($possible_titles)]) . ',
        ' . quote_smart($event_descript) . ',
        ' . (mt_rand(4, 10) * 5) . ',
        ' . quote_smart($event_prizes) . ',
        ' . quote_smart($prize_descript) . ',
        ' . (int)$min_level . ',
        ' . (int)$max_level . ',
        ' . (int)$size . ',
        ' . quote_smart($SOLO_EVENT_TYPES[array_rand($SOLO_EVENT_TYPES)]) . ',
        ' . quote_smart($event_prereport) . ',
        ' . quote_smart($event_postreport) . ',
        ' . $now . ',
        ' . quote_smart($host) . ',
        \'flower.gif\'
      )
    ';
    $database->FetchNone($command, 'creating a 1st/2nd/3rd event');

    echo 'Created a 1st, 2nd, 3rd event hosted by ' . $host . '.<br />';
  }
  else
    echo 'No regular event created.<br />';
}
?>