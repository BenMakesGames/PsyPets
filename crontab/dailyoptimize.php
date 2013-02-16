<?php
/* deletes old data and OPTIMIZEs TABLEs.
*/

$_GET['maintenance'] = 'no';

//ini_set('include_path', '/your/web/root');

require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/userlib.php';
require_once 'commons/timelib.php';
require_once 'commons/parklib.php';

$now = time();
list($now_day, $now_month, $now_year) = explode(' ', date('j n Y', $now));

  // St. Patrick's Day! {
    if(date('n j') == '3 17')
    {
      require_once 'commons/newslib.php';

      if(date('Y') % 2 == 0)
        $poster = 'mmansur';
      else
        $poster = 'lpawlak';

      $npc = get_user_byuser($poster, 'idnum,display');

      if($poster == 'mmansur')
        news_post(
          $npc['idnum'],
          'event',
          'St. Patrick\'s Day ' . date('Y') . '!',
          '<p>Somehow we\'re doing a St. Patrick\'s Day competition again!  I guess it\'s become somewhat of a tradition of Lakisha\'s and mine...</p><p>Well anyway, please help us out!  Lakisha and I have even arranged to hand out prizes to the top 25 helpers!  <a href="stpatricks.php?where=totem">Stop by, and we\'ll tell you all the details!</a></p>'
        );
      else
        news_post(
          $npc['idnum'],
          'event',
          'St. Patrick\'s Day ' . date('Y') . '!',
          '<p>I hope you haven\'t forgotten about the competition Matalie and I hold every year?  It started as an alcohol-inspired bet, and somehow turned into a ridiculous tradition.</p><p>We\'re both aiming to win, of course, and willing to reward those who help us... the top 25, anyway... <a href="stpatricks.php?where=bank">come talk to us for details.</a></p>'
        );

      $database->FetchNone('UPDATE monster_users SET newcityhallpost=\'yes\'');

      echo '* St. Patrick\'s Day city hall post has been made!' . "\r\n";
    }
    else if(date('n j') == '3 18')
      psymail_user('telkoth', 'psypets', 'St. Patrick\'s Day!', 'Don\'t forget to run this script: adminstpatricksday.php (and check for alt abuse!)', 0);
  // }
  
  
  // Thanksgiving! {
    if(is_thanksgiving())
    {
      require_once 'commons/newslib.php';
      
      $npc = get_user_byuser('mwitford', 'idnum,display');
      
      news_post(
        $npc['idnum'],
        'event',
        'Thanksgiving Day!',
        '<p>Thaddeus and I worked together to get something together for you guys for Thanksgiving!  Come visit me at the Library, and ask about my Thanksgiving Scrolls!</p>'
      );

      $command = 'UPDATE monster_users SET newcityhallpost=\'yes\'';
      $database->FetchNone($command, 'writenewspost.php');

      echo '* Thanksgiving Day city hall post has been made!' . "\r\n";
    }

  // Virtual Hide-and-go-Seek Tag {
    $database->FetchNone('DELETE FROM psypets_wired WHERE lastplay<' . ($now - 43200));
  // }

  // Easter {
    if(is_easter(time() + 4 * 24 * 60 * 60))
    {
      require_once 'commons/newslib.php';

      $npc = get_user_byuser('eheidel', 'idnum,display');

      news_post(
        $npc['idnum'],
        'event',
        'Easter ' . date('Y') . '!',
        '<p>I think we\'ll start seeing those "Plastic Eggs" show up again this year, and I\'d like to once again ask for the community\'s help in collecting them.</p><p>Find me in Room 106 at the HERG Research Lab (or, as its commonly called, the "City Hall") for more details.</p><p>Thanks in advance, and happy hunting!</p>'
      );

      $database->FetchNone('UPDATE monster_users SET newcityhallpost=\'yes\'');
    }
    else if(is_easter(time() - 24 * 60 * 60))
    {
      $database->FetchNone('DELETE FROM monster_inventory WHERE itemname=\'Plastic Egg\'');
    }
  // }

  // The One Ring {
    $database->FetchNone('DELETE FROM monster_inventory WHERE itemname=\'The One Ring\'');

    $command = 'SELECT COUNT(idnum) AS c FROM monster_users WHERE lastactivity>=' . ($now - 3 * 24 * 60 * 60);
    $data = $database->FetchSingle($command, 'fetching number of eligible residents');

    $num_residents = (int)$data['c'];
    $resident_picked = mt_rand(1, $num_residents);

    $command = 'SELECT idnum,user FROM monster_users WHERE lastactivity>=' . ($now - 3 * 24 * 60 * 60) . ' LIMIT ' . $resident_picked . ',1';
    $new_bearer = $database->FetchSingle($command, 'finding new ring-bearer');

    set_badge($new_bearer['idnum'], 'ringbearer');
    add_inventory($new_bearer['user'], '', 'The One Ring', 'This ring found its way into your home, somehow.', 'home');

    echo '* User #' . $new_bearer['idnum'] . ' is the new ring-bearer.' . "\r\n";
  // }
    
  // Delete trashed PsyMails {
    $database->FetchNone('DELETE FROM monster_mail WHERE location=\'Trash\'');
  // }

  echo '* Deleted ', $database->AffectedRows(), ' trashed PsyMails.', "\r\n";
    
/*
  
  // Bot reporting based on fast clicking {
    $command = 'SELECT * FROM psypets_botreport ORDER BY userid,timestamp';
    $items = $database->FetchMultiple($command, 'fetching bot reports');

    $report = '';
    $last_userid = 0;
    foreach($items as $item)
    {
      if($item['userid'] != $last_userid)
      {
        $display = get_user_byid($item['userid'], 'display');
        $report .= '<br />{b}{r ' . $display['display'] . '}{/}<br />';
        $last_userid = $item['userid'];
      }

      $report .= '@' . $item['timestamp'] . ': ' . $item['clicks'] . ' clicks in 10 seconds (' . $item['useragent'] . ')<br />';
    }

    psymail_user('telkoth', 'psypets', 'Bot activity report', $report);

    $command = 'TRUNCATE TABLE psypets_botreport';
    $database->FetchNone($command, 'truncating bot reports');
  // }
*/
  // Delete month-old park events {
    $old_events = $database->FetchMultiple('SELECT * FROM monster_events WHERE finished=\'no\' AND timestamp<' . ($now - (60 * 60 * 24 * 28)));
    
    $deleted_events = 0;
    
    foreach($old_events as $event)
    {
      delete_and_refund_event($event, true, 'It did not receive enough participants to run after being up for four weeks.');
      $deleted_events++;
    }
    
    echo '* Cancelled ' . $deleted_events . ' Park events' . "\r\n";
  // }

  // Delete old plaza advertisements {
    $database->FetchNone('DELETE FROM psypets_advertising WHERE permanent=\'no\' AND expirytime<=' . $now);
  // }
    
  // Delete login and pet activity logs that are older than four weeks {
    $database->FetchNone('DELETE FROM monster_loginhistory WHERE timestamp<' . ($now - (60 * 60 * 24 * 28)));
  // }

  // Delete week-old pet logs {
    $database->FetchNone('DELETE FROM monster_petlogs WHERE timestamp<' . ($now - (60 * 60 * 24 * 7)));
  // }

  // Trim pet shelter down to (active residents / 5) pets {
    $command = 'SELECT numactiveusers FROM monster_statistics ORDER BY timestamp DESC LIMIT 1';
    $data = $database->FetchSingle($command, 'fetching recent resident count');

    $max_pet_shelter = (int)max(10, (int)$data['numactiveusers'] / 5);

    $command = 'SELECT COUNT(idnum) AS c FROM monster_pets WHERE user=\'psypets\' AND last_check<' . $now;
    $data = $database->FetchSingle($command, 'fetching pet shelter population');

    $pet_shelter = (int)$data['c'];

    if($pet_shelter > $max_pet_shelter)
    {
      $to_kill = $pet_shelter - $max_pet_shelter;

      $database->FetchNone('DELETE FROM monster_pets WHERE user=\'psypets\' AND last_check<' . $now . ' ORDER BY last_check ASC LIMIT ' . $to_kill);

      echo '* Killed ' . $database->AffectedRows() . ' Pet Shelter pets' . "\r\n";
    }/*
    // create pets to keep the shelter at least half-full
    else if($pet_shelter < $max_pet_shelter / 2)
    {
      $to_create = $max_pet_shelter / 2 - $pet_shelter;

      for($x = 0; $x < $to_create; ++$x)
        create_random_pet('psypets');
    }*/
  // }

  // Delete pet badges which do not have matching pets {
    $command = 'SELECT psypets_petbadges.petid,monster_pets.idnum FROM psypets_petbadges LEFT JOIN monster_pets ON psypets_petbadges.petid=monster_pets.idnum WHERE monster_pets.idnum IS NULL';
    $data = $database->FetchMultiple($command, 'fetching orphaned pet badges');

    if(count($data) > 0)
    {
      foreach($data as $value)
        $idnums[] = $value['petid'];
    
      $database->FetchNone('DELETE FROM psypets_petbadges WHERE petid IN (' . implode(',', $idnums) . ') LIMIT ' . count($idnums));
    }
  // }

  // delete 28-day-old game-rating votes {
    $database->FetchNone('DELETE FROM psypets_whatisbendoing WHERE lastchange<' . ($now - 60 * 60 * 24 * 28));
  // }

  // delete 2-day-old recycling store inventory {
    $quantities = $database->FetchMultiple('SELECT COUNT(idnum) AS qty,itemname FROM monster_inventory WHERE user IN (\'ihobbs\',\'grocerystore\') AND changed<' . ($now - 2 * 24 * 60 * 60) . ' GROUP BY(itemname) ORDER BY qty DESC');
  
    $database->FetchNone('DELETE FROM monster_inventory WHERE user IN (\'ihobbs\',\'grocerystore\') AND changed<' . ($now - 2 * 24 * 60 * 60));

    echo '* Deleted ' . $database->AffectedRows() . ' Refuse Store and Farmer\'s Market inventory' . "\r\n";

    $i = 0;
    foreach($quantities as $qty)
    {
      echo '** ' . $qty['itemname'] . ' x' . $qty['qty'] . "\r\n";
      if(++$i == 10) break;
    }
  // }

  if(mt_rand(1, 7) == 1)
    psymail_user('telkoth', 'telkoth', 'Check the To-do List!', '<a href="https://www.psypets.net/arrangewishes.php">To-do List</a>');

  echo 'Finished daily optimize.';

?>
