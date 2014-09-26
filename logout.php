<?php
$whereat = 'getbirthday';
$reading_tos = true;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';

if($user['multi_login'] == 'no' || $user['lastactivity'] < $now - 15 * 60)
{
  $command = 'UPDATE monster_users SET sessionid=0 WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'logout.php');
}

setcookie($SETTINGS['cookie_name'], '', 1, $SETTINGS['cookie_path'], $SETTINGS['cookie_domain']);
$_COOKIE = array();

require_once 'commons/statlib.php';
record_stat($user['idnum'], 'Logged Out', 1);

header('Location: /');
