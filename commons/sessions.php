<?php
require_once 'commons/settings.php';
require_once 'commons/ip.php';
require_once 'commons/timelib.php';
require_once 'libraries/cookie_messages.php';
require_once 'libraries/db_messages.php';

function load_user_pets($user, &$userpets)
{
  if($user['pets_loaded'] === true)
    return false;

	$userpets = $GLOBALS['database']->FetchMultiple(
		'SELECT * FROM monster_pets ' .
    'WHERE `user`=' . $GLOBALS['database']->Quote($user['user']) . ' AND location=\'home\' ORDER BY orderid,idnum ASC'
	);

  foreach($userpets as $i=>&$pet)
  {
		$pet['localid'] = $i;
  }

  $user['pets_loaded'] = true;

  return true;
}

function load_user_projects($user, &$userprojects)
{
  if($user['projects_loaded'] === true)
    return false;

	$userprojects = $GLOBALS['database']->FetchMultiple(
		'SELECT * FROM monster_projects ' .
    'WHERE userid=' . (int)$user['idnum'] . ' AND complete=\'no\' ORDER BY priority DESC,idnum ASC'
	);

  foreach($userprojects as $i=>&$project)
		$project['localid'] = $i;

  $user['projects_loaded'] = true;

  return true;
}

// start important things

header('Content-Type: text/html; charset=utf-8');

$its_your_birthday = false;

$now = time();
list($now_day, $now_month, $now_year) = explode(' ', date('j n Y', $now));

if($NO_LOGIN === true)
  $user = false;
else
{
  require_once 'models/psyDBObject.class.php';
  require_once 'models/User.class.php';
  require_once 'models/FailedLogins.class.php';
  require_once 'models/LoginHistory.class.php';
  require_once 'models/House.class.php';

  $user_object = User::GetBySession();
  
  $user = $user_object->RawData();
}

$userpets = array();
$userprojects = array();
$my_age = 0;

$st_patricks = ($now_month == 3 && $now_day == 17);
$PSYPETS_BIRTHDAY = ($now_month == 3 && ($now_day >= 21 && $now_day <= 23));

if($now_month == 4 || $now_month == 3)
  $EASTER = psypets_easter();
else
  $EASTER = 0;

// if we're logged in...
if($user !== false)
{
  if($user['birthday'] == '0000-00-00' && $whereat != 'getbirthday')
  {
    header('Location: /getbirthday.php');
    exit();
  }

  if($user['readtos'] != 'yes' && $reading_tos !== true)
  {
    header('Location: /meta/termsofservice.php');
    exit();
  }

  if($user['childlockout'] == 'yes' && $child_safe === false)
  {
    header('Location: /403.php');
    exit();
  }

  if($require_petload !== 'no')
  {
    load_user_pets($user, $userpets);
  }

  if($invisible !== 'yes')
  {
    if($now - $user['lastclickcheck'] > 10)
    {
      $clicks = $user['clickcount'] + 1;
/*
      if($clicks >= 20)
      {
        $command = 'INSERT INTO psypets_botreport (userid, timestamp, clicks, useragent) VALUES ' .
                   '(' . $user['idnum'] . ', ' . $now . ', ' . $clicks . ', ' . quote_smart($_SERVER['REQUEST_URI']) . ')';
        fetch_none($command, 'updating last activity (2)');
      }
*/
      $extra = ',clickcount=0,lastclickcheck=' . $now;
    }
    else
      $extra = ',clickcount=clickcount+1';
  
    $pagehit_command = 'UPDATE monster_users SET lastactivity=' . $now . $extra . ',lastclient=' . quote_smart($_SERVER['HTTP_USER_AGENT']) . ',lastcountry=' . quote_smart($_SERVER['HTTP_CF_IPCOUNTRY']) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    fetch_none($pagehit_command, 'updating last activity (1)');
    
    $extra = '';
  }

  // URL parameter to temporarily view the page without admin rights: admin=no
  if($_GET['admin'] != 'no')
  {
    $command = 'SELECT * FROM monster_admins WHERE `user`=' . quote_smart($user['user']) . ' LIMIT 1';
    $admin = fetch_single($command, 'fetching resident admin rights');
  }
  else
    $admin = false;

  if($admin === false)
    $admin['alphalevel'] = 0;

  $user['admin'] = $admin;

  $birthday = explode('-', $user['birthday']);
  $its_your_birthday = ($birthday[1] == $now_month && $birthday[2] == $now_day);

  $my_age = ($now - strtotime($user['birthday'])) / (365 * 24 * 60 * 60);

  $command = 'SELECT * FROM monster_houses WHERE userid=' . $user['idnum'] . ' LIMIT 1';
  $house = fetch_single($command, 'fetching house');

  if($st_patricks)
  {
    if($user['show_totemgardern'] == 'no')
    {
      $user['show_totemgarden'] = 'yes';
      $command = 'UPDATE monster_users SET show_totemgardern=\'yes\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
      fetch_none($command, 'revealing the totem pole garden...');
    }
  }
}

if(!$AJAX)
{
	$db_messages = get_new_db_messages($user['idnum']);
	if(count($db_messages) > 0)
	{
    foreach($db_messages as $this_message)
      $CONTENT['messages'][] = '<div class="flash-message ' . $DB_MESSAGE_CATEGORY_NAMES[$this_message['category']] . '">' . $this_message['message'] . '</div>';
	}

	$cookie_messages = get_cookie_messages($user['idnum']);
	if(count($cookie_messages) > 0)
	{
    foreach($cookie_messages as $this_message)
      $CONTENT['messages'][] = $this_message;
	}
}

if($user === false)
{
	if($require_login != 'no')
	{
		if($AJAX === true)
			echo 'You are no longer logged in!  Please reload the page and log in.';
		else
		{
			require 'commons/require_login.php';
		}

		exit();
	}
	else
	{
		$user = array();
		$userpets = array();
		$userprojects = array();
	}
}


?>
