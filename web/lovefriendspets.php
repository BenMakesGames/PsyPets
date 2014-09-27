<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/petlib.php';
require_once 'commons/formatting.php';

$msgs = array();

$profile_user = get_user_bydisplay(urldecode($_POST['resident']));

if($profile_user === false)
{
  if($_POST['ajax'] == 'yes')
    die('Could not find a resident by that name.');
  else
  {
    header('Location: ./directory.php');
    exit();
  }
}

if($profile_user['is_npc'] == 'yes')
{
  if($_POST['ajax'] == 'yes')
    die('That\'s very nice of you, but NPC pets do not need to be pet.  (They never pass the time.  Now you know their terrible secret.');
  else
  {
    header('Location: ./npcprofile.php?npc=' . link_safe($profile_user['display']));
    exit();
  }
}

if($profile_user['childlockout'] == 'yes' || $profile_user['activated'] != 'yes' || $profile_user['disabled'] != 'no')
{
  if($_POST['ajax'] == 'yes')
    die('Could not find a resident by that name.');
  else
  {
    header('Location: ./directory.php');
    exit();
  }
}

$pets = get_pets_byuser($profile_user['user'], 'home');
$love_actions = 0;

foreach($pets as $pet)
{
  if(!array_key_exists('love' . $pet['idnum'], $_POST))
    continue;

  if(
    $pet['dead'] == 'no' && $pet['zombie'] == 'no' && $pet['changed'] == 'no' && $pet['sleeping'] == 'no' &&
    (
      $pet['last_love'] < $now - 60 * 60 ||
      ($pet['last_love'] < $now - 30 * 60 && $pet['last_love_by'] != $profile_user['idnum'])
    )
  )
  {
    gain_safety($pet, 1, true);
    gain_love($pet, 2, true);

    $pet['last_love'] = $now;
    $pet['last_love_by'] = $user['idnum'];

    save_pet($pet, array('love', 'safety', 'last_love', 'last_love_by'));

    add_logged_event(
      $profile_user['idnum'],
      $pet['idnum'],
      0,
      'realtime',
      false,
      '<a href="residentprofile.php?resident=' . link_safe($user['display']) . '">' . $user['display'] . '</a> pet ' . $pet['petname'] . '.',
      array('safety' => 1, 'love' => 2)
    );

    $love_actions++;
    $love_petname = $pet['petname'];
  }
  else
    $msgs[] = 29;
}

if($_POST['ajax'] == 'yes')
{
  if($love_actions > 1)
    die('<p class="success">' . $love_actions . ' pets were the happy recipients of your attention.</p>');
  else if($love_actions == 0)
    die('<p class="failure">No pets were pet.</p>');
  else
    die('<p class="success">' . $love_petname . ' was the happy recipient of your attention.</p>');
}
else
{
  if($love_actions == 1)
    $msgs[] = '116:' . $love_petname;
  else
    $msgs[] = '117:' . $love_actions;

  header('Location: ./residentprofile.php?resident=' . link_safe($profile_user['display']) . '&msg=' . link_safe(implode(',', $msgs)) . '#petlist');
}
?>
