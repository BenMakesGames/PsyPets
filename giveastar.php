<?php
$require_petload = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

$postid = (int)$_GET['postid'];

$command = "SELECT createdby,threadid FROM monster_posts WHERE idnum=$postid LIMIT 1";
$post = $database->FetchSingle($command, 'giveastar.php');

if($post === false)
{
  Header("Location: ./plaza.php");
  exit();
}

if($post['createdby'] != $user['idnum'] && $user['stickers_to_give'] > 0)
{
  $command = "UPDATE monster_posts SET goldstars=goldstars+1 WHERE idnum=$postid LIMIT 1";
  $database->FetchNone($command, 'giveastar.php');

  $command = 'UPDATE monster_users SET stickers_to_give=stickers_to_give-1 WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'giveastar.php');

  $command = "UPDATE monster_users SET stickers_given=stickers_given+1,newgoldstar='yes' WHERE idnum=" . $post["createdby"] . " LIMIT 1";
  $database->FetchNone($command, 'giveastar.php');

  $command = 'SELECT * FROM psypets_starlog WHERE userid=' . $user['idnum'] . ' AND postid=' . $postid . ' LIMIT 1';
  $stars = $database->FetchSingle($command, 'giveastar.php');
  
  if($stars === false)
    $command = 'INSERT INTO psypets_starlog (userid, postid, authorid, stars) VALUES ' .
               '(' . $user['idnum'] . ', ' . $postid . ', ' . $post['createdby'] . ', 1)';
  else
    $command = 'UPDATE psypets_starlog SET stars=stars+1,new=\'yes\' WHERE userid=' . $user['idnum'] . ' AND postid=' . $postid . ' LIMIT 1';

  $database->FetchNone($command, 'giveastar.php');

  require_once 'commons/questlib.php';

  $goldstars = get_quest_value($user['idnum'], 'goldstarsgiven');
  $goldstar_count = (int)$goldstars['value'] + 1;

  if($goldstars === false)
    add_quest_value($user['idnum'], 'goldstarsgiven', $goldstar_count);
  else
    update_quest_value($goldstars['idnum'], $goldstar_count);

  $badges = get_badges_byuserid($user['idnum']);
  if($badges['goldstar'] == 'no' && $goldstar_count >= 10)
  {
    set_badge($user['idnum'], 'goldstar');

    $body = 'You\'ve just now given out your 10th Gold Star Sticker.  Thanks for participating in Plaza discussion!<br /><br />' .
            '{i}(You earned the Sticker Sticker Badge!){/}';

    psymail_user($user['user'], 'csilloway', 'You\'ve given out 10 Gold Stars!', $body);
  }

  if(date('n j') == '5 5' && mt_rand(1, 3) == 1)
  {
    require_once 'commons/itemlib.php';
  
    $target = get_user_byid($post['createdby'], 'user');
    add_inventory($target['user'], '', 'Strawberry Margarita', 'Cinco de Mayo - another excuse to drink alcohol', 'storage/incoming');
    flag_new_incoming_items($target['user']);
  }
}

header('Location: ./jumptopost.php?postid=' . $postid);
?>
