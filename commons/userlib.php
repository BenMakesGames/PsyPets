<?php
require_once 'commons/formatting.php';
require_once 'commons/utility.php';
require_once 'commons/newslib.php';

$simulated_time = 0;
$USER_ID_CACHE = array();

function normalized_display_name($name)
{
  $name = strtolower($name);
  $name = str_replace(
    array('_', ' ', '.', '!', '?', '~', 'l', '1', '0'),
    array( '',  '',  '',  '',  '',  '', 'i', 'i', 'o'),
    $name
  );
  
  return $name;
}

function resident_link($display, $extra = '')
{
  return '<a href="/residentprofile.php?resident=' . link_safe($display) . '">' . $display . $extra . '</a>';
}

function user_age($user)
{
  list($Y, $m, $d) = explode('-', $user['']);

  return(date('md') < $m . $d ? date('Y') - $Y - 1 : date('Y') - $Y);
}

function user_avatar($user)
{
  if($user['is_a_whale'] == 'yes')
    return '/gfx/avatars/special-secret/awesome.png';
  else
    return '/gfx/avatars/' . $user['graphic'];
}

function has_logged_events_byuser($userid)
{
  $command = "SELECT * FROM monster_petlogs WHERE userid=$userid LIMIT 1";
  $event = fetch_single($command, 'checking for pet logs');

  return($event !== false);
}

function clear_logged_events_byuser_bypet($userid, $petid)
{
  $command = "DELETE FROM monster_petlogs WHERE userid=$userid AND petid=$petid";
  fetch_none($command, 'clear_logged_events_byuser_bypet');
}

function clear_logged_events_byuser($userid)
{
  $command = "DELETE FROM monster_petlogs WHERE userid=$userid";
  fetch_none($command, 'user library > clear all logs');
}

function newer_log_exists_byuser_bypet($userid, $petid, $timestamp)
{
  $command = "SELECT timestamp FROM monster_petlogs WHERE userid=$userid AND petid=$petid AND timestamp>$timestamp LIMIT 1";
  $event = fetch_single($command, 'checking for new pet logs');

  return($event !== false);
}

function get_logged_events_count_byuser_bypet($userid, $petid)
{
  $command = "SELECT COUNT(idnum) AS c FROM monster_petlogs WHERE userid=$userid AND petid=$petid";
  $data = fetch_single($command, 'fetching pet log count');
  
  return $data['c'];
}

function get_logged_events_byuser_bypet($userid, $petid, $pagenum)
{
  $start = ($pagenum - 1) * 20;
  $length = 20;

  $command = "SELECT * FROM monster_petlogs WHERE userid=$userid AND petid=$petid ORDER BY timestamp DESC,hour ASC,idnum ASC LIMIT $start, $length";
  $events = fetch_multiple($command, 'userlib.php/get_logged_events_byuser_bypet()');

  return $events;
}

function get_new_logged_events_byuser_bypet($userid, $petid, $oldtime)
{
  $start = ($pagenum - 1) * 20;
  $length = 20;

  $command = "SELECT * FROM monster_petlogs WHERE userid=$userid AND petid=$petid AND timestamp>" . $oldtime . ' ORDER BY timestamp DESC,hour ASC,idnum ASC';
  $events = fetch_multiple($command, 'userlib.php/get_new_logged_events_byuser_bypet()');

  return $events;
}

function get_logged_events_byuser_orderbyname($userid)
{
  $command = "SELECT a.* FROM monster_petlogs AS a,monster_pets AS b WHERE a.userid=$userid AND b.idnum=a.petid ORDER BY b.petname ASC";
  $events = fetch_multiple($command, 'userlib.php/get_logged_events_byuser_orderbyname()');

  return $events;
}

function get_logged_events_byuser_orderbysortid($userid)
{
  $command = "SELECT a.* FROM monster_petlogs AS a,monster_pets AS b WHERE a.userid=$userid AND b.idnum=a.petid ORDER BY b.orderid ASC";
  $events = fetch_multiple($command, 'userlib.php/get_logged_events_byuser_orderbysortid()');

  return $events;
}

$DELAYED_PET_LOG_INSERTS = array();

function process_pet_log_cache()
{
	global $DELAYED_PET_LOG_INSERTS;
	
	if(count($DELAYED_PET_LOG_INSERTS) > 0)
	{
		foreach($DELAYED_PET_LOG_INSERTS as $keyset=>$values)
		{
			$command = 'INSERT INTO monster_petlogs (' . implode(',', array_keys($values[0])) . ') VALUES ';
			
			$row_count = count($values);
	
			for($i = 0; $i < $row_count; ++$i)
			{
				$command .= '(' . implode(',', $values[$i]) . ')';
				if($i < $row_count - 1)
					$command .= ',';
			}
			
			$GLOBALS['database']->FetchNone($command);
		}
	}
	
	$DELAYED_PET_LOG_INSERTS = array();
}

function add_logged_event_cached($userid, $petid, $hour, $timetype, $type, $description = false, $extras = array())
{
	global $DELAYED_PET_LOG_INSERTS;
	
  if($description !== false)
  {
    global $now;

    $values = $extras;

    $values['userid'] = (int)$userid;
    $values['petid'] = (int)$petid;
    $values['type'] = quote_smart($timetype);
    $values['hour'] = (int)$hour;
    $values['timestamp'] = $now;
    $values['description'] = quote_smart($description);

    $fields = array_keys($values);

		$DELAYED_PET_LOG_INSERTS[implode(',', $fields)][] = $values;
  }

  if($type !== false)
  {
    $command = 'UPDATE psypets_petstats SET ' . $type . '=' . $type . '+1 WHERE petid=' . $petid . ' LIMIT 1';
    fetch_none($command, 'logging pet event stats');
    if($GLOBALS['database']->AffectedRows() == 0)
    {
      $command = 'INSERT INTO psypets_petstats (petid, ' . $type . ') VALUES ' .
                 '(' . $petid . ', 1)';
      fetch_none($command, 'inserting pet event stats');
    }
  }
}

function add_logged_event($userid, $petid, $hour, $timetype, $type, $description = false, $extras = array())
{
  if($description !== false)
  {
    global $now;

    $values = $extras;

    $values['userid'] = (int)$userid;
    $values['petid'] = (int)$petid;
    $values['type'] = quote_smart($timetype);
    $values['hour'] = (int)$hour;
    $values['timestamp'] = $now;
    $values['description'] = quote_smart($description);

    $fields = array_keys($values);

    $command = '
      INSERT INTO monster_petlogs
      (' . implode(',', $fields) . ')
      VALUES
      (' . implode(',', $values) . ')
    ';
    fetch_none($command, 'logging pet event');
  }

  if($type !== false)
  {
    $command = 'UPDATE psypets_petstats SET ' . $type . '=' . $type . '+1 WHERE petid=' . $petid . ' LIMIT 1';
    fetch_none($command, 'logging pet event stats');
    if($GLOBALS['database']->AffectedRows() == 0)
    {
      $command = 'INSERT INTO psypets_petstats (petid, ' . $type . ') VALUES ' .
                 '(' . $petid . ', 1)';
      fetch_none($command, 'inserting pet event stats');
    }
  }
}

function record_new_friending($userid, $frienderid)
{
  global $now;

  $command = 'DELETE FROM psypets_friendreport WHERE userid=' . $userid . ' AND friendedby=' . $frienderid;
  fetch_none($command, 'deleting old friending records');

  if($GLOBALS['database']->AffectedRows() == 0)
  {
    $command = 'UPDATE monster_users SET newfriend=\'yes\' WHERE idnum=' . $userid . ' LIMIT 1';
    fetch_none($command, 'flagging new friending');
  }

  $command = 'INSERT INTO psypets_friendreport (userid, timestamp, friendedby) VALUES ' .
             '(' . $userid . ', ' . $now . ', ' . $frienderid . ')';
  fetch_none($command, 'recording friending record');
}

function is_friend(&$me, &$friend)
{
  $data = fetch_single('
    SELECT idnum
    FROM psypets_user_friends
    WHERE
      userid=' . (int)$me['idnum'] . '
      AND friendid=' . (int)$friend['idnum'] . '
    LIMIT 1
  ');
  
  return($data !== false);
}

function is_enemy(&$me, &$enemy)
{
  $data = fetch_single('
    SELECT idnum
    FROM psypets_user_enemies
    WHERE
      userid=' . (int)$me['idnum'] . '
      AND enemyid=' . (int)$enemy['idnum'] . '
    LIMIT 1
  ');
  
  return($data !== false);
}

function add_friend(&$me, &$friend, $inform_new_friend = true)
{
  if(!is_friend($me, $friend))
  {
    fetch_none('
      INSERT INTO psypets_user_friends
      (userid, friendid)
      VALUES
      (' . (int)$me['idnum'] . ', ' . (int)$friend['idnum'] . ')
    ');

    if($inform_new_friend && !is_enemy($friend, $me))
      record_new_friending($friend['idnum'], $me['idnum']);
  }
}

function remove_friend(&$me, &$friend)
{
  fetch_none('
    DELETE FROM psypets_user_friends
    WHERE
      userid=' . (int)$me['idnum'] . '
      AND friendid=' . (int)$friend['idnum'] . '
  ');
}

function add_enemy(&$me, &$enemy)
{
  if(!is_enemy($me, $enemy))
  {
    fetch_none('
      INSERT INTO psypets_user_enemies
      (userid, enemyid)
      VALUES
      (' . (int)$me['idnum'] . ', ' . (int)$enemy['idnum'] . ')
    ');
  }
}

function remove_enemy(&$me, &$enemy)
{
  fetch_none('
    DELETE FROM psypets_user_enemies
    WHERE
      userid=' . (int)$me['idnum'] . '
      AND enemyid=' . (int)$enemy['idnum'] . '
  ');
}

function flag_madesale($userid)
{
  $command = "UPDATE monster_users SET `bankflag`='yes' WHERE idnum=$userid LIMIT 1";
  fetch_none($command, 'flag_madesale');
}

function clear_madesale($userid)
{
  $command = "UPDATE monster_users SET `bankflag`='no' WHERE idnum=$userid LIMIT 1";
  fetch_none($command, 'clear_madesale');
}

function check_store_stock($user)
{
  $command = "SELECT idnum FROM monster_inventory WHERE user=" . quote_smart($user['user']) . " AND forsale>0 LIMIT 1";
  $listed_item = fetch_single($command, 'checking if store has stock');

  if($listed_item === false)
  {
    $command = "UPDATE monster_users SET openstore='no' WHERE idnum=" . (int)$user['idnum'] . " LIMIT 1";
    fetch_none($command, 'closing resident store');
  }
}

function get_badges_byuserid($userid)
{
  $command = 'SELECT * FROM psypets_badges WHERE userid=' . $userid . ' LIMIT 1';
  $badges = fetch_single($command, 'user library > get badges');
  return $badges;
}

function set_badge($userid, $badge)
{
  $command = 'UPDATE psypets_badges SET `' . $badge . '`=\'yes\' WHERE userid=' . $userid . ' LIMIT 1';
  fetch_none($command, 'user library > set badge');
}

function delete_old_projects_by_userid($userid)
{
  $command = "DELETE FROM monster_projects WHERE complete='yes' AND location=$userid AND completetime<" . ($now - (60 * 60 * 24));
  fetch_none($command, 'userlib.php/delete_old_projects_by_userid()');
}

function get_projects_by_userid($userid)
{
  $command = "SELECT * FROM monster_projects WHERE location=$userid ORDER BY complete,idnum ASC";
  $projects = fetch_multiple($command, 'get_projects_by_userid');
  
  return $projects;
}

function psymail_user2(&$to, &$from, $subject, $body, $items)
{
  global $now;

  $command = 'INSERT INTO `monster_mail` ' .
             '(`to`, `from`, `date`, `subject`, `message`, `attachments`) VALUES ' .
             '(' . quote_smart($to['user']) . ', ' . quote_smart($from['user']) . ', ' . $now . ', ' . quote_smart($subject) . ', ' . quote_smart($body) . ', ' . $items . ')';
  fetch_none($command, 'userlib.php/psymail_user()');

  $mailid = $GLOBALS['database']->InsertID();

  $field = ($from['is_npc'] == 'no' ? 'email_personal' : 'email_game');
  
  if($to[$field] == 'yes')
  {
    global $SETTINGS;

    $mail_descriptions = array(
      'Sounds interesting!', 'How compelling!', 'That seems kind of interesting...',
      'Are you intrigued?  I\'m intrigued!', 'How thoughtful!', 'Sounds like a good read!',
      'Is that good?  I can\'t tell.', 'How exciting!', 'Well that\'s random.',
      'Mysterious!', 'I wonder what it says...', 'It could be important...',
    );

    $description = $mail_descriptions[array_rand($mail_descriptions)];

    $message = '<strong>' . $from['display'] . '</strong> has sent you a PsyMail!';
    if($items > 0)
      $message .= ' (With ' . $item . ' item' . ($items != 1 ? 's' : '') . ' attached!)';
    $message .= '<br /><br />It\'s entitled "' . $subject . '".  ' . $description . '  ' .
                '<a href="https://' . $SETTINGS['site_domain'] . '/readmail.php?mail=' . $mailid . '">Would you like to read it?</a>';

    $message = format_text($message);

    mail($to['email'], 'PsyMail notification: a message from ' . $from['display'] . '!', $message, "MIME-Version: 1.0\nContent-type: text/html; charset=utf-8\nFrom: " . $SETTINGS['site_mailer']);
  }

  if($to['newmail'] != 'yes')
    flag_new_mail($to['user']);
}

function flag_new_mail($username)
{
  $command = 'UPDATE monster_users SET newmail=\'yes\' WHERE user=' . quote_smart($username) . ' LIMIT 1';
  fetch_none($command, 'flagging user as having received new mail');
}

function psymail_user($to, $from, $subject, $body, $items = 0)
{
  global $now;

  $command = 'INSERT INTO `monster_mail` ' .
             '(`to`, `from`, `date`, `subject`, `message`, `attachments`) VALUES ' .
             '(' . quote_smart($to) . ', ' . quote_smart($from) . ', ' . $now . ', ' . quote_smart($subject) . ', ' . quote_smart($body) . ', ' . $items . ')';
  fetch_none($command, 'userlib.php/psymail_user()');

  flag_new_mail($to);
}

function get_user_profile($userid)
{
  $command = "SELECT * FROM monster_profiles WHERE idnum=$userid LIMIT 1";
  $profile = fetch_single($command, 'userlib.php/get_user_profile()');

  if($profile == false)
  {
    $command = "INSERT INTO monster_profiles (`idnum`) VALUES ('$userid')";
    fetch_none($command, 'userlib.php/get_user_profile()');

    $command = "SELECT * FROM monster_profiles WHERE idnum=$userid LIMIT 1";
    $profile = fetch_single($command, 'userlib.php/get_user_profile()');
  }

  return $profile;
}

function save_user_profile($profile)
{
  $command = 'UPDATE monster_profiles SET ' .
             '`enabled`=\'' . $profile['enabled'] . '\', ' .
             '`name`=' . quote_smart($profile['name']) . ', ' .
             '`aim`=' . quote_smart($profile['aim']) . ', ' .
             '`yahoo`=' . quote_smart($profile['yahoo']) . ', ' .
             '`msn`=' . quote_smart($profile['msn']) . ', ' .
             '`skype`=' . quote_smart($profile['skype']) . ', ' .
             '`facebook`=' . quote_smart($profile['facebook']) . ', ' .
             '`myspace`=' . quote_smart($profile['myspace']) . ', ' .
             '`url`=' . quote_smart($profile['url']) . ', ' .
             '`show_age`=' . quote_smart($profile['show_age']) . ', ' .
             '`gender`=' . quote_smart($profile['gender']) . ', ' .
             '`locationsearch`=' . quote_smart($profile['locationsearch']) . ', ' .
             '`zip`=' . (int)$profile['zip'] . ', ' .
             '`latitude`=' . (float)$profile['latitude'] . ', ' .
             '`longitude`=' . (float)$profile['longitude'] . ', ' .
             '`location`=' . quote_smart($profile['location']) . ' ' .
             'WHERE idnum=' . (int)$profile['idnum'] . ' LIMIT 1';
  fetch_none($command, 'saving resident profile');
}

function get_user_byuser($user, $select = '*')
{
  static $users_by_user;
  
  if(!$users_by_user[$user][$select])
  {
    $users_by_user[$user][$select] = fetch_single('
      SELECT ' . $select . '
      FROM monster_users
      WHERE user=' . quote_smart($user) . '
      LIMIT 1
    ');
  }
  
  return $users_by_user[$user][$select];
}

function get_user_byemail($email, $select = '*')
{
  $command = 'SELECT ' . $select . ' FROM monster_users WHERE email=' . quote_smart($email) . ' LIMIT 1';
  $this_user = fetch_single($command, 'fetching resident by e-mail address');

  return $this_user;
}

function get_admin_byuser($username)
{
  $command = 'SELECT * FROM monster_admins WHERE user=' . quote_smart($username) . ' LIMIT 1';
  $admin = fetch_single($command, 'getting admin rights for resident');

  return $admin;
}

function get_user_byid($idnum, $select = '*')
{
  global $USER_ID_CACHE;

  if(!is_array($USER_ID_CACHE[$select]) || !array_key_exists($idnum, $USER_ID_CACHE[$select]))
  {
    $command = 'SELECT ' . $select . ' FROM monster_users WHERE idnum=' . $idnum . ' LIMIT 1';
    $USER_ID_CACHE[$select][$idnum] = fetch_single($command, 'fetching resident by idnum');
  }

  return $USER_ID_CACHE[$select][$idnum];
}

function get_user_bydisplay($display, $select = '*')
{
  $command = 'SELECT ' . $select . ' FROM monster_users WHERE `display`=' . quote_smart($display) . ' LIMIT 1';
  $this_user = fetch_single($command, 'fetching resident by resident name');

  return $this_user;
}

function give_money(&$user, $amount, $message, $details = '')
{
  if($amount == 0)
    return;

  global $now;

  $command = 'UPDATE monster_users SET money=money+' . $amount . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  fetch_none($command, 'give_money');

  add_transaction($user['user'], $now, $message, $amount, $details);
}

function take_money(&$user, $amount, $message, $details = '')
{
  if($amount == 0)
    return;

  global $now;

  $command = 'UPDATE monster_users SET money=money-' . $amount . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  fetch_none($command, 'take_money');

  add_transaction($user['user'], $now, $message, -$amount, $details);
}

function flag_new_incoming_items($user)
{
  $command = 'UPDATE monster_users SET newincoming=\'yes\' WHERE user=' . quote_smart($user) . ' LIMIT 1';
  fetch_none($command, 'flagging player as having new incoming items');
}

function amelia_earhart_number($user)
{
  mt_srand($user['idnum']);

  $number = mt_rand(10000000, 99999999);

  mt_srand();

  return $number;
}

function book_code_number($user)
{
  srand($user['idnum']);
  
  $digits = array('8', '4', '6', '2', '9', '3');

  shuffle($digits);
  
  return implode('', $digits);
}
?>
