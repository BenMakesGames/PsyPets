<?php
require_once 'commons/itemlib.php';
require_once 'commons/userlib.php';

$TEAM_EVENT_TYPES = array(
  'ctf', 'picturades', 'tow',
);

$SOLO_EVENT_TYPES = array(
  'archery', 'crafts', 'brawl', 'cookoff', 'ddr', 'mining', 'race', 'fashion', 'fishing', 'jump',
  'roborena', 'hunt',
);

$HUNT_EVENT_TYPES = array(
  'hunt',
);

$EVENT_TYPES = array(
  'archery' => 'Archery',
  'crafts' => 'Arts & Crafts',
  'brawl' => 'Brawl',
  'ctf' => 'Capture the Flag',
  'cookoff' => 'Cook-Off',
  'ddr' => 'Dance Mania',
  'mining' => 'Digcraft Build',
  'race' => 'Distance Race',
  'fashion' => 'Fashion Show',
  'fishing' => 'Fishing',
  'jump' => 'Long Jump',
  'picturades' => 'Picturades',
  'roborena' => 'Roborena',
  'hunt' => 'Scavenger Hunt',
  'strategy' => 'Strategy Game',
  'swim' => 'Swimming Race',
  'tow' => 'Tug of War',
);

$EVENT_HELP_PAGES_BY_TYPE = array(
  'archery' => '/help/archery.php',
  'crafts' => '/help/arts_and_crafts.php',
  'brawl' => '/help/brawl.php',
  'ctf' => '/help/ctf.php',
  'cookoff' => '/help/cookoff.php',
  'ddr' => '/help/dancemania.php',
  'mining' => '/help/digcraft.php',
  'race' => '/help/race.php',
  'fashion' => '/help/fashionshow.php',
  'fishing' => '/help/fishingcompetition.php',
  'jump' => '/help/longjump.php',
  'picturades' => '/help/picturades.php',
  'roborena' => '/help/roborena.php',
  'hunt' => '/help/scavengerhunt.php',
  'strategy' => '/help/strategy.php',
  'swim' => '/help/swimming.php',
  'tow' => '/help/tug_of_war.php',
);

function max_event_level($min_level)
{
  return max($min_level + 10, $min_level * 2);
}

function get_event_count($where_clause)
{
  $command = 'SELECT COUNT(idnum) AS c FROM monster_events WHERE ' . $where_clause;
  $data = fetch_single($command, 'fetching park event count');

  return $data['c'];
}

function get_event_details($where_clause, $order_clause, $start, $limit)
{
  $command = 'SELECT idnum,name,fee,prizes,prizedescript,minlevel,maxlevel,minparticipant,event,participants,finished,host,graphic,timestamp FROM monster_events WHERE ' . $where_clause . ' ORDER BY ' . $order_clause . ' LIMIT ' . $start . ',' . $limit;
  $events = fetch_multiple($command, 'fetching event details');

  return $events;
}

function delete_and_refund_event(&$event, $notify_hoster = true, $explanation = '')
{
  $now = time();
  
  $command = 'DELETE FROM `monster_events` WHERE idnum=' . $event['idnum'] . ' LIMIT 1';
  fetch_none($command, 'deleting event');

  $participants = explode(',', $event['participants']);

  foreach($participants as $petid_f)
  {
    $petid = substr($petid_f, 1, strlen($petid_f) - 2);

    $command = 'SELECT user FROM `monster_pets` WHERE `idnum`=' . (int)$petid . ' LIMIT 1';
    $this_pet = fetch_single($command, 'fetching event participant');

    $refund[$this_pet['user']] += $event['fee'];
  }

  foreach($refund as $username=>$amount)
  {
    if(strtolower($username) == $SETTINGS['site_ingame_mailer'])
      continue;

    if($amount > 0)
    {
      $command = "UPDATE monster_users SET money=money+$amount WHERE user=" . quote_smart($username) . ' LIMIT 1';
      fetch_none($command, 'refunding participants');

      add_transaction($username, $now, 'Park Event refund for canceled event', $amount);
    }

    psymail_user($username, $SETTINGS['site_ingame_mailer'], '"' . $event['name'] . '" park event cancelled', 'This event was cancelled.  Your total entrance fees of ' . $amount . '<span class="money">m</span> have been refunded.');
  }

  if($notify_hoster && $explanation == '')
    $explanation = 'This event has been cancelled.';
  
  if(strlen($event['prizes']) > 0)
  {
    $prizes = explode(',', $event['prizes']);

    foreach($prizes as $item)
    {
      $command = 'UPDATE `monster_inventory` SET user=' . quote_smart($event['host']) . ", location='storage/incoming', changed=$now WHERE idnum=$item LIMIT 1";
      fetch_none($command, 'returning prize items');
    }

    flag_new_incoming_items($event['host']);
  
    if($notify_hoster)
    {
      psymail_user(
        $event['host'], $SETTINGS['site_ingame_mailer'],
        '"' . $event['name'] . '" park event cancelled',
        $explanation . '  The prize items you were offering have been returned to your Incoming.',
        count($prizes)
      );
    }
  }
  else
  {
    if($notify_hoster)
    {
      psymail_user(
        $event['host'], $SETTINGS['site_ingame_mailer'],
        '"' . $event['name'] . '" park event cancelled',
        $explanation
      );
    }
  }
}
?>
