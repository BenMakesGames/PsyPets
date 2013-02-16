<?php
$require_petload = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/fireworklib.php';
require_once 'commons/threadfunc.php';

if($user['fireworks'] == '')
{
  header('Location: ./residentprofile.php?resident=' . link_safe($user['display']));
  exit();
}

$fireworkid = (int)$_GET['firework'];

$supply = get_firework_supply($user);

$imgurl = 'postwalls/' . $POST_BACKGROUNDS[$fireworkid] . '.png';
$PAGE['user']['profile_wall_repeat'] = 'yes';

if(array_key_exists($fireworkid, $supply) && $user['profile_wall'] != $imgurl)
{
  expend_firework($supply, $fireworkid);

  $command = 'UPDATE monster_users SET fireworks=' . quote_smart(render_firework_data_string($supply)) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'removing firework from player');

  $command = 'UPDATE monster_users SET profile_wall=' . quote_smart($imgurl) . ',profile_wall_repeat=\'yes\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'setting profile background');
}

header('Location: ./residentprofile.php?resident=' . link_safe($user['display']));
?>
