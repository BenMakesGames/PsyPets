<?php
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/featuredrivelib.php';

$min_time = mktime(0, 0, 0, 2, 1, 2011);
$max_time = mktime(0, 0, 0, 3, 1, 2011);

$can_vote = user_can_vote($user['idnum'], $min_time, $max_time);

if(!$can_vote)
{
  header('Location: ./featuredrive.php');
  exit();
}

$vote = get_voter_feature_drive_suggestion($user['idnum']);
$suggestions = get_feature_drive_suggestions();

$new_vote = trim(urldecode($_GET['suggestion']));

if($new_vote == '')
{
  header('Location: ./featuredrive.php?msg=145');
  exit();
}
else if(strlen($new_vote) > 20)
{
  header('Location: ./featuredrive.php?msg=146');
  exit();
}
else if($vote['vote'] == $new_vote)
{
  header('Location: ./featuredrive.php');
  exit();
}
else if($vote === false)
{
  make_feature_drive_suggestion($new_vote, $user['idnum']);

  header('Location: ./featuredrive.php');
  exit();
}
else
{
  delete_feature_drive_suggestion($user['idnum']);
  make_feature_drive_suggestion($new_vote, $user['idnum']);

  header('Location: ./featuredrive.php');
  exit();
}

