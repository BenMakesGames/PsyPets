<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

require_once 'commons/admincheck.php';

$require_petload = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/sessions.php";
require_once "commons/userlib.php";

require_once 'models/User.class.php';

if($user['admin']['possessaccounts'] != "yes")
{
  header('Location: /n404/');
  exit();
}

$userid = (int)$_GET['user'];

$host_user = User::GetByID($userid);

if(!$host_user->IsLoaded() || $host_user->ID() != $userid)
{
  header('Location: /adminresident.php');
  exit();
}

$host_user->LogIn();
$raw_host_user = $host_user->RawData();

setcookie(
	$SETTINGS['cookie_name'],
	$raw_host_user['idnum'] . ';' . $raw_host_user['sessionid'],
	time() + $raw_host_user['login_persist'],
	$SETTINGS['cookie_path'],
	$SETTINGS['cookie_domain']
);

header('Location: /');
