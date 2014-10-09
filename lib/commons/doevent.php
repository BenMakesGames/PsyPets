<?php
require_once 'commons/itemlib.php';
require_once 'commons/userlib.php';
require_once 'commons/utility.php';
require_once 'commons/petlib.php';
require_once 'commons/relationshiplib.php';

function record_pet_park_result($petid, $eventid, $eventtype, $size, $placement, $result, $prizeids, $prizenames)
{
  global $now;

  $q_prize_ids = '\'' . implode(',', $prizeids) . '\'';
  $q_prize_names = quote_smart(implode(',', $prizenames));

  $command = '
    INSERT INTO psypets_park_event_results
    (petid, eventid, eventtype, timestamp, size, placement, result, prizeid, prizename)
    VALUES
    (
      ' . $petid . ',
      ' . $eventid . ',
      \'' . $eventtype . '\',
      ' . $now . ',
      ' . $size . ',
      ' . $placement . ',
      ' . quote_smart($result) . ',
      ' . $q_prize_ids . ',
      ' . $q_prize_names . '
    )
  ';
  fetch_none($command, 'recording pet-park result');
}

function unenter_pet($petid, $eventid)
{
  $command = 'SELECT * FROM monster_events ' .
             'WHERE idnum=' . (int)$eventid . ' LIMIT 1';

  $event = fetch_single($command, 'do event > withdraw pet');

  $participants = explode(',', $event['participants']);

  $first = 1;
  foreach($participants as $petid_f)
  {
    $thisid = substr($petid_f, 1, strlen($petid_f) - 2);

    if($thisid != $petid)
    {
      if($first == 1)
        $first = 0;
      else
        $participation_list .= ',';

      $participation_list .= '<' . $thisid . '>';
    }
  }

  $command = 'UPDATE monster_events ' .
             'SET `participants`=' . quote_smart($participation_list) . ' ' .
             'WHERE idnum=' . (int)$eventid . ' LIMIT 1';
  fetch_none($command, 'do event > withdraw pet');
}

function DoEvent($idnum)
{
  global $now, $SETTINGS;

  $command = 'SELECT * FROM monster_events ' .
             'WHERE idnum=' . (int)$idnum . ' LIMIT 1';
  $event = fetch_single($command, 'do event');

  if($event === false)
  {
    echo 'Event not found.';
    return;
  }

  $participants = explode(',', $event['participants']);

  // mix up the participants :)
  shuffle($participants);

  $good_to_go = true;

  foreach($participants as $petid_f)
  {
    $petid = (int)substr($petid_f, 1, strlen($petid_f) - 2);

    $command = 'SELECT * FROM monster_pets ' .
               'WHERE `idnum`=' . $petid . ' LIMIT 1';
    $this_pet = fetch_single($command, 'fetching participating pet');

    if($this_pet === false || $this_pet['user'] == 'psypets' || $this_pet['location'] != 'home' || $this_pet['dead'] != 'no' || $this_pet['zombie'] == 'yes')
    {
      unenter_pet($petid, $idnum);
      $good_to_go = false;
      continue;
    }

    $competition_pets[$petid] = $this_pet;

    $competition_users[$petid] = get_user_byuser($competition_pets[$petid]['user']);
  }

  if($good_to_go == false)
    return;

//  echo "Do event: " . $event["event"] . "<br />\n";

  $team_game = false;
  $no_placement = false;

  switch($event['event'])
  {
    case 'race':
      $eventvalues = DoRace($event, $competition_pets, $competition_users);
      $eventtype = 'race';
      break;
    case 'swim':
      $eventvalues = DoRace($event, $competition_pets, $competition_users);
      $eventtype = 'swimming race';
      break;
    case 'ctf':
      $team_game = true;
      $eventvalues = DoCTF($event, $competition_pets, $competition_users);
      $eventtype = 'Capture the Flag game';
      break;
    case 'hunt':
      $no_placement = true;
      $eventvalues = DoHunt($event, $competition_pets, $competition_users);
      $eventtype = 'scavenger hunt';
      break;
    case 'jump':
      $eventvalues = DoLongJump($event, $competition_pets, $competition_users);
      $eventtype = 'Long Jump competition';
      break;
    case 'mining':
      $eventvalues = DoDigcraft($event, $competition_pets, $competition_users);
      $eventtype = 'Digcraft Build competition';
      break;
    case 'fashion':
      $eventvalues = DoFashion($event, $competition_pets, $competition_users);
      $eventtype = 'Fashion Show';
      break;
    case 'fishing':
      $eventvalues = DoFishing($event, $competition_pets, $competition_users);
      $eventtype = 'fishing competition';
      break;
    case 'volleyball':
      $team_game = true;
      $eventvalues = DoVolleyball($event, $competition_pets, $competition_users);
      $eventtype = 'game of Volleyball';
      break;
    case 'strategy':
      $eventvalues = DoStrategyGame($event, $competition_pets, $competition_users);
      $eventtype = 'strategy game competition';
      break;
    case 'brawl':
      $eventvalues = DoBrawl($event, $competition_pets, $competition_users);
      $eventtype = 'brawl';
      break;
    case 'roborena':
      $eventvalues = DoRoborena($event, $competition_pets, $competition_users);
      $eventtype = 'roborena';
      break;
    case 'archery':
      $eventvalues = DoArchery($event, $competition_pets, $competition_users);
      $eventtype = 'Archery competition';
      break;
    case 'ddr':
      $eventvalues = DoDDR($event, $competition_pets, $competition_users);
      $eventtype = 'Dance Mania competition';
      break;
    case 'picturades':
      $team_game = true;
      $eventvalues = DoPicturades($event, $competition_pets, $competition_users);
      $eventtype = 'a Picturades game';
      break;
    case 'crafts':
      $eventvalues = DoGenericCrafts($event, $competition_pets, $competition_users);
      $eventtype = 'crafts competition';
      break;
    case 'tow':
      $team_game = true;
      $eventvalues = DoTugofWar($event, $competition_pets, $competition_users);
      $eventtype = 'Tug of War competition';
      break;
    case 'cookoff':
      $eventvalues = DoCookOff($event, $competition_pets, $competition_users);
      $eventtype = 'cook-off';
      break;
    default:
      die('What kind of event is this?!  ' . $event['event'] . '!?  Error!  ERROR, I SAY!');
  }

  $places = $eventvalues[0];
  $prizes = $eventvalues[1];
  $report = $eventvalues[2];

  $command = 'UPDATE monster_events ' .
             'SET `report`=' . quote_smart($report) . ', ' .
                 "`timestamp`=$now,finished='yes' " .
             'WHERE idnum=' . (int)$event['idnum'] . ' LIMIT 1';
  fetch_none($command, 'recording event results');

  $user_reports = array();

  foreach($competition_users as $idnum=>$place)
  {
    $message = '<a href="/petevents.php?petid=' . $idnum . '">' . $competition_pets[$idnum]['petname'] . '</a>';

    if(!$no_placement)
    {
      if($team_game == true)
        $message .= ' was on the ' . ($places[$idnum] == 1 ? 'winning' : 'losing') . " team of this $eventtype and";
      else
        $message .= ' placed ' . numeric_place($places[$idnum]) . " in this $eventtype and";
    }

    $my_prizes = array();
    $my_prize_list = array();

    if(count($prizes[$idnum]) > 0)
    {
      $message .= ' received ';
    
      $first = true;
      foreach($prizes[$idnum] as $prize)
      {
        // send the items while we're at it :)
        $command = 'UPDATE `monster_inventory` ' .
                   'SET `user`=' . quote_smart($competition_users[$idnum]['user']) . ',' .
                   'location=\'storage/incoming\',changed=' . $now . ',' .
                   'message2=' . quote_smart($competition_pets[$idnum]['petname'] . ' won this in the ' . $event['name'] . ' ' . $eventtype) . ' ' .
                   'WHERE idnum=' . $prize . ' LIMIT 1';
        fetch_none($command, 'handing out prizes');

        if($first == true)
          $first = false;
        else
          $message .= ', ';

        $prize_item = get_inventory_byid($prize, 'itemname');

        $message .= $prize_item['itemname'];
        
        $my_prizes[] = $prize;
        $my_prize_list[] = $prize_item['itemname'];
      }

      flag_new_incoming_items($competition_users[$idnum]['user']);
    } // if the user received any prizes
    else
      $message .= ' received no prize';

    record_pet_park_result($idnum, $event['idnum'], $event['event'], $event['minparticipant'], (int)$places[$idnum], '', $my_prizes, $my_prize_list);

    $user_reports[$idnum][] = $message . '.';
  } // for each user

  foreach($user_reports as $userid=>$reports)
  {
    $message = 'The event has finished, and the results are in!  ';

    if(count($reports) == 1)
      $message .= $reports[0] . '  <i>(<a href="//' . $SETTINGS['site_domain'] . '/eventdetails.php?idnum=' . $event['idnum'] . '">See more details</a>.)</i>';
    else
    {
      $message .= '<i>(<a href="//' . $SETTINGS['site_domain'] . '/eventdetails.php?idnum=' . $event['idnum'] . '">See more details</a>.)</i>';
      $message .= '<ul><li>' . implode('</li><li>', $reports) . '</li></ul>';
    }
    
    $message .= '<hr /><h5>Event Report</h5>' . $report;

    psymail_user(
      $competition_users[$userid]['user'],
      $SETTINGS['site_ingame_mailer'],
      $event['name'] . ' event results',
      $message,
      count($prizes[$userid])
    );
  }

  if($event['host'] != $SETTINGS['site_ingame_mailer'])
  {
    $amount = $event['fee'] * $event['minparticipant'];

    $command = 'UPDATE monster_users ' .
               "SET money=money+$amount " .
               'WHERE `user`=' . quote_smart($event['host']) . ' LIMIT 1';
    fetch_none($command, 'awarding entry fees to host');

    add_transaction($event['host'], $now, 'Park Event', $amount);

    require_once 'commons/questlib.php';

    $host_id = get_user_byuser($event['host'], 'idnum');

    $hosted = get_quest_value($host_id['idnum'], 'event host count');
    $hosted_count = ((int)$hosted['value']) + 1;

    if($hosted === false)
      add_quest_value($host_id['idnum'], 'event host count', $hosted_count);
    else
      update_quest_value($hosted['idnum'], $hosted_count);

    $badges = get_badges_byuserid($host_id['idnum']);
    if($badges['ranger'] == 'no' && $hosted_count >= 10)
    {
      $extra_message = "\n\nAlso, congratulations on hosting your 10th Park event.  You've earned the Park Ranger Badge!";
      set_badge($host_id['idnum'], 'ranger');
    }
    
    if($event['trophies'] > 0)
    {
      add_inventory_quantity($event['host'], '', 'Park Token', 'Received for hosting an event with trophies.', 'storage/incoming', $event['trophies']);
      $and_tokens = ', and ';

      if($event['trophies'] == 1)
        $and_tokens .= 'one Park Token for the trophy you offered as a prize';
      else
        $and_tokens .= $event['trophies'] . ' Park Tokens for the trophies you offered as prizes';
    }
    else
      $and_tokens = '';

    psymail_user(
      $event['host'],
      'psypets',
      'Your event, ' . $event['name'] . ', completed',
      'You collected ' . $amount . '{m} from entrance fees for this event' . $and_tokens . '. (<a href="eventdetails.php?idnum=' . $event['idnum'] . '">See event results</a>.)' . $extra_message
    );
  }
  
  $friends = array_rand($participants, 2);

  $participant_1 = $participants[$friends[0]];
  $participant_2 = $participants[$friends[1]];
  
  $friend_1 = (int)substr($participant_1, 1, strlen($participant_1) - 2);
  $friend_2 = (int)substr($participant_2, 1, strlen($participant_2) - 2);

  $pet_1 = get_pet_byid($friend_1);
  $pet_2 = get_pet_byid($friend_2);

  do_friendly_hang_out(0, $pet_1, $pet_2);
}

// returns ($places, $prizes, $report)
function DoDDR($event, $pets, $users)
{
  $avg_level = floor(($event['minlevel'] + $event['maxlevel']) / 2);
  $exp_mult = ceil($avg_level / 4);

  if(strlen($event['prizes']) > 0)
    $prizes = explode(',', $event['prizes']);
  else
    $prizes = array();

  $user_prizes = array();
  $user_places = array();
  $event_report = '';

  $ddr_perfect = array();
  $ddr_great = array();
  $ddr_good = array();
  $ddr_boo = array();
  $ddr_miss = array();
  
  $difficulty = array();
  $sta_check = array();
  $score = array();

  foreach($pets as $idnum=>$pet)
  {
    $difficulty[$idnum] = 3;
    $sta_check[$idnum] = 1;
  }

  for($i = 1; $i <= 16; $i++)
  {
    $steps = rand() % (3 * $avg_level + 1) + (2 * $avg_level);
    foreach($pets as $idnum=>$pet)
    {
      $bonus = ($pet['merit_catlike_balance'] == 'yes' ? 1 : 0);

      $ddr_miss[$idnum] = $steps;

      $perfect = success_roll($pet['wit'] + $pet['athletics'] + ceil($pet['music'] / 3) + $bonus, 10, $difficulty[$idnum] - 2) * 2;
      if($perfect > $ddr_miss[$idnum])
        $perfect = $ddr_miss[$idnum];
      $ddr_miss[$idnum] -= $perfect;
      $ddr_perfect[$idnum] += $perfect;

      $great = success_roll($pet['wit'] + $pet['athletics'] + ceil($pet['music'] / 3) + $bonus, 10, $difficulty[$idnum] - 1) * 2;
      if($great > $ddr_miss[$idnum])
        $great = $ddr_miss[$idnum];
      $ddr_miss[$idnum] -= $great;
      $ddr_great[$idnum] += $great;

      $good = success_roll($pet['wit'] + $pet['athletics'] + ceil($pet['music'] / 3) + $bonus, 10, $difficulty[$idnum]) * 2;
      if($good > $ddr_miss[$idnum])
        $good = $ddr_miss[$idnum];
      $ddr_miss[$idnum] -= $good;
      $ddr_good[$idnum] += $good;

      $boo = success_roll($pet['wit'] + $pet['athletics'] + ceil($pet['music'] / 3) + $bonus, 10, $difficulty[$idnum]) * 2;
      if($boo > $ddr_miss[$idnum])
        $boo = $ddr_miss[$idnum];
      $ddr_miss[$idnum] -= $boo;
      $ddr_boo[$idnum] += $boo;

      if(success_roll($pet['sta'] * 3, 10, 6) >= $sta_check[$idnum])
      {
        $sta_check[$idnum]++;
      }
      else
      {
        $sta_check[$idnum] = 1;
        $difficulty[$idnum]++;
      }
    }
  }

  foreach($pets as $idnum=>$pet)
    $score[$idnum] = $ddr_perfect[$idnum] * 50 + $ddr_great[$idnum] * 25 - $ddr_boo[$idnum] * 25 - $ddr_miss[$idnum] * 50;

  arsort($score);
  
  $event_report .= '<ol>';

  $place = 1;

  foreach($score as $idnum=>$points)
  {
    $event_report .= "<li><p><a href=\"/petprofile.php?petid=$idnum\"><b>" . $pets[$idnum]['petname'] . "</b></a><br />\n" .
                     ' Perfect: ' . $ddr_perfect[$idnum] . "<br />\n" .
                     ' Great: ' . $ddr_great[$idnum] . "<br />\n" .
                     ' Good: ' . $ddr_good[$idnum] . "<br />\n" .
                     ' Boo: ' . $ddr_boo[$idnum] . "<br />\n" .
                     ' Miss: ' . $ddr_miss[$idnum] . "<br />\n" .
                     ' Score: ' . ($points <= 0 ? 'failed' : (number_format($points, 0, '.', ',') . ',000')) .
                     '</p></li>';

    $user_places[$idnum] = $place;

    if($place <= count($prizes))
    {
      $user_prizes[$idnum][] = $prizes[$place - 1];
    }

    gain_love($pets[$idnum], success_roll(8, 10, 6));
    gain_safety($pets[$idnum], success_roll(3, 10, 6), true);
    gain_esteem($pets[$idnum], success_roll(9, 10, 6));

    train_pet($pets[$idnum], 'athletics', ceil($exp_mult / 2));
    train_pet($pets[$idnum], 'wit', ceil($exp_mult / 2));
    train_pet($pets[$idnum], 'sta', ceil($exp_mult / 2));
    train_pet($pets[$idnum], 'music', ceil($exp_mult / 4));

    save_pet($pets[$idnum], array('love', 'safety', 'esteem', 'athletics', 'wit', 'sta', 'music', 'music_count', 'athletics_count', 'wit_count', 'sta_count'));

    ++$place;
  }

  $event_report .= "</ol>\n";

  return array($user_places, $user_prizes, $event_report);
}

function DoTugofWar($event, $pets, $users)
{
  $avg_level = floor(($event['minlevel'] + $event['maxlevel']) / 2);
  $exp_mult = ceil($avg_level / 4);

  $prizes = take_apart(',', $event['prizes']);

  $user_prizes = array();
  $user_places = array();
  $event_report = '';

  $teams = array(array(), array());
  $names = array(array(), array());

  $team_colors = array('<span style="color:red;">', '<span style="color:blue;">');
  $team_names = array('Red', 'Blue');
  $team_on = array();
  
//  $assignment_report = '';

  $cur_team = 0;
  foreach($pets as $idnum=>$pet)
  {
    $teams[$cur_team][] = $pet;
    $names[$cur_team][] = array('name' => $pet['petname'], 'pronoun' => p_pronoun($pet['gender']));
    $names_only[$cur_team][] = $pet['petname'];
    $team_on[$idnum] = $cur_team;

//    $assignment_report .= '<li>' . $pet['petname'] . ' (' . $idnum . ') is on team ' . $team_on[$idnum] . '</li>';

    $team_weight[$cur_team] += $pet['str'] + $pet['sta'] + ($pet['dex'] + $pet['athletics']) / 2;

    if($cur_team == 0)
      $cur_team = 1;
    else
      $cur_team = 0;
  }

  $event_report = '<p><span style="color:red;">Red team: ' . implode(', ', $names_only[0]) . "</span><br />\n" .
                  '<span style="color:blue;">Blue team: ' . implode(', ', $names_only[1]) . "</span></p>\n";

  if($team_weight[0] == $team_weight[1])
    $team_weight[mt_rand(0, 1)]++;

  if($team_weight[0] > $team_weight[1])
  {
    $losing_pet = $names[1][array_rand($names[1])];
    $winning_team = 0;
    $event_report .= '<p>Both sides pulled with all their might until <span style="color:blue;">' . $losing_pet['name'] . '</span> loosed ' . $losing_pet['pronoun'] . ' grip, sending ' . $losing_pet['pronoun'] . ' team careening in to the mud.  <span style="color:red;">Red</span> team is victorious!</p>';
  }
  else
  {
    $losing_pet = $names[0][array_rand($names[0])];
    $winning_team = 1;
    $event_report .= '<p>Both sides pulled with all their might until <span style="color:red;">' . $losing_pet['name'] . '</soan> loosed ' . $losing_pet['pronoun'] . ' grip, sending ' . $losing_pet['pronoun'] . ' team careening in to the mud.  <span style="color:blue;">Blue</span> team is victorious!</p>';
  }
/*
  $extra_report = $event_report . '<p>And now, debugging information:</p><ul>' .
    $assignment_report .
    '<li>$winning_team = ' . $winning_team . '(0 = red, 1 = blue).</li>';
*/
  $losing_prize_index = count($pets) / 2;
  $winning_prize_index = 0;

  // hand out prizes
  foreach($pets as $idnum=>$pet)
  {
    gain_esteem($pets[$idnum], success_roll(8, 10, 6));
    gain_love($pets[$idnum], success_roll(8, 10, 6));
    gain_safety($pets[$idnum], success_roll(8, 10, 6));

//    $extra_report .= '<li>' . $pet['petname'] . ' (' . $idnum . ') is on team ' . $team_on[$idnum] . '</li>';

    // prize for a member of the winning team
    if($team_on[$idnum] == $winning_team)
    {
      if(count($prizes) > 0)
      {
        $user_prizes[$idnum][] = $prizes[$winning_prize_index];
        $winning_prize_index++;
      }

      $user_places[$idnum] = 1;
    }
    // prize for a member of the losing team
    else
    {
//      $extra_report .= '<li>that\'s the losing team!</li>';
      if(count($prizes) > count($pets) / 2)
      {
        $user_prizes[$idnum][] = $prizes[$losing_prize_index];
        $losing_prize_index++;
      }

      $user_places[$idnum] = 0;
    }

    train_pet($pets[$idnum], 'str', ceil($exp_mult / 2));
    train_pet($pets[$idnum], 'sta', ceil($exp_mult / 2));
    train_pet($pets[$idnum], 'dex', ceil($exp_mult / 4));
    train_pet($pets[$idnum], 'athletics', ceil($exp_mult / 4));

    save_pet($pets[$idnum], array('love', 'safety', 'esteem', 'str', 'sta', 'dex', 'athletics', 'str_count', 'sta_count', 'dex_count', 'athletics_count'));
  }
/*
  $extra_report .= '</ul>' .
    '<p>$user_places: ' . print_r($user_places, true) . '</p>' .
    '<p>$user_prizes: ' . print_r($user_prizes, true) . '</p>';

  psymail_user('telkoth', 'psypets', 'ToW debugging', $extra_report);
*/
  return array($user_places, $user_prizes, $event_report);
}

function DoPicturades($event, $pets, $users)
{
  global $SETTINGS;

  $avg_level = floor(($event['minlevel'] + $event['maxlevel']) / 2);
  $exp_mult = ceil($avg_level / 4);

  $prizes = take_apart(',', $event['prizes']);

  $things = array(
    'the beach', 'a U.F.O.',
    'a convent', 'the Grocery Store',
    'war', 'a highway', 'an Unreasonably Large Sword',
    'a Sparkling Doodilly', 'the disco',
    'the Hollow Earth', 'a Moat', 'soda',
    'a Deck of Many Things', 'Rubber', 'a school bus',
    'Orion', 'Gemini', 'the solar system', 'a nuclear reaction',
    'an elevator', 'a county fair', 'a ferris wheel',
    'The Eiffel Tower', 'the Twin Towers', 'China',
    'a USB cable', 'the Internet', 'global warming', 'the Ice Age',
    'a paper bag', 'the Horsehead Nebula', 'a bowl of soup',
    'an atoll', 'a hammerhead shark', 'fruit salad',
    'a shrubbery', 'The Plague', 'Hamlet', 'poison', 'a king',
    'a guillotine', 'a folding chair', 'a keyboard',
    'a bundt pan', 'a brain', 'head pigeons', 'line-of-sight',
    'a bust', 'a Sootie', 'pancakes', 'tea', 'a Muffin Tree',
    'Smoke', 'a collar', 'whiskers', 'some Honey', 'a cantaloupe',
    'a frog', 'Odin', 'a word-processor', 'a wizard', 'a hobgoblin',
    'a shoelace', 'an eclipse', 'Earth', 'Gold Star Stickers',
    'a sweater', 'Ki Ri Kashu', 'a satellite', 'a safe',
    'a tap-dancing ballerina fairy princess veterinarian',
    'world peace', 'a flux capacitor', 'a Destiny Prognostication Engine',
    'the Olympics', 'a pair of boxers',
  );
  
  $user_prizes = array();
  $user_places = array();
  $event_report = '';

  $teams = array(array(), array());
  $names = array(array(), array());

  $team_colors = array('<span style="color:red;">', '<span style="color:blue;">');
  $team_names = array('Red', 'Blue');
  $team_on = array();
  
  $cur_team = 0;
  foreach($pets as $idnum=>$pet)
  {
    $teams[$cur_team][] = $pet;
    $names[$cur_team][] = array('name' => $pet['petname'], 'pronoun' => p_pronoun($pet['gender']));
    $names_only[$cur_team][] = $pet['petname'];
    $team_on[$idnum] = $cur_team;
    $pet_ids[] = $idnum;

    if($cur_team == 0)
      $cur_team = 1;
    else
      $cur_team = 0;
  }

  $event_report = '<p><span style="color:red;">Red team: ' . implode(', ', $names_only[0]) . "</span><br />\n" .
                  '<span style="color:blue;">Blue team: ' . implode(', ', $names_only[1]) . "</span></p>\n";

  $pic_i = 1;
  foreach($pets as $idnum=>$pet)
  {
    $event_report .= '<p><b>Picture #' . $pic_i . ':</b> ' . $team_colors[$team_on[$idnum]] . $pet['petname'] . '</span> must draw "' . $things[array_rand($things)] . '"... ';

    $difficulty = mt_rand(20, 50) + $avg_level * 5;
    $got_it = false;
    $time = mt_rand(3, 7);
    
    while(!$got_it)
    {
      shuffle($pet_ids);

      foreach($pet_ids as $guesser)
      {
        if($pet_ids == $idnum) continue;
        $difficulty -= $pets[$idnum]['painting'] + $pets[$idnum]['open'] + $pets[$idnum]['dex'] + $pets[$guesser]['wit'];
        
        if($pets[$guesser]['per'] + $pets[$guesser]['wit'] > $difficulty)
        {
          $event_report .= $team_colors[$team_on[$guesser]] . $pets[$guesser]['petname'] . '</span> guesses it in ' . $time . ' seconds, scoring a point!</p>';
          
          $team_points[$team_on[$guesser]]++;

          gain_esteem($pets[$guesser], success_roll(3, 10, 6));

          $got_it = true;
          break;
        }

        $time++;
      }
    }

    $pic_i++;
  }

  if($team_points[0] == $team_points[1]) // bonus round!
  {
    
    // recruit a random resident to be the artist...
    $command = 'SELECT idnum,user,display FROM monster_users WHERE lastactivity>' . (time() - 60 * 60) . ' ORDER BY RAND() LIMIT 1';
    $random_resident = fetch_single($command, 'fetching random, recently-active resident');

    $thing = $things[array_rand($things)];

    psymail_user(
      $random_resident['user'],
      $SETTINGS['site_ingame_mailer'],
      'You participated in a Picturades tie-breaking bonus-round!',
      'You were asked to draw ' . $thing . ' for the pets involved! ({link http://' . $SETTINGS['site_domain'] . '/eventdetails.php?idnum=' . $event['idnum'] . ' View the event\'s details?})'
    );
  
    require_once 'commons/statlib.php';
    record_stat($random_resident['idnum'], 'Participated in a Picturades Bonus-round', 1);
    // ... awesome.

    $event_report .= '<p>The score is tied at ' . $team_points[0] . '!  The pets must do a tie-breaking bonus-round!</p>' .
                     '<p><b>Picture #' . $pic_i . ':</b> {r ' . $random_resident['display'] . '}, who\'s passing by, is recruited for this bonus-round to draw "' . $thing . '"... ';

    $passerby_skill = mt_rand($avg_level * 2, $avg_level * 6);

    $difficulty = mt_rand(20, 50) + $avg_level * 5;
    $got_it = false;
    $time = 0;
    
    while(!$got_it)
    {
      shuffle($pet_ids);

      foreach($pet_ids as $guesser)
      {
        if($pet_ids == $idnum) continue;
        $difficulty -= $passerby_skill;
        
        if($pets[$guesser]['per'] + $pets[$guesser]['wit'] > $difficulty)
        {
          $event_report .= $team_colors[$team_on[$guesser]] . $pets[$guesser]['petname'] . '</span> guesses it in ' . $time . ' seconds, scoring the tie-breaking point!</p>';
          
          $team_points[$team_on[$guesser]]++;

          gain_esteem($pets[$guesser], success_roll(5, 10, 6));

          $got_it = true;
          break;
        }
        
        $time++;
      }
    }
  }
  
  $event_report .= '<p><span style="color:red;">Red</span> team has ' . $team_points[0] . ' points...<br />' .
                   '<span style="color:blue;">Blue</span> team has ' . $team_points[1] . ' points...</p>';
  
  if($team_points[1] > $team_points[0])
  {
    $winning_team = 1;
    $event_report .= '<p><span style="color:blue;">Blue</span> team is victorious!</p>';
  }
  else
  {
    $winning_team = 0;
    $event_report .= '<p><span style="color:red;">Red</span> team is victorious!</p>';
  }

  $losing_prize_index = count($pets) / 2;
  $winning_prize_index = 0;

  // hand out prizes
  foreach($pets as $idnum=>$pet)
  {
    gain_esteem($pets[$idnum], success_roll(4, 10, 6));
    gain_love($pets[$idnum], success_roll(8, 10, 6));
    gain_safety($pets[$idnum], success_roll(6, 10, 6));

    // prize for a member of the winning team
    if($team_on[$idnum] == $winning_team)
    {
      if(count($prizes) > 0)
      {
        $user_prizes[$idnum][] = $prizes[$winning_prize_index];
        $winning_prize_index++;
      }

      $user_places[$idnum] = 1;
    }
    // prize for a member of the losing team
    else
    {
      if(count($prizes) > count($pets) / 2)
      {
        $user_prizes[$idnum][] = $prizes[$losing_prize_index];
        $losing_prize_index++;
      }

      $user_places[$idnum] = 0;
    }

    train_pet($pets[$idnum], 'painting', $exp_mult);
    train_pet($pets[$idnum], 'dex', ceil($exp_mult / 3));
    train_pet($pets[$idnum], 'wit', ceil($exp_mult / 3));

    save_pet($pets[$idnum], array('love', 'safety', 'esteem', 'painting', 'dex', 'wit', 'painting_count', 'dex_count', 'wit_count'));
  }

  return array($user_places, $user_prizes, $event_report);
}

/*
// returns ($places, $prizes, $report)
function DoVolleyball($event, $pets, $users)
{
  $prizes = take_apart(',', $event['prizes']);

  $user_prizes = array();
  $user_places = array();
  $event_report = '';

  $teams = array(array(), array());
  $names = array(array(), array());
  
  $team_colors = array('{red}', '{blue}');
  $team_names = array('Red', 'Blue');

  $team_score = array(0, 0);
  $team_server = array(0, 0);

  $game_time = 540;                // 540s == 9m
  $game_serving_team = rand(0, 1);
  $game_momentum = 0;              // ball gets increasingly hard to hit until a fault is made

  shuffle($pets);

  $cur_team = 0;
  foreach($pets as $idnum=>$pet)
  {
    $teams[$cur_team][] = $pet;
    $names[$cur_team][] = $pet['petname'];

    if($cur_team == 0)
      $cur_team = 1;
    else
      $cur_team = 0;
  }

  $event_report = '<p>{blue}Blue team: ' . implode(', ', $names[0]) . "{/}<br />\n" .
                  '{red}Red team: ' . implode(', ', $names[1]) . "{/}</p>\n";

  while(1)
  {
    $server = $teams[$team_server[$game_serving_team]];

    $event_report = '<p>' . $team_colors[$game_serving_team] . $names[$team_server[$game_serving_team]] . "{/} serves...</p>\n";

    $ballstrength = successes($teams[$team_server[$game_serving_team]]['
  }
}
*/
// returns ($places, $prizes, $report)
function DoCtF($event, $pets, $users)
{
  $avg_level = floor(($event['minlevel'] + $event['maxlevel']) / 2);
  $exp_mult = ceil($avg_level / 6);

  $prizes = take_apart(',', $event['prizes']);

  $user_prizes = array();
  $user_places = array();
  $event_report = '';

  $teams[0] = array();
  $teams[1] = array();

  $team_flag = array(0, 0);
  $team_danger = array(0, 0);
  $team_color = array('<span style="color:red;">', '<span style="color:blue;">');
  $team_color_word = array('Red', 'Blue');

  $flag_capture = array();
  $stealth = array();
  $perception = array();
  $action = array();
  $captured = array();
  $team_on = array();
  $team_against = array();
  $last_chase = array();

  // split up the pets into two teams
  $team = 0;
  foreach($pets as $idnum=>$pet)
  {
    $flag_capture[$idnum] = 0;
    $stealth[$idnum] = 0;
    $perception[$idnum] = 0;
    $captured[$idnum] = 0;
    $last_chase[$idnum] = 0;

    gain_love($pets[$idnum], success_roll(6, 10, 6));

    $team_on[$idnum] = $team;

    if($team == 0)
      $team_against[$idnum] = 1;
    else
      $team_against[$idnum] = 0;

    $a = rand() % 4;
    if($a == 0 || $a == 1)
      $action[$idnum] = 'stealth';
    else if($a == 2)
      $action[$idnum] = 'run';
    else if($a == 3)
      $action[$idnum] = 'defend';

    $team_flag[$team] += success_roll($pet['int'] + $pet['stealth'], 10, 6);

    $teams[$team][] = $idnum;
    if($team == 0)
      $team = 1;
    else
      $team = 0;
  }

  $event_report = "<p>$team_color[0]$team_color_word[0] team: ";

  $first = true;
  foreach($teams[0] as $member)
  {
    if($first == true)
      $first = false;
    else
      $event_report .= ', ';
    $event_report .= $pets[$member]['petname'];
  }

  $event_report .= "</span><br />\n$team_color[1]$team_color_word[1] team: ";

  $first = true;
  foreach($teams[1] as $member)
  {
    if($first == true)
      $first = false;
    else
      $event_report .= ', ';
    $event_report .= $pets[$member]['petname'];
  }

  $event_report .= "</span></p>\n<p>\n";

  while(1)
  {
    foreach($pets as $idnum=>$pet)
    {
      // if we're captured
      if($captured[$idnum] > 0)
      {
        // become more un-captured (less captured...)
        $captured[$idnum]--;
        if($captured[$idnum] == 0)
        {
          // pick a new job if we're free
          $a = rand() % 4;
          if($a == 0 || $a == 1)
            $action[$idnum] = 'stealth';
          else if($a == 2)
            $action[$idnum] = 'run';
          else if($a == 3)
            $action[$idnum] = 'defend';

          $perception[$idnum] = 0;
          $stealth[$idnum] = 0;
        }
      }
      else
        // gain safety while you're not captured
        gain_safety($pets[$idnum], success_roll(1, 10, 6), true);

    } // for each pet

    foreach($pets as $idnum=>$pet)
    {
      $bonus = ($pet['merit_acute_senses'] == 'yes' ? 1 : 0);
    
      if($captured[$idnum] > 0)
      {
        // do nothing
      }
      // if we've got the flag... RUN
      else if($flag_capture[$idnum] > 0)
      {
        $flag_capture[$idnum]--;
        $stealth[$idnum] = success_roll($pet['dex'] + $pet['stealth'], 10, 8);
        gain_esteem($pets[$idnum], success_roll(2, 10, 6));

        // did we get it?  did we get the flag back?
        if($flag_capture[$idnum] == 0)
        {
          $event_report .= '<strong>' . $team_color[$team_on[$idnum]] . $pet['petname'] . "</span> brings the flag back, winning the game!</strong><br />\n";
          gain_esteem($pets[$idnum], success_roll(8, 10, 6));
          set_pet_badge($pets[$idnum], 'ctf');

          $losing_prize_index = count($pets) / 2;
          $winning_prize_index = 0;

          // hand out prizes
          foreach($pets as $otheruser=>$otherpet)
          {
            // prize for a member of the winning team
            if($team_on[$otheruser] == $team_on[$idnum])
            {
              if(count($prizes) > 0)
              {
                $user_prizes[$otheruser][] = $prizes[$winning_prize_index];
                $winning_prize_index++;
              }

              $user_places[$otheruser] = 1;
            }
            // prize for a member of the losing team
            else
            {
              if(count($prizes) > count($pets) / 2)
              {
                $user_prizes[$otheruser][] = $prizes[$losing_prize_index];
                $losing_prize_index++;
              }

              $user_places[$otheruser] = 0;
            }

            train_pet($pets[$idnum], 'stealth', ceil($exp_mult / 2));
            train_pet($pets[$idnum], 'int', ceil($exp_mult / 5));
            train_pet($pets[$idnum], 'per', ceil($exp_mult / 3));
            train_pet($pets[$idnum], 'str', ceil($exp_mult / 4));
            train_pet($pets[$idnum], 'sta', ceil($exp_mult / 3));
            train_pet($pets[$idnum], 'bra', ceil($exp_mult / 4));
            train_pet($pets[$idnum], 'athletics', ceil($exp_mult / 3));

            save_pet($pets[$otheruser], array('love', 'safety', 'esteem', 'stealth', 'int', 'per', 'str', 'sta', 'bra', 'athletics', 'stealth_count', 'int_count', 'per_count', 'str_count', 'sta_count', 'bra_count', 'athletics_count'));
          }

          $event_report .= "</p>\n<p>" . $team_color[$team_on[$idnum]] . $team_color_word[$team_on[$idnum]] . " team</span> wins the game.</p>\n";

          return array($user_places, $user_prizes, $event_report);
        }
      }
      // if we're stealthing...
      else if($action[$idnum] == 'stealth')
      {
        $stealth[$idnum] = success_roll($pet['athletics'] + $pet['stealth'], 10, 6);

        $perception[$idnum] += success_roll($pet['per'] + $pet['stealth'] + $bonus, 10, 8);
        $team_flag[$team_against[$idnum]] -= .33;

        // if we found their flag
        if($perception[$idnum] >= $team_flag[$team_against[$idnum]] && $team_danger[$team_against[$idnum]] == 0)
        {
          // make it easier to find next time
          $team_flag[$team_against[$idnum]] -= success_roll($pet['int'], 10, 4);
          $team_danger[$team_against[$idnum]] = 1;

          $action[$idnum] = 'run';
          $flag_capture[$idnum] = 2;

          $event_report .= '<strong>' . $team_color[$team_on[$idnum]] . $pet['petname'] . '</span> got the ' . $team_color[$team_against[$idnum]] . $team_color_word[$team_against[$idnum]] . " flag</span>!</strong><br />\n";
        }
      }
      // if we're running
      else if($action[$idnum] == 'run')
      {
        $stealth[$idnum] = success_roll($pet['athletics'] + $pet['stealth'], 10, 8);

        $perception[$idnum] += success_roll($pet['per'] + $pet['stealth'] + $bonus, 10, 6);
        $team_flag[$team_against[$idnum]] -= .5;

        // if we found their flag
        if($perception[$idnum] >= $team_flag[$team_against[$idnum]] && $team_danger[$team_against[$idnum]] == 0)
        {
          // make it easier to find next time
          $team_flag[$team_against[$idnum]] -= success_roll($pet['int'], 10, 4);
          $team_danger[$team_against[$idnum]] = 1;

          $action[$idnum] = 'run';
          $flag_capture[$idnum] = 2;

          $event_report .= '<strong>' . $team_color[$team_on[$idnum]] . $pet['petname'] . '</span> got the ' . $team_color[$team_against[$idnum]] . $team_color_word[$team_against[$idnum]] . " flag</span>!</strong><br />\n";
        }
      }
    } // for each pet

    foreach($pets as $idnum=>$pet)
    {
      $bonus = ($pet['merit_acute_senses'] == 'yes' ? 1 : 0);
    
      if($captured[$idnum] > 0)
      {
        // do nothing
      }
      else if($action[$idnum] == 'defend')
      {
        $perception[$idnum] = success_roll($pet['per'] + $pet['stealth'] + $bonus, 10, 6);
        $last_chase[$idnum]++;
        
        $did_something = false;

        $enemy_members = $teams[$team_against[$idnum]];
        shuffle($enemy_members);

        // watch out for opponents
        foreach($enemy_members as $member)
        {
          // well, for offensive opponents
          if(($action[$member] == 'stealth' || $action[$member] == 'run') && $captured[$member] == 0
            && ($team_danger[$team_on[$idnum]] == 0 || $flag_capture[$member] > 0))
          {
            // did we see them?
            if($perception[$idnum] > $stealth[$member])
            {
              if($flag_capture[$member] > 0)
                $event_report .= '<strong>';
            
              $event_report .= $team_color[$team_on[$idnum]] . $pets[$idnum]['petname'] . '</span> found ' . $team_color[$team_on[$member]] . $pets[$member]['petname'] . '</span>';
              $last_chase[$idnum] = 0;

              // chase them!
              if(success_roll($pet['str'] + $pet['athletics'], 10, 6) > success_roll($pets[$member]['str'] + $pets[$member]['athletics'], 10, 6))
              {
                // we got 'im!
                $captured[$member] = 1;
                gain_esteem($pets[$idnum], success_roll(2, 10, 6));

                $event_report .= ' and captured ' . t_pronoun($pets[$member]['gender']);
                
                // if they had the flag
                if($flag_capture[$member] > 0)
                {
                  // they don't any more :)
                  $team_danger[$team_on[$idnum]] = 0;
                  $flag_capture[$member] = 0;

                  $event_report .= ', returning the flag';
                }

                $event_report .= "!<br />\n";
              }
              else
              {
                // we didn't catch them, but they're running now :)
                $action[$member] = 'run';
                gain_esteem($pets[$member], success_roll(1, 10, 6));

                $event_report .= ' but ' . $team_color[$team_on[$member]] . $pets[$member]['petname'] . '</span> got away!<br />';
              }

              if($flag_capture[$member] > 0)
                $event_report .= '</strong>';

              $did_something = true;
            } // if we noticed the enemy pet
          } // if the enemy pet is a threat

          if($did_something == true) break;
        } // for each enemy
        
        // if we haven't chased anyone...
        if($last_chase[$idnum] == 2)
        {
          // ... start searching out the flag
          $perception[$idnum] = 0;
          $steath[$idnum] = 0;

          $a = rand() % 2;
          if($a == 0)
            $action[$idnum] = 'run';
          else if($a == 1)
            $action[$idnum] = 'stealth';
        }
      } // if we're defending
    } // for each pet
  } // forever
}

// returns ($places, $prizes, $report)
function DoLongJump($event, $pets, $users)
{
  $avg_level = floor(($event['minlevel'] + $event['maxlevel']) / 2);
  $exp_mult = ceil($avg_level / 4);

  if(strlen($event['prizes']) > 0)
    $prizes = explode(',', $event['prizes']);
  else
    $prizes = array();

  $user_prizes = array();
  $user_places = array();
  $event_report = '';

  $successes = array();

  foreach($pets as $idnum=>$pet)
  {
    $successes[$idnum] = ($pet['athletics'] * 105 + $pet['str'] * 85 + mt_rand(1, 100)) / 200;
  }

  arsort($successes);

  $place = 0;

  $event_report = "<ul>\n";

  foreach($successes as $idnum=>$score)
  {
    $place++;

    $user_places[$idnum] = $place;

    $event_report .= "<li><a href=\"/petprofile.php?petid=$idnum\">" . $pets[$idnum]['petname'] . '</a> leaped ' . round($score, 2) . ' meters, placing ' . numeric_place($place) . "</li>\n";

    if($place <= count($prizes))
    {
      $user_prizes[$idnum][] = $prizes[$place - 1];
    }

    gain_love($pets[$idnum], success_roll(8, 10, 6));
    gain_esteem($pets[$idnum], success_roll(10, 10, 6));

    train_pet($pets[$idnum], 'athletics', $exp_mult );
    train_pet($pets[$idnum], 'str', ceil($exp_mult / 2));
    train_pet($pets[$idnum], 'dex', ceil($exp_mult / 4));

    save_pet($pets[$idnum], array('love', 'safety', 'esteem', 'athletics', 'str', 'athletics_count', 'str_count'));
  }

  $event_report .= "</ul>\n";

  return array($user_places, $user_prizes, $event_report);
}

// returns ($places, $prizes, $report)
function DoFishing($event, $pets, $users)
{
  $avg_level = floor(($event['minlevel'] + $event['maxlevel']) / 2);
  $exp_mult = ceil($avg_level / 4);

  if(strlen($event['prizes']) > 0)
    $prizes = explode(',', $event['prizes']);
  else
    $prizes = array();

  $user_prizes = array();
  $user_places = array();
  $event_report = '';

  $successes = array();

  foreach($pets as $idnum=>$pet)
  {
    $successes[$idnum] = ($pet['fishing'] * 40 + $pet['dex'] * 20 + $pet['stealth'] + mt_rand(0, 200)) / 10 + 1;
  }

  arsort($successes);

  $place = 0;

  $event_report = "<ul>\n";

  foreach($successes as $idnum=>$score)
  {
    $place++;

    $user_places[$idnum] = $place;

    $event_report .= "<li><a href=\"/petprofile.php?petid=$idnum\">" . $pets[$idnum]['petname'] . '</a> caught a ' . round($score, 1) . 'cm fish, placing ' . numeric_place($place) . "</li>\n";

    if($place <= count($prizes))
    {
      $user_prizes[$idnum][] = $prizes[$place - 1];
    }

    gain_love($pets[$idnum], success_roll(8, 10, 6));
    gain_esteem($pets[$idnum], success_roll(10, 10, 6));

    train_pet($pets[$idnum], 'fishing', $exp_mult );
    train_pet($pets[$idnum], 'dex', ceil($exp_mult / 2));
    train_pet($pets[$idnum], 'stealth', ceil($exp_mult / 4));

    save_pet($pets[$idnum], array('love', 'safety', 'esteem', 'fishing', 'dex', 'stealth', 'fishing_count', 'dex_count', 'stealth_count'));
  }

  $event_report .= "</ul>\n";

  return array($user_places, $user_prizes, $event_report);
}

// returns ($places, $prizes, $report)
function DoFashion($event, $pets, $users)
{
  $avg_level = floor(($event['minlevel'] + $event['maxlevel']) / 2);
  $exp_mult = ceil($avg_level / 4);

  if(strlen($event['prizes']) > 0)
    $prizes = explode(',', $event['prizes']);
  else
    $prizes = array();

  $user_prizes = array();
  $user_places = array();
  $event_report = '';

  $successes = array();

  $max_success = 0;
  
  foreach($pets as $idnum=>$pet)
  {
    $successes[$idnum] = ($pet['tai'] * 100 + $pet['jeweling'] * 80 + $pet['open'] * 70 + $pet['perception'] * 60 + $pet['wits'] * 20 + $pet['dex'] * 20 + ($pet['merit_steady_hands'] == 'yes' ? 40 : 0) + mt_rand(1, 200));
    $max_success = max($max_success, $successes[$idnum]);
  }

  arsort($successes);

  $adjectives = array('stunning', 'dazzling', 'haute couture', 'sleek', 'modern', 'retro');
  
  $place = 0;

  $event_report = '<ul>';

  foreach($successes as $idnum=>$score)
  {
    $place++;

    $user_places[$idnum] = $place;

    $event_report .= '<li><a href="/petprofile.php?petid=' . $idnum . '">' . $pets[$idnum]['petname'] . '</a> takes ' . numeric_place($place);

    if($place <= 3)
    {
      $i = array_rand($adjectives);
      $adjective = $adjectives[$i];
      unset($adjectives[$i]);
      $event_report .= ' for ' . p_pronoun($pets[$idnum]['gender']) . ' "' . $adjective . '" designs';
    }

    $event_report .= '</li>' . "\n";

    if($place <= count($prizes))
    {
      $user_prizes[$idnum][] = $prizes[$place - 1];
    }

    gain_love($pets[$idnum], success_roll(8, 10, 6));
    gain_esteem($pets[$idnum], success_roll(10, 10, 6));

    train_pet($pets[$idnum], 'tai', $exp_mult);
    train_pet($pets[$idnum], 'jeweling', ceil($exp_mult / 2));
    train_pet($pets[$idnum], 'per', ceil($exp_mult / 2));

    save_pet($pets[$idnum], array('love', 'safety', 'esteem', 'tai', 'jeweling', 'per', 'tai_count', 'jeweling_count', 'per_count'));
  }

  $event_report .= '</ul>';

  return array($user_places, $user_prizes, $event_report);
}

// returns ($places, $prizes, $report)
function DoStrategyGame($event, $pets, $users)
{
  $avg_level = floor(($event['minlevel'] + $event['maxlevel']) / 2);
  $exp_mult = ceil($avg_level / 4);

  if(strlen($event['prizes']) > 0)
    $prizes = explode(',', $event['prizes']);
  else
    $prizes = array();

  $user_prizes = array();
  $user_places = array();
  $event_report = '';

  $successes = array();

  foreach($pets as $idnum=>$pet)
  {
    $bonus = ($pet['merit_lightning_calculator'] == 'yes' ? 1 : 0);
    $successes[$idnum] = success_roll($pet['int'] + $pet['wit'] + $pet['open'], 10, 5) + $bonus;
  }

  arsort($successes);

  $place = 0;

  $event_report = "<ul>\n";

  foreach($successes as $idnum=>$score)
  {
    $place++;

    $user_places[$idnum] = $place;

    $event_report .= "<li><a href=\"/petprofile.php?petid=$idnum\">" . $pets[$idnum]['petname'] . '</a> placed ' . numeric_place($place) . "</li>\n";

    if($place <= count($prizes))
    {
      $user_prizes[$idnum][] = $prizes[$place - 1];
    }

    gain_love($pets[$idnum], success_roll(8, 10, 6));
    gain_esteem($pets[$idnum], success_roll(10, 10, 6));

    train_pet($pets[$idnum], 'int', $exp_mult);
    train_pet($pets[$idnum], 'wit', $exp_mult);

    save_pet($pets[$idnum], array('love', 'safety', 'esteem', 'int', 'wit', 'int_count', 'wit_count'));
  }

  $event_report .= "</ul>\n";
  
  return array($user_places, $user_prizes, $event_report);
}

function DoDigcraft($event, $pets, $users)
{
  $avg_level = floor(($event['minlevel'] + $event['maxlevel']) / 2);
  $exp_mult = ceil($avg_level / 4);

  if(strlen($event['prizes']) > 0)
    $prizes = explode(',', $event['prizes']);
  else
    $prizes = array();

  $user_prizes = array();
  $user_places = array();
  $event_report = '';

  $successes = array();

  foreach($pets as $idnum=>$pet)
  {
    $successes[$idnum] = success_roll($pet['mining'] * 2 + $pet['sculpting'] + $pet['cra'] + $pet['open'], 10, 6);
  }

  arsort($successes);

  $place = 0;

  $event_report = "<ul>\n";

  foreach($successes as $idnum=>$score)
  {
    $place++;

    $user_places[$idnum] = $place;

    $event_report .= "<li><a href=\"/petprofile.php?petid=$idnum\">" . $pets[$idnum]['petname'] . '</a> placed ' . numeric_place($place) . "</li>\n";

    if($place <= count($prizes))
    {
      $user_prizes[$idnum][] = $prizes[$place - 1];
    }

    gain_love($pets[$idnum], success_roll(8, 10, 6));
    gain_esteem($pets[$idnum], success_roll(10, 10, 6));

    train_pet($pets[$idnum], 'mining', $exp_mult);
    train_pet($pets[$idnum], 'str', floor($exp_mult / 2));
    train_pet($pets[$idnum], 'sta', floor($exp_mult / 2));
    train_pet($pets[$idnum], 'sculpting', ceil($exp_mult / 3));
    train_pet($pets[$idnum], 'cra', ceil($exp_mult / 4));

    save_pet($pets[$idnum], array('love', 'safety', 'esteem', 'mining', 'str', 'sta', 'sculpting', 'cra', 'mining_count', 'str_count', 'sta_count', 'sculpting_count', 'cra_count'));
  }

  $event_report .= "</ul>\n";
  
  return array($user_places, $user_prizes, $event_report);
}

function DoGenericCrafts($event, $pets, $users)
{
  $avg_level = floor(($event['minlevel'] + $event['maxlevel']) / 2);
  $exp_mult = ceil($avg_level / 4);

  if(strlen($event['prizes']) > 0)
    $prizes = explode(',', $event['prizes']);
  else
    $prizes = array();

  $user_prizes = array();
  $user_places = array();
  $event_report = '';

  $successes = array();

  foreach($pets as $idnum=>$pet)
  {
    $bonus = ($pet['merit_steady_hands'] == 'yes' ? 1 : 0);
    $successes[$idnum] = success_roll(($pet['dex'] + $pet['wit'] + $pet['cra']) * 2 + ceil($pet['open'] / 2), 10, 6) + $bonus;
  }

  arsort($successes);

  $place = 0;

  $event_report = "<ul>\n";

  foreach($successes as $idnum=>$score)
  {
    $place++;

    $user_places[$idnum] = $place;

    $event_report .= "<li><a href=\"/petprofile.php?petid=$idnum\">" . $pets[$idnum]['petname'] . '</a> placed ' . numeric_place($place) . "</li>\n";

    if($place <= count($prizes))
    {
      $user_prizes[$idnum][] = $prizes[$place - 1];
    }

    gain_love($pets[$idnum], success_roll(8, 10, 6));
    gain_esteem($pets[$idnum], success_roll(10, 10, 6));

    train_pet($pets[$idnum], 'cra', ceil($exp_mult * 0.75));
    train_pet($pets[$idnum], 'wit', ceil($exp_mult * 0.75));
    train_pet($pets[$idnum], 'dex', ceil($exp_mult * 0.75));

    save_pet($pets[$idnum], array('love', 'safety', 'esteem', 'cra', 'wit', 'dex', 'cra_count', 'wit_count', 'dex_count'));
  }

  $event_report .= "</ul>\n";
  
  return array($user_places, $user_prizes, $event_report);
}

function DoHunt($event, $pets, $users)
{
  $avg_level = floor(($event['minlevel'] + $event['maxlevel']) / 2);
  $exp_mult = ceil($avg_level / 4);

  if(strlen($event['prizes']) > 0)
    $prizes = explode(',', $event['prizes']);
  else
    $prizes = array();

  $score = array();
  $player_result = array();

  $user_prizes = array();
  $user_places = array();
  $event_report = '';

  foreach($prizes as $prize)
  {
    foreach($pets as $idnum=>$pet)
    {
      $bonus = ($pet['merit_acute_senses'] == 'yes' ? 1 : 0);
      $this_score = success_roll($pet['per'] + $pet['gathering'], 10, 5) + $bonus;
      $score[$idnum] += $this_score;
      gain_love($pets[$idnum], floor($this_score / 2));
    }

    arsort($score);

    foreach($score as $idnum=>$this_score)
    {
      gain_esteem($pets[$idnum], floor($this_score / 2));
      gain_safety($pets[$idnum], floor($this_score / 3), true);

      $user_prizes[$idnum][] = $prize;
      $score[$idnum] = 0;

      break;
    }

    train_pet($pets[$idnum], 'per', $exp_mult);
    train_pet($pets[$idnum], 'gathering', $exp_mult);

    save_pet($pets[$idnum], array('love', 'safety', 'esteem', 'per', 'gathering', 'per_count', 'gathering_count'));
  }

  foreach($user_prizes as $idnum=>$user_prize)
  {
    foreach($user_prize as $this_prize)
    {
      $command = "SELECT itemname FROM monster_inventory WHERE idnum=$this_prize LIMIT 1";
      $prize_item = fetch_single($command, 'fetching prize');

      $this_pet = $pets[$idnum];
      $event_report .= "<li><a href=\"/petprofile.php?petid=$idnum\">" . $pets[$idnum]['petname'] . '</a> found ' . $prize_item['itemname'] . "</li>\n";
    }
  }

  $event_report .= "</ul>\n";

  return array($user_places, $user_prizes, $event_report);
}

function DoRace($event, $pets, $users)
{
  $avg_level = floor(($event['minlevel'] + $event['maxlevel']) / 2);
  $exp_mult = ceil($avg_level / 4);

  $user_prizes = array();
  $user_places = array();
  $event_report = '';

  $distance = array();

  foreach($pets as $idnum=>$this_pet)
  {
    $distance[$idnum] = 0;
  }

  for($i = 0; $i < 3; ++$i)
  {
    foreach($pets as $idnum=>$this_pet)
    {
      $distance[$idnum] += success_roll($this_pet['sta'] + $this_pet['athletics'] + ceil($this_pet['str'] / 2), 10, 6);
    }
  }

  arsort($distance);
  $place = 0;

  $event_report = '<p>The final placements are as follows:</p>' .
                  '<ul>';

  if(strlen($event['prizes']) > 0)
    $prizes = explode(',', $event['prizes']);
  else
    // array of count() == 0
    $prizes = array();

  foreach($distance as $idnum=>$dist)
  {
//    echo '<p>' . $this_pet['petname'] . ": $dist</p>\n";

    ++$place;

    $user_places[$idnum] = $place;

    $event_report .= '<li><a href="/petprofile.php?petid=' . $idnum . '">' . $pets[$idnum]['petname'] . '</a> placed ' . numeric_place($place) . "</li>\n";

    if($place <= count($prizes))
    {
      $user_prizes[$idnum][] = $prizes[$place - 1];
    }

    if($place == 1)
      set_pet_badge($pets[$idnum], 'runner');

    gain_love($pets[$idnum], success_roll(8, 10, 6));
    gain_safety($pets[$idnum], success_roll(4, 10, 6), true);
    gain_esteem($pets[$idnum], success_roll(8, 10, 6));

    train_pet($pets[$idnum], 'sta', $exp_mult);
    train_pet($pets[$idnum], 'athletics', $exp_mult);
    train_pet($pets[$idnum], 'str', ceil($exp_mult / 3));

    save_pet($pets[$idnum], array('love', 'safety', 'esteem', 'sta', 'str', 'athletics', 'sta_count', 'str_count', 'athletics_count'));
  }

  $event_report .= '</ul>';

  return array($user_places, $user_prizes, $event_report);
}

function DoArchery($event, $pets, $users)
{
  $avg_level = floor(($event['minlevel'] + $event['maxlevel']) / 2);
  $exp_mult = ceil($avg_level / 4);

  $user_prizes = array();
  $user_places = array();
  $event_report = '';

  $distance = array();

  foreach($pets as $idnum=>$this_pet)
  {
    $distance[$idnum] = 0;
  }

  for($i = 0; $i < 3; ++$i)
  {
    foreach($pets as $idnum=>$this_pet)
    {
      $distance[$idnum] += success_roll($this_pet['dex'] + $this_pet['athletics'], 10, 6) +
        ($this_pet['merit_steady_hands'] == 'yes' ? 1 : 0);
    }
  }

  arsort($distance);
  $place = 0;

  $event_report = "<p>The final placements are as follows:<br /></p>\n" .
                  "<ul>\n";

  if(strlen($event['prizes']) > 0)
    $prizes = explode(',', $event['prizes']);
  else
    // array of count() == 0
    $prizes = array();

  foreach($distance as $idnum=>$dist)
  {
//    echo "<p>" . $this_pet["petname"] . ": $dist</p>\n";

    ++$place;

    $user_places[$idnum] = $place;

    $event_report .= "<li><a href=\"/petprofile.php?petid=$idnum\">" . $pets[$idnum]['petname'] . '</a> placed ' . numeric_place($place) . "</li>\n";

    if($place <= count($prizes))
    {
      $user_prizes[$idnum][] = $prizes[$place - 1];
    }

    if($place == 1)
      set_pet_badge($pets[$idnum], 'archer');

    gain_love($pets[$idnum], success_roll(8, 10, 6));
    gain_safety($pets[$idnum], success_roll(4, 10, 6), true);
    gain_esteem($pets[$idnum], success_roll(8, 10, 6));

    train_pet($pets[$idnum], 'dex', $exp_mult);
    train_pet($pets[$idnum], 'athletics', $exp_mult);

    save_pet($pets[$idnum], array('love', 'safety', 'esteem', 'dex', 'athletics', 'dex_count', 'athletics_count'));
  }

  $event_report .= "</ul>\n";

  return array($user_places, $user_prizes, $event_report);
}

function DoBrawl($event, $pets, $users)
{
  $avg_level = floor(($event['minlevel'] + $event['maxlevel']) / 2);
  $exp_mult = ceil($avg_level / 4);

  if(strlen($event['prizes']) > 0)
    $prizes = explode(',', $event['prizes']);
  else
    $prizes = array();

  $user_prizes = array();
  $user_places = array();
  $event_report = '';

  $initiative = array();
  $hp = array();
  $names = array();

  $index = 0;

  foreach($pets as $idnum=>$pet)
  {
    $initiative[$idnum] = success_roll($pet['athletics'] + $pet['wit'], 10, 6) + (rand() % 10 + 1);
    $hp[$idnum] = 6;
    $names[$index] = $idnum;
    ++$index;
  }

  arsort($initiative);

  $place = count($pets);

  $last_survivor = '';

  do
  {
    foreach($initiative as $idnum=>$init)
    {
      if($hp[$idnum] > 0 && $place > 1)
      {
        $i = -1;
        // pick another, random pet
        do
        {
          $i = rand() % count($names);
        } while($names[$i] == $idnum || $hp[$names[$i]] < 1);

        // attack it!
        $attack = success_roll($pets[$idnum]['athletics'] + $pets[$idnum]['bra'] * 2, 10, 6);
        // it tries to dodge!
        $dodge = success_roll($pets[$names[$i]]['athletics'] * 2 + $pets[$names[$i]]['bra'], 10, 6);

        $attack -= $dodge;

        if($attack > 0)
        {
          // roll for damage
          $damage = success_roll($pets[$idnum]['str'] + ceil($attack * 0.75), 10, 6);
          // roll for soak
          $soak = success_roll($pets[$names[$i]]['sta'], 10, 6) + ($pets[$names[$i]]['merit_tough_hide'] == 'yes' ? 1 : 0);

          $damage -= $soak;

          gain_esteem($pets[$idnum], success_roll($damage, 10, 6));

          if($damage > 0)
          {
            gain_safety($pets[$idnum], success_roll($damage + $soak, 10, 6), false);

            if($damage >= $hp[$names[$i]])
            {
              $last_survivor = $idnum;

              $hp[$names[$i]] = 0;

              $user_places[$names[$i]] = $place;

              $place--;
            }
            else
              $hp[$names[$i]] -= $damage;
          }
          else
            gain_safety($pets[$names[$i]], success_roll($soak + $damage, 10, 6), false);
        }
      }
    }
  } while($place > 1);

  $user_places[$last_survivor] = $place;

  $event_report .= "<ul>\n";

  asort($user_places);
  
  foreach($user_places as $idnum=>$place)
  {
    $event_report .= "<li><a href=\"/petprofile.php?petid=$idnum\">" . $pets[$idnum]['petname'] . '</a> got ' . numeric_place($place) . " place</li>\n";

    if($place <= count($prizes))
    {
      $user_prizes[$idnum][] = $prizes[$place - 1];
    }

    train_pet($pets[$idnum], 'bra', ceil($exp_mult / 2));
    train_pet($pets[$idnum], 'str', ceil($exp_mult / 3));
    train_pet($pets[$idnum], 'sta', ceil($exp_mult / 3));
    train_pet($pets[$idnum], 'athletics', ceil($exp_mult / 4));

    save_pet($pets[$idnum], array('love', 'safety', 'esteem', 'bra', 'str', 'sta', 'athletics', 'bra_count', 'str_count', 'sta_count', 'athletics_count'));
  }

  $event_report .= "</ul>\n";

  return array($user_places, $user_prizes, $event_report);
}

function DoRoborena($event, $pets, $users)
{
  $avg_level = floor(($event['minlevel'] + $event['maxlevel']) / 2);
  $exp_mult = ceil($avg_level / 4);

  if(strlen($event['prizes']) > 0)
    $prizes = explode(',', $event['prizes']);
  else
    $prizes = array();

  $user_prizes = array();
  $user_places = array();
  $event_report = '';

  $initiative = array();
  $hp = array();
  $names = array();

  $index = 0;

  foreach($pets as $idnum=>$pet)
  {
    $initiative[$idnum] = success_roll(max(1, $pet['wit'] + $pet['eng'] + $pet['mechanics'] - $pet['smi']), 10, 6) + (rand() % 10 + 1);
    $hp[$idnum] = success_roll($pet['mechanics'] + $pet['smi'], 10, 5) + 1;
    $names[$index] = $idnum;
    ++$index;
  }

  arsort($initiative);

  $place = count($pets);

  $last_survivor = '';

  $s = 0;
  $m = 0;

  $s += mt_rand(4, 9);

  do
  {
    $s += mt_rand(1, 27);
    if($s >= 60)
    {
      $m++;
      $s -= 60;
    }

    foreach($initiative as $idnum=>$init)
    {
      if($hp[$idnum] > 0 && $place > 1)
      {
        $s += mt_rand(0, 3);
        if($s >= 60)
        {
          $m++;
          $s -= 60;
        }

        $i = -1;
        // pick another, random pet
        do
        {
          $i = rand() % count($names);
        } while($names[$i] == $idnum || $hp[$names[$i]] < 1);

        // attack it!
        $attack = success_roll($pets[$idnum]['dex'] + $pets[$idnum]['pil'] + $pets[$idnum]['eng'], 10, 4);
        // it tries to dodge!
        $dodge = success_roll($pets[$names[$i]]['dex'] + $pets[$idnum]['pil'] + $pets[$names[$i]]['eng'], 10, 4);

        $attack -= $dodge;

        if($attack > 0)
        {
          gain_esteem($pets[$idnum], 1);

          if($hp[$names[$i]] <= 1)
          {
            $last_survivor = $idnum;

            $hp[$names[$i]] = 0;

            $user_places[$names[$i]] = $place;

            $place--;
            
            $event_report .= '<span class="dim">' . sprintf('%02d:%02d', $m, $s) . '</span> ' . $pets[$names[$i]]['petname'] . '\'s robot is destroyed by ' . $pets[$idnum]['petname'] . '\'s robot!<br />';
          }
          else
            $hp[$names[$i]]--;
        }
      }
    }
  } while($place > 1);

  $user_places[$last_survivor] = $place;

  $event_report .= "<br /><ul>\n";

  asort($user_places);

  foreach($user_places as $idnum=>$place)
  {
    $event_report .= "<li><a href=\"/petprofile.php?petid=$idnum\">" . $pets[$idnum]['petname'] . '</a> got ' . numeric_place($place) . " place</li>\n";

    if($place <= count($prizes))
    {
      $user_prizes[$idnum][] = $prizes[$place - 1];
    }

    if($place == 1)
      set_pet_badge($pets[$idnum], 'roborenaist');

    train_pet($pets[$idnum], 'wit', ceil($exp_mult / 4));
    train_pet($pets[$idnum], 'eng', ceil($exp_mult / 2));
    train_pet($pets[$idnum], 'mechanics', ceil($exp_mult / 2));
    train_pet($pets[$idnum], 'smi', ceil($exp_mult / 3));
    train_pet($pets[$idnum], 'dex', ceil($exp_mult / 4));
    train_pet($pets[$idnum], 'pil', ceil($exp_mult / 3));

    save_pet($pets[$idnum], array('love', 'safety', 'esteem', 'wit', 'eng', 'mechanics', 'smi', 'dex', 'pil', 'wit_count', 'eng_count', 'mechanics_count', 'smi_count', 'dex_count', 'pil_count'));
  }

  $event_report .= "</ul>\n";

  return array($user_places, $user_prizes, $event_report);
}

function DoCookOff($event, $pets, $users)
{
  $avg_level = floor(($event['minlevel'] + $event['maxlevel']) / 2);
  $exp_mult = ceil($avg_level / 4);

  if(strlen($event['prizes']) > 0)
    $prizes = explode(',', $event['prizes']);
  else
    $prizes = array();

  $INGREDIENTS = array(
    'Redsberries' => 'Now, Redsberries are very similiar to Blueberries, and in fact it takes a refined palate to tell the difference from taste alone.  Interestingly, Redsberries are slightly less common in the wild than Blueberries, though no one is exactly certain why.',
    'Steak' => 'Steak is of course a relatively common and well-understood meat, so I\'m very curious to see what our contestants will do with it.  They\'re going to have to creating something... <em>new</em>, and <em>exciting</em> if they\'re going to catch the attetion of our judges here today.',
    'Blue Egg' => 'Blue Eggs are <em>very</em> rare - very hard to find - and are also said to be one of the most delicious eggs in the world.  I will be very interested to see what five different things our contestants will come up with, given an ingredient they have probably had less experience working with.',
    'Chicken' => '<i>chicken factoids</i>',
    'Fish' => '<i>fish factoids</i>',
    'Firespice' => 'Firespice is unique to the Hollow Earth, but is very similar to Earth horseradish.  Still, it has some very unique qualities, and I\'m sure the contestants will be using those to their every advantage.',
    'Onion' => '<i>onion factoids</i>',
    'Sour Lime' => 'Sour Limes are a Lemon-Lime hybrid that work surprisingly well as substitutes for either lemons or limes, giving our contestants a lot of options.  I personally love Sour Limes, so I can\'t wait to see what they come up with!',
    'Artichoke' => '<i>artichoke factoids</i>',
    'Celery' => 'You may think of Celery as being... <em>watery</em>, and... <em>bland</em>, <em>tasteless</em>.  But the seeds actually contain a lot of flavor, and I think that\'s what we\'re going to see the contestants use a lot of in their dishes here today.',
    'Peanuts' => '<i>peanuts factoids</i>',
    'Potato' => '<i>potato factoids</i>',
    'Cream Cheese' => '<i>cream cheese factoids</i>',
    'Azuki Bean' => '<i>azuki bean factoids</i>',
    'Broccoli' => 'There are of course living, PsyPets broccoli, but don\'t worry!  That\'s not what we have the contestants working with here today.  These are your standard broccolis.  Hopefully we don\'t startle any of the contestants by using it.  Ha, ha!',
    'Ginger' => 'If there\'s ever been a secret ingredient that had a wide range of possibilities, <em>this</em> is it.',
    'Coconut' => '<i>coconut factoids</i>',
    'Corn' => '<i>corn factoids</i>',
    'Eggplant' => '<i>eggplant factoids</i>',
    'Mango' => '<i>mango factoids</i>',
    'Pamplemousse' => '<i>pamplemousse factoids</i>',
    'Pitless Peach' => 'Now, the Pitless Peach of course does not exist, so where our host acquired so many, I can only guess.  At any rate, I\'m sure our contestants will appreciate not having to deal with the pits when working with these today.',
    'Prickly Green' => 'Prickly Greens are, of course, Pears.  Their taste is a little more bitter than a regular Pear, and because of that bitterness I think we\'ll see some very interesting dishes emerge.',
    'Purple Jelly' => 'Purple Jelly is of course a jelly made from <em>Blueberries</em>.  I think usually we\'d expect the Blueberries to themselves be the ingredient... it will be interesting to see how our contestants deal with this <em>unexpected</em> mystery ingredient.',
    'Tomato' => '<i>tomato factoids</i>',
    'Watermelon' => '<i>watermelon factoids</i>',
    'White Radish' => 'White Radish is of course what is used in Daikon Radishes.  It\'s a very common radish with a lot of possibilities, so I\'m very curious to see what our contestants will come up with.',
    'Yam' => '<i>yam factoids</i>',
    'Rice' => '<i>rice factoids</i>',
    'Pineapple' => '<i>pineapple factoids</i>',
    'Pumpkin' => '<i>pumpkin factoids</i>',
    'Orange' => '<i>orange factoids</i>',
  );
  
  $adjectives = array('silver' => 1, 'Siberian' => 1, 'Australian' => 1, 'Mexican' => 1, 'gold' => 1, 'european' => 1, 'concord' => 1, 'Nile' => 1, 'long' => 1, 'Asian' => 1);
  $flavors = array('robust' => 1, 'sweet' => 1, 'spicy' => 1, 'salty' => 1, 'tangy' => 1, 'rich' => 1, 'creamy' => 1, 'fresh' => 1, 'crisp' => 1, 'aromatic' => 1);
  $pots = array('a pot' => 'pots', 'a wok' => 'woks', 'a colander' => 'colanders', 'a blender' => 'blenders', 'a frier' => 'friers', 'a bowl' => 'bowls', 'some molds' => 'molds');
  $opinions = array('good' => 1, 'interesting' => 1, 'unusual' => 1, 'delicious' => 1, 'unique' => 1, 'wonderful' => 1, 'different' => 1);
  $step_dishes = array('dough' => 1, 'batter' => 1, 'sauce' => 1, 'pasta' => 1, 'cracker' => 1, 'soup' => 1);
  $sizes = array('large' => 1, 'small' => 1, 'big' => 1, 'medium-sized' => 1);
  $purees = array('puree' => 1, 'base' => 1, 'emulsion' => 1, 'syrup' => 1);
  $places = array('someone\'s garden' => 1, 'an avocado orchard' => 1, 'an obscure vale' => 1, 'a hidden clutch' => 1, 'a lotus vale' => 1);
  $cooking = array('searing' => 1, 'frying' => 1, 'basting' => 1, 'mixing' => 1, 'kneading' => 1, 'blanching' => 1, 'stewing' => 1, 'chilling' => 1, 'grilling' => 1, 'marinating' => 1, 'deep-frying' => 1, 'simmering' => 1, 'poaching' => 1, 'breading' => 1);
  $states = array('frozen' => 1, 'freeze-dried' => 1, 'distilled' => 1, 'pureed' => 1, 'julienned' => 1);
  $devices = array('A Hungarian squash grater' => 1, 'A spaetzle marker' => 1, 'Moroccan tangines' => 1, 'mamoul molds' => 1);
  $stuffs = array('green... goop - heh - ' => 1, 'orange liquid' => 1, 'whitish mixture' => 1);
  $right = array('right' => 1, 'okay' => 1, 'yeah' => 1, 'uh-huh' => 1, 'sure' => 1, 'makes sense' => 1, 'I see' => 1);
  $helps_to_do = array('bring out the flavor' => 1, 'soften it, for use with something else' => 1, 'draw out any impurities' => 1);
  $keep_that_in_mind = array('We\'ll have to keep that on our radar!' => 1, 'We\'ll have to check back on <em>that</em> one later!' => 1, 'Let\'s be sure to keep an eye on that.' => 1, 'I\'m sure we\'ll be seeing that again.' => 1);
  $yep = array('yep' => 1, 'that\'s right' => 1, 'yes' => 1, 'correct' => 1);
  $cooking_style = array('galantine' => 1, 'confit' => 1, 'tandoori' => 1, 'american' => 1, 'shabu-shabu' => 1, 'chettinad' => 1, 'teppanyaki' => 1, 'peking' => 1, 'korean' => 1);
  $colors = array('green' => 1, 'beige' => 1, 'yellow' => 1, 'orange' => 1, 'red' => 1, 'black' => 1, 'white' => 1, 'grey' => 1);
  
  $dishes = array(
    '%0% ice cream' => 1, '%0% enchilladas' => 1, '%0% salad' => 1, '%0% flambe' => 1,
    '%0% a la mode' => 1, '%0% and %1% soup' => 2, '%0% and %1% empanada' => 2, '%0% cookies' => 1,
    '%0% pasta' => 1, '%0%-crusted %1%' => 2, '%0%-stuffed %1%' => 2, '%0% and %1% ceviche' => 2,
    '%0% gazpacho' => 1, '%0% wrap' => 1, '%0% con jamon' => 1, '%0% croquette' => 1, '%0% tamale' => 1,
    '%0% and curried %1% chowder' => 2, '%0% and %1% custard' => 2, '%0%-stuffed chile relleno' => 1,
    '%0% meatballs' => 1, '%0% falukka tart' => 1, '%0%-%1% salad on flatbread' => 2,
    'spring %0% soup with %1%' => 2
  );
  
  $INGREDIENT = array_rand($INGREDIENTS);   

  $ANNOUNCER = 'Alton ' . ucfirst(array_rand($colors));
  $ANNOUNCER_SHORT = 'Alton';
  $HOST = 'The Chairman';
  $REPORTER = 'Kevin Something';
  $REPORTER_SHORT = 'Kevin';
  
  $report = '<p><b>' . $HOST . '</b>: Welcome!  You have all honed your craft, and assembled here for battle, but there is still one ingredient missing: our... <em>secret</em> ingredient.  The theme on which you all will offer your unique variations.  Today\'s secret ingredient is... ... <b>' . strtoupper($INGREDIENT) . '!</b>  LET THE BATTLE BEGIN!!</p>';
  
  $report .= '<p><b>[0:00] ' . $ANNOUNCER . '</b>: And the heat is on!  The chefs have 60 minutes to prepare a dish using the secret ingredient, ' . $INGREDIENT . '.  Looking around, I see we have several <em>varieties</em> of ' . $INGREDIENT . '.  We have ';
  $report .= array_rand($adjectives) . ' ' . $INGREDIENT;
  if(mt_rand(1, 2) == 1)
    $report .= ', ' . array_rand($adjectives) . ' ' . $INGREDIENT;
  $special_note = array_rand($adjectives);
  $report .= ', and some ' . $special_note . ' ' . $INGREDIENT . ', which has a more... <em>' . array_rand($flavors) . '</em> flavor than most ' . $INGREDIENT . '.</p>';  
  
  $timer = mt_rand(1, 6);
  
  $things_to_say = array(1, 2, 3, 4,/* 5,*/ 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16,
    17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35,
    36, 37);
  
  while($timer < 60)
  {
    $report .= '<p><b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ';
  
    $i = array_rand($things_to_say);
    $type = $things_to_say[$i];
    unset($things_to_say[$i]);
    
    $this_pet = $pets[array_rand($pets)];
    
    if($type == 1)
    {
      $i = array_rand($INGREDIENTS, 3);
  
      $r = array_rand($i);
      $i[$r] = '<em>' . $i[$r] . '</em>';
  
      $report .= $REPORTER . '</b>: ' . $ANNOUNCER_SHORT . ', I\'m at ' . $this_pet['petname'] . '\'s table, and ' . pronoun($this_pet['gender']) . ' has taken ' . array_rand($pots) . ' and put ' . $i[0] . ', ' . $i[1] . ', and some ' . array_rand($sizes) . ' ' . $i[2] . ' inside!<br />';  
      $report .= '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $ANNOUNCER . '</b>: That sounds ' . array_rand($opinions) . ', I wonder what ' . pronoun($this_pet['gender']) . '\'s going to do with it.';
    }
    else if($type == 2)
    {
      $i = array_rand($INGREDIENTS);
      $report .= $ANNOUNCER . '</b>: Over on ' . $this_pet['petname'] . '\'s side, we see some ' . array_rand($sizes) . ' ' . $i . ' - maybe some ' . array_rand($adjectives) . ' ' . $i . ' - being put into ' . array_rand($pots) . '.'; 
    }
    else if($type == 3)
    {
      $report .= $ANNOUNCER . '</b>: It looks like ' . $this_pet['petname'] . ' is taking apart some... ' . array_rand($INGREDIENTS) . '?  And has some ' . array_rand($INGREDIENTS) . ' waiting on the side.  That looks ' . array_rand($opinions) . '.';
    }
    else if($type == 4)
    {
      $report .= $REPORTER . '</b>: ' . $this_pet['petname'] . ' has taken some ' . array_rand($INGREDIENTS) . ' out of ' . array_rand($pots) . ', and put it into ' . array_rand($pots) . ' where ' . pronoun($this_pet['gender']) . ' has a ' . array_rand($INGREDIENTS) . ' ' . array_rand($purees) . '.<br />';
      $report .= '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $ANNOUNCER . '</b>: ' . ucfirst(array_rand($right)) . '.  ' . ucfirst(array_rand($opinions)) . '.';
    }
    else if($type == 5)
    {
      $quoted_fact = true;
      $report .= $ANNOUNCER . '</b>: ' . $INGREDIENTS[$INGREDIENT] . '</p>';
    }
    else if($type == 6)
    {
      $quoted_judging = true;
      $report .= $ANNOUNCER . '</b>: Now, each contestant\'s dish will be judged not only on taste, but on originality <em>and</em> presentation.  Once the clock hits 60 minutes, it\'s hands off, and judging will begin.'; 
    }
    else if($type == 7)
    {
      $report .= $ANNOUNCER . '</b>: We have ' . $this_pet['petname'] . ' working on what looks like a <em>' . array_rand($step_dishes) . '</em> there.<br />';
      $report .= '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $REPORTER . '</b>: I think you\'re right.  ' . ucfirst(pronoun($this_pet['gender'])) . '\'s nodding.';
    }
    else if($type == 8)
    {
      $report .= $ANNOUNCER . '</b>: ' . $this_pet['petname'] . ' has dropped some, ah, ' . array_rand($adjectives) . ' ' . $INGREDIENT . ' into ' . array_rand($pots) . ' with some... ' . array_rand($INGREDIENTS) . ' and ' . array_rand($INGREDIENTS) . '...';
    }
    else if($type == 9)
    {
      $types = array_rand($adjectives, 2);
      $report .= $ANNOUNCER . '</b>: We can see ' . $this_pet['petname'] . ' taking some ' . $types[0] . ' ' . $INGREDIENT . ' out of ' . array_rand($pots) . '.  Oh, I\'m told that\'s actually <em>' . $types[1] . '</em> ' . $INGREDIENT . '!  Sorry!';
    }
    else if($type == 10)
    {
      $i = array_rand($states) . ' ' . array_rand($INGREDIENTS);
      $report .= $REPORTER . '</b>: ' . $this_pet['petname'] . ' has taken out some ' . $i . '.<br />'; 
      $report .= '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $ANNOUNCER . '</b>: Some ' . $i . '?  ' . array_rand($keep_that_in_mind);
    }
    else if($type == 11)
    {
      $p = array_rand($cooking);
      $report .= $REPORTER . '</b>: ' . $this_pet['petname'] . ' is ' . $p . ' some ' . array_rand($INGREDIENTS) . '.<br />';
      $report .= '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $ANNOUNCER . '</b>: ' . ucfirst(array_rand($right)) . '.  ' . ucfirst($p) . ' will of course help to... ' . array_rand($helps_to_do) . '.';
    }
    else if($type == 12)
    {
      $report .= $ANNOUNCER . '</b>: On ' . $this_pet['petname'] . '\'s side we have a very interesting device coming out.  ' . array_rand($devices) . '.  I don\'t know how that\'s going to be used, but I\'m sure it\'s not just for show!';
      $quoted_device = true;
    }
    else if($type == 13)
    {
      $report .= $REPORTER . '</b>: ' . $this_pet['petname'] . ' has mixed together some ' . array_rand($adjectives) . ' ' . $INGREDIENT . '...<br />';
      $report .= '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $ANNOUNCER . '</b>: ' . ucfirst(array_rand($right)) . '.<br />';
      $report .= '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $REPORTER . '</b>: Some ' . array_rand($sizes) . ', ' . array_rand($adjectives) . ' ' . array_rand($INGREDIENTS) . '...<br />';
      $report .= '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $ANNOUNCER . '</b>: ' . ucfirst(array_rand($right)) . '.<br />';
      $report .= '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $REPORTER . '</b>: And ' . array_rand($INGREDIENTS) . '.<br />';
      $report .= '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $ANNOUNCER . '</b>: ' . ucfirst(array_rand($opinions)) . '.';
    }
    else if($type == 14)
    {
      $report .= $ANNOUNCER . '</b>: I see ' . $this_pet['petname'] . ' pouring some ' . array_rand($stuffs) . ' into ' . array_rand($pots) . '.<br />';
      $report .= '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $REPORTER . '</b>: ' . $ANNOUNCER_SHORT . ', that is a combination of ' . array_rand($INGREDIENTS) . ' and ' . array_rand($adjectives) . ' ' . array_rand($INGREDIENTS) . ' that ' . pronoun($this_pet['gender']) . ' was working on earlier.<br />';
      $report .= '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $ANNOUNCER . '</b>: Ah.  I wonder how it got that color.';
    }
    else if($type == 15)
      $report .= $ANNOUNCER . '</b>: ' . $this_pet['petname'] . ' has started ' . array_rand($cooking) . ' some ' . array_rand($INGREDIENTS) . '.  I have no idea what dish that\'s going in to.</p>';
    else if($type == 16)
    {
      $report .= $REPORTER . '</b>: ' . $this_pet['petname'] . ' is <em>attacking</em> some ' . array_rand($INGREDIENTS) . ' here.<br />';
      $report .= '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $ANNOUNCER . '</b>: Attacking?  I\'ll... be curious to see what ' . pronoun($this_pet['gender']) . ' does with that.';
    }
    else if($type == 17)
      $report .= $ANNOUNCER . '</b>: Now this is pretty cool.  It looks like ' . $this_pet['petname'] . ' is ' . array_rand($cooking) . ' some ' . $INGREDIENT . ' in a ' . array_rand($INGREDIENTS) . ' oil.  That\'s not something you see often here.';
    else if($type == 18)  
      $report .= $ANNOUNCER . '</b>: I was talking to ' . $this_pet['petname'] . ' before the battle, and ' . pronoun($this_pet['gender']) . ' said ' . pronoun($this_pet['gender']) . ' had visited ' . array_rand($places) . ' not long ago, so I think we can expect some influence <em>from</em> that visit in ' . p_pronoun($this_pet['gender']) . ' dishes here today.'; 
    else if($type == 19)
    {
      $i = array_rand($INGREDIENTS);
      $report .= $REPORTER . '</b>: ' . $this_pet['petname'] . ' has added some ' . $INGREDIENT . ' to a ' . $i . ' mixture here.<br />';
      $report .= '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $ANNOUNCER . '</b>: ' . $INGREDIENT . ' and ' . $i . '... sounds ' . array_rand($opinions) . '!';
    }
    else if($type == 20)
      $report .= $ANNOUNCER . '</b>: Oh, it looks like there has been a <em>' . array_rand($step_dishes) . ' problem</em> on ' . $this_pet['petname'] . '\'s side.  Problems will happen, of course; it\'s what you <em>do</em> with them that counts.';
    else if($type == 21)
      $report .= $ANNOUNCER . '</b>: Over on ' . $this_pet['petname'] . '\'s side, we\'ve got some ' . array_rand($INGREDIENTS) . ' being tossed around there with some ' . array_rand($INGREDIENTS) . '.  We could be looking at a ' . array_rand($cooking) . ' process there.</p>';
    else if($type == 22)
    {
      $i = array_rand($INGREDIENTS);
      $report .= $ANNOUNCER . '</b>: ' . $REPORTER_SHORT . ', what\'ve you got for me?<br />';
      $report .= '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $REPORTER . '</b>: Well on ' . $this_pet['petname'] . '\'s side, we have some ' . $i . ' being put together with some ' . array_rand($sizes) . ' ' . array_rand($INGREDIENTS) . ' in ' . array_rand($pots) . '.<br />';
      $report .= '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $ANNOUNCER . '</b>: ' . ucfirst(array_rand($right)) . ', ' . array_rand($right) . '.  You know, I bet that\'s for some kind of ' . $i . ' <em>' . array_rand($step_dishes) . '</em>.';
    }
    else if($type == 23)
      $report .= $ANNOUNCER . '</b>: I see ' . array_rand($pots) . ' of ' . array_rand($INGREDIENTS) . ' being put into the blast chiller...';
    else if($type == 24)
      $report .= $ANNOUNCER . '</b>: Here\'s ' . $this_pet['petname'] . ' weighing some ' . $INGREDIENT . ', and it looks like ' . pronoun($this_pet) . ' is going to put that on some kind of ' . array_rand($step_dishes) . '.';
    else if($type == 25)
      $report .= $ANNOUNCER . '</b>: ' . $this_pet['petname'] . ' is ' . array_rand($cooking) . ' some ' . array_rand($INGREDIENTS) . '.  Ooh, it doesn\'t look like ' . pronoun($this_pet['gender']) . ' likes how that turned out.';  
    else if($type == 26)
    {
      $d = array_rand($step_dishes);
      $report .= $ANNOUNCER . '</b>: ' . $this_pet['petname'] . ' has some ' . array_rand($INGREDIENTS) . ' there, in what looks like a... ' . $d . '?  Well, I\'m going call it a ' . $d . ' until it becomes something else.';
    }
    else if($type == 27)
    {
      $i = array_rand($INGREDIENTS, 2);
      $report .= $ANNOUNCER . '</b>: ' . $this_pet['petname'] . ' is breaking out ' . array_rand($adjectives) . ' ' . $i[0] . '.  I know it <em>looks</em> like ' . $i[1] . ', but it <em>is</em> ' . $i[0] . '<br />';
      $report .= '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $REPORTER . '</b>: What does that taste like?<br />';
      $report .= '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $ANNOUNCER . '</b>: ' . $i[0] . '!';
    }
    else if($type == 28)
    {
      $d = array_rand($step_dishes);
      $report .= $ANNOUNCER . '</b>: ' . $this_pet['petname'] . ' is putting a ' . $d . ' on some ' . $INGREDIENT . '.  We saw ' . t_pronoun($this_pet['gender']) . ' mixing up some ' . array_rand($INGREDIENTS) . ' for that ' . $d . ' earlier.';
    }
    else if($type == 29)
      $report .= $ANNOUNCER . '</b>: ' . $this_pet['petname'] . ' is plating some ' . $INGREDIENT . ' sauce.  I\'m not sure what\'s going to accompany that.';
    else if($type == 30)
      $report .= $ANNOUNCER . '</b>: Looking on ' . $this_pet['petname'] . '\'s side, I see ' . pronoun($this_pet['gender']) . ' is ' . array_rand($cooking) . ' some... well... I don\'t know what that is!';
    else if($type == 31)
    {
      $t = array_rand($adjectives, 2);
      $report .= $REPORTER . '</b>: ' . $this_pet['petname'] . ' has taken two ' . array_rand($sizes) . ' ' . $pots[array_rand($pots)] . ', and put ' . $t[0] . ' ' . $INGREDIENT . ' into one and ' . $t[1] . ' ' . $INGREDIENT . ' into another.<br />';
      $report .= '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $ANNOUNCER . '</b>: And I see some ' . array_rand($INGREDIENTS) . ' going in to one of those.<br />';
      $report .= '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $REPORTER . '</b>: ' . ucfirst(array_rand($yep)) . '.';
    }
    else if($type == 32)
    {
      $t = array_rand($adjectives, 2);
      $report .= $REPORTER . '</b>: ' . $this_pet['petname'] . ' is adding some ' . array_rand($INGREDIENTS) . ' to ' . array_rand($pots) . ' of ' . $t[0] . ' ' . $INGREDIENT . '.<br />';
      $report .= '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $ANNOUNCER . '</b>: And not to the ' . $t[1] . ' ' . $INGREDIENT . ' there?<br />';
      $report .= '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $REPORTER . '</b>: It doesn\'t look like it.<br />';
      $report .= '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $ANNOUNCER . '</b>: Hm!';
    }
    else if($type == 33)
    {
      $d = array_rand($step_dishes);
      $i = array_rand($INGREDIENTS, 3);
      $report .= $REPORTER . '</b>: ' . $this_pet['petname'] . ' is ' . array_rand($cooking) . ' a ' . $d . ' ' . pronoun($this_pet['gender']) . ' made earlier.<br />';
      $report .= '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $ANNOUNCER . '</b>: Do we know what\'s in that ' . $d . '?<br />';
      $report .= '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $REPORTER . '</b>: Ah, some ' . $i[0] . ', ' . $i[1] . ' and ' . $i[2] . '.';
    }
    else if($type == 34)
    {
      $report .= $REPORTER . '</b>: I just saw ' . $this_pet['petname'] . ' put some ' . $INGREDIENT . ' into ' . array_rand($pots) . '.<br />'; 
      $report .= '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $ANNOUNCER . '</b>: And ' . pronoun($this_pet['gender']) . '\'s adding some ' . array_rand($INGREDIENTS) . '.  I think we\'re looking at some kind of ' . array_rand($step_dishes) . ' here.';
    }
    else if($type == 35)
      $report .= $ANNOUNCER . '</b>: ' . $this_pet['petname'] . ' is adding water to a mixture of ' . array_rand($INGREDIENTS) . ' and ' . $INGREDIENT . ' in a ' . array_rand($pots) . '...';   
    else if($type == 36)
    {
      $i = array_rand($INGREDIENTS, 3);
      $a = array_rand($adjectives);
      $f = array_rand($flavors, 2);
      $report .= $ANNOUNCER . '</b>: ' . $REPORTER_SHORT . ', do we know what\'s in that ' . array_rand($pots) . ' that ' . $this_pet['petname'] . ' is working on?<br />' .
                 '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $REPORTER . '</b>: ' . $i[0] . ', ' . $i[1] . ', and ' . $a . ' ' . $i[2] . '.<br />' .
                 '<b>[0:' . str_pad($timer, 2, '0', STR_PAD_LEFT) . '] ' . $ANNOUNCER . '</b>: If memory serves, ' . $a . ' ' . $i[2] . ' is more ' . $f[0] . ' and ' . $f[1] . ' than regular ol\' ' . $i[2] . '.  ' . array_rand($keep_that_in_mind);
    }
    else if($type == 37)
      $report .= $ANNOUNCER . '</b>: ' . $this_pet['petname'] . ' is rolling up these herb-rubbed pieces of ' . array_rand($INGREDIENTS) . '. ' . ucfirst(pronoun($this_pet['gender'])) . ' could be cooking those ' . array_rand($cooking_style) . ' style.';
  
    $report .= '</p>';
  
    $timer += mt_rand(mt_rand(mt_rand(2, 3), 5), mt_rand(12, mt_rand(16, 20)));
  }
  
  $report .= '<p><b>*BZZZZT!*</b></p>' .
             '<p><b>' . $ANNOUNCER . '</b>:  That\'s the alarm, and time. is. up.  Let\'s see what our contestants have come up with...</p>';
  
  $scores = array();
  foreach($pets as $i=>$pet)
  {
    $scores[$i] = successes($pet['dex'] + $pet['int'] + $pet['wit'] + $pet['per'] + $pet['cra'] + $pet['open'] + $pet['conscientious']) +
      ($pet['merit_steady_hands'] == 'yes' ? 1 : 0);
  }
  
  arsort($scores);
  
  $report .= '<ol>';
  
  $place = 1;
  
  foreach($scores as $idnum=>$score)
  {
    $dish_s = array_rand($dishes);
    $num = $dishes[$dish_s];
  
    if(mt_rand(1, 10) == 1)
      $dish_s = ucfirst(array_rand($cooking_style)) . ' style ' . $dish_s;
      
    if(mt_rand(1, 3) == 1)
    {
      $dish_s .= ' with %' . $num . '%';
      $num++;
  
      if(mt_rand(1, 2) == 1)
      {
        $dish_s .= ' and %' . $num . '%';
        $num++;
      }
    }
  
    if(mt_rand(1, 2) == 1)
    {
      $dish_s .= ' in a %' . $num . '%';
      $num++;
    
      if(mt_rand(1, 3) == 1)
      {
        $dish_s .= '-%' . $num . '%';
        $num++;
      }
  
      if(mt_rand(1, 4) == 1)
        $dish_s .= ' glaze';
      else
        $dish_s .= ' sauce';
    } 
  
    if($num == 1)
      $dish = sprintf2($dish_s, array(0 => $INGREDIENT));
    else
    {
      $foods = array_rand($INGREDIENTS, $num);
      $foods[array_rand($foods)] = $INGREDIENT;
      
      $dish = sprintf2($dish_s, $foods);
    }
    
    $report .= '<li><a href="/petprofile.php?petid=' . $pets[$idnum]['idnum'] . '">' . $pets[$idnum]['petname'] . '</a>: ' . $dish . '</li>';

    $user_places[$idnum] = $place;

    if($place <= count($prizes))
      $user_prizes[$idnum][] = $prizes[$place - 1];
  
    if($place == 1)
      set_pet_badge($pets[$idnum], 'peppers');

    gain_love($pets[$idnum], success_roll(8, 10, 6));
    gain_safety($pets[$idnum], success_roll(3, 10, 6), true);
    gain_esteem($pets[$idnum], success_roll(9, 10, 6));

    train_pet($pets[$idnum], 'dex', ceil($exp_mult / 3));
    train_pet($pets[$idnum], 'int', ceil($exp_mult / 3));
    train_pet($pets[$idnum], 'wit', ceil($exp_mult / 3));
    train_pet($pets[$idnum], 'per', ceil($exp_mult / 3));
    train_pet($pets[$idnum], 'cra', ceil($exp_mult / 3));

    save_pet($pets[$idnum], array('love', 'safety', 'esteem', 'dex', 'int', 'wit', 'per', 'cra', 'dex_count', 'int_count', 'wit_count', 'per_count', 'cra_count'));
  
    ++$place;
  }

  $report .= '</ol>';

  return array($user_places, $user_prizes, $report);
}
