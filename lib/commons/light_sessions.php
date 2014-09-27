<?php
// start important things
require_once 'commons/settings_light.php';

session_set_cookie_params(0, $SETTINGS['cookie_path'], $SETTINGS['cookie_domain']);
session_name('monstersession');
session_start();

$sessuser = $_SESSION['session_user'];
$sesssess = $_SESSION['session_sessionid'];

header("Cache-control: private");

$user = $database->FetchSingle('SELECT * FROM `monster_users` WHERE idnum=' . $database->Quote($sessuser) . ' LIMIT 1');

$now = time();

if($user === false || $user['sessionid'] != $sesssess)
{
  echo "<p>You must be logged in.</p>";
  exit();
}
