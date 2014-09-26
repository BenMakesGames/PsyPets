<?php
$child_safe = false;

if($_GET['resident'] == $SETTINGS['site_ingame_mailer'])
{
  header('Location: ./cityhall.php');
  exit();
}

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/formatting.php';
require_once 'commons/sessions.php';
require_once 'commons/userlib.php';
require_once 'commons/badges.php';

$profile_user = get_user_bydisplay($_GET['resident']);

if($profile_user === false)
{
  header('Location: ./directory.php');
  exit();
}

if($profile_user['is_npc'] == 'yes')
{
  header('Location: ./npcprofile.php?npc=' . link_safe($profile_user['display']));
  exit();
}

if($profile_user['childlockout'] == 'yes' || $profile_user['activated'] != 'yes' || $profile_user['disabled'] != 'no')
{
  header('Location: ./directory.php');
  exit();
}

$badges = get_badges_byuserid($profile_user['idnum']);

if($badges['worstideaever'] == 'no' && $user['idnum'] != $profile_user['idnum'])
{
  // if the browsing account is older than the profile's account by one year, or the browsing account was created
  // before July 4th 2004 (3 months after PsyPets' creation), then the browsing account may give the profile's
  // account the 'worstideaever' badge.
  if($user['signupdate'] <= $profile_user['signupdate'] - 365 * 24 * 60 * 60 || $user['signupdate'] <= 1088930100)
    $give_worst_idea_ever_badge = 'yes';
  else
    $give_worst_idea_ever_badge = 'no';
}
else
  $give_worst_idea_ever_badge = false;

if($give_worst_idea_ever_badge == 'yes')
{
  set_badge($profile_user['idnum'], 'worstideaever');
  psymail_user($profile_user['user'], $SETTINGS['site_ingame_mailer'], $user['display'] . ' loved on you!', 'You received the Someone Liiiiikes Yooouuu Badge!');
}

header('Location: ./residentprofile.php?resident=' . link_safe($profile_user['display']));
?>
