<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";
require_once "commons/userlib.php";
require_once 'commons/threadfunc.php';

$postid = (int)$_GET['postid'];

$command = 'SELECT threadid,createdby,troll_flag FROM monster_posts WHERE idnum=' . $postid . ' LIMIT 1';
$this_post = $database->FetchSingle($command, 'fetching post');

if($this_post['troll_flag'] == 'yes')
{
  if($user['admin']['alphalevel'] >= 6)
  {
    $command = 'UPDATE monster_posts SET troll_flag=\'no\',locked=\'no\' WHERE idnum=' . $postid . ' LIMIT 1';
    $database->FetchNone(($command, 'unmarking post as containing trolls');

    $poster = get_user_byid($this_post['createdby'], 'user');

    psymail_user($poster['user'], $SETTINGS['site_ingame_mailer'], 'Oops!  Your post was accidentally marked as containing trolls!', '<a href="jumptopost.php?postid=' . $postid . '">One of your posts</a> was previously marked as containing trolls, but {r ' . $user['display'] . ' believes this was done in error, and has un-marked the post.  Sorry about that!');

    if($plazainfo['groupid'] == 0) 
      psymail_user('telkoth', $SETTINGS['site_ingame_mailer'], 'post unmarked as troll', '{r ' . $user['display'] . '} unmarked the following post as containing trolls: <a href="jumptopost.php?postid=' . $postid . '">post #' . $postid . '</a>');
  }
}

header('Location: ./jumptopost.php?postid=' . $postid);
?>
