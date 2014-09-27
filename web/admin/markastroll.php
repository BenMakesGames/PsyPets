<?php
require_once 'commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/threadfunc.php';
require_once 'commons/trolllib.php';

if($user['admin']['alphalevel'] >= 6)
{
  $postid = (int)$_GET['postid'];

  $command = 'SELECT threadid,creationdate,createdby,troll_flag FROM monster_posts WHERE idnum=' . $postid . ' LIMIT 1';
  $this_post = $database->FetchSingle($command, 'fetching post');

  if($this_post['troll_flag'] == 'no')
  {
    $this_thread = get_thread_byid($this_post['threadid']);

    $command = 'SELECT * FROM monster_plaza WHERE idnum=' . $this_thread['plaza'] . ' LIMIT 1';
    $plazainfo = $database->FetchSingle($command, 'viewthread.php?idnum=' . $threadid);

    $watcher_list = explode(',', $plazainfo['admins']);
    $is_watcher = in_array($user['idnum'], $watcher_list);

    $command = 'UPDATE monster_posts SET troll_flag=\'yes\',locked=\'yes\' WHERE idnum=' . $postid . ' LIMIT 1';
    $database->FetchNone(($command, 'marking post as containing trolls');

    $poster = get_user_byid($this_post['createdby'], 'user,display');

    if($this_post['creationdate'] >= $now - (5 * 24 * 60 * 60))
    {
      psymail_user(
        $poster['user'],
        $SETTINGS['site_ingame_mailer'],
        'One of your posts was marked as trolling/flaming.',
          '<a href="/jumptopost.php?postid=' . $postid . '">One of your posts</a> was marked as trolling, flaming, or maybe both.<br /><br />' .
          'It can be easy to get caught up in a fight that\'s already started, and we all have our \'bad days\', but to keep the forums a place where everyone can feel welcome, we must be responsible for our actions.  Please try to be conscientious of how others might read your post, and when in doubt, {i}don\'t{/} post - take a minute to cool off, and come back later.<br /><br />' .
          'Remember the <a href="/termsofservice.php">Terms of Service</a>, and if you ever have questions, feel free to <a href="/admincontact.php">contact me</a>!'
      );
      $subject_line = 'post marked for trolls';
    }
    else
      $subject_line = 'post marked for trolls (silently)';

    $text = $poster['display'] . ' (' . $poster['user'] . ') ' . $this_post['title'] . ' ' . $this_post['body'];
    train_as_trolling($text);

    psymail_user('telkoth', $SETTINGS['site_ingame_mailer'], $subject_line, '{r ' . $user['display'] . '} marked the following post as containing trolls: <a href="jumptopost.php?postid=' . $postid . '">post #' . $postid . '</a>');
  }
}

header('Location: /jumptopost.php?postid=' . $postid);
