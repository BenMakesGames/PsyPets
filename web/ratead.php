<?php
$nevercache = true;
$require_petload = 'no';
$child_safe = false;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/userlib.php';
require_once 'commons/ip.php';

$ad_id = (int)$_GET['ad'];
$option = (int)$_GET['option'];

$command = 'SELECT * FROM psypets_advertising WHERE idnum=' . $ad_id . ' LIMIT 1';
$ad = $database->FetchSingle($command, 'fetching ad');

if($ad !== false && $ad['permanent'] == 'no' && $option >= 1 && $option <= 4)
{
  $voters = take_apart(',', $ad['voters']);
  $my_ip = getip();

  if(!in_array($my_ip, $voters))
  {
    $voters[] = $my_ip;
    $vote = $ad['vote'];

    if($option == 1)
    {
      $vote_delta = ',vote=vote+2';
      $vote += 2;
    }
    else if($option == 2)
    {
      $vote_delta = ',vote=vote+1';
      $vote += 1;
    }
    else if($option == 4)
    {
      $vote -= 2;
      $vote_delta = ',vote=vote-2';
    }
    else
      $vote_delta = '';

    if(count($voters) >= 15 && $vote <= -15)
    {
      $command = 'DELETE FROM psypets_advertising WHERE idnum=' . $ad_id . ' LIMIT 1';
      $database->FetchNone($command, 'updating ad rating');
      
      $poster = get_user_byid($ad['userid'], 'user,display');
      
      psymail_user($poster['user'], $SETTINGS['site_ingame_mailer'], 'Your ad was removed!', 'The PsyPets community found the following ad to be inappropriate.  The ad has been canceled.  No refund will be issued.{hr}' . $ad['ad'] . '{hr}If you believe your ad was unjustly removed, please contact <a href="admincontact.php">an administrator</a>.');
      psymail_user($SETTINGS['author_login_name'], $SETTINGS['site_ingame_mailer'], $poster['display'] . '\'s ad was removed!', 'The PsyPets community found the following ad to be inappropriate.  The ad has been canceled.{hr}' . $ad['ad']);
    }
    else
    {
      $command = 'UPDATE psypets_advertising SET voters=' . quote_smart(implode(',', $voters)) . $vote_delta . ' WHERE idnum='. $ad_id . ' LIMIT 1';
      $database->FetchNone($command, 'updating ad rating');
    }
  }
}

if($_POST['ajax'] != 'yes')
  header('Location: ./park.php');
