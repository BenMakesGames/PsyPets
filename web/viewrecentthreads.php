<?php
$require_petload = 'no';

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";
require_once "commons/threadfunc.php";

if(array_key_exists('updatetrack', $_GET))
{
  if($_POST['threadtrack'] >= 1 && $_POST['threadtrack'] <= 24 && (int)$_POST['threadtrack'] == $_POST['threadtrack'])
  {
    $user['threadtrack'] = $_POST['threadtrack'];
    $command = 'UPDATE monster_users SET threadtrack=' . (int)$_POST['threadtrack'] . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'viewrecentthreads.php');
  }
}

$command = 'SELECT * FROM monster_watching ' .
           'WHERE user=' . quote_smart($user['user']) . ' AND threadid>0 AND lastread>' . ($now - ($user['threadtrack'] * 60 * 60)) . ' ORDER BY lastread DESC LIMIT 20';
$watched_threads = $database->FetchMultiple($command, 'threadsubscriptions.php');

$command = 'SELECT a.*,b.lastread FROM psypets_watchedthreads AS a,monster_watching AS b ' .
           'WHERE a.userid=' . $user['idnum'] . ' AND b.user=' . quote_smart($user['user']) . ' AND a.threadid=b.threadid ORDER BY b.lastread DESC';
$thread_subscriptions = $database->FetchMultiple($command, 'threadsubscriptions.php');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Plaza Forums &gt; Recently Visited Threads</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="plaza.php">Plaza Forums</a> &gt; Thread Subscriptions</h4>
     <h5>Recently Visited Threads</h5>
     <p>You may change how many hours back to find threads, from 1 to 24 hours.  Only the most recent 20 are shown.</p>
     <form action="viewrecentthreads.php?updatetrack" method="POST">
     <table border=0 cellspacing=0 cellpadding=4>
      <tr>
       <td>Recency:</td>
       <td><input name="threadtrack" value="<?= $user['threadtrack'] ?>" size="2" maxlength="2" /> hours</td>
      </tr>
      <tr>
       <td></td>
       <td><input type="submit" value="Update" /></td>
      </tr>
     </table>
     </form>
<?php
if(count($watched_thread) > 0)
{
?>
     <table border=0 cellspacing=0 cellpadding=4>
      <tr class="titlerow">
       <th>Thread Title</th>
       <th>Replies</th>
       <th>Views</th>
       <th>Last Updated</th>
       <th>Last Visited</th>
      </tr>
<?php
  $bgcolor = begin_row_class();

  foreach($watched_threads as $watched_thread)
  {
    $thread_info = get_thread_byid($watched_thread['threadid']);
    if($watched_thread['lastread'] < $thread_info['updatedate'])
    {
      $opentag = '<p><b>';
      $closetag = '</b></p>';
    }
    else
    {
      $opentag = '<p>';
      $closetag = '</p>';
    }
?>
      <tr class="<?= $bgcolor ?>">
       <td><?= $opentag ?><a href="viewthread.php?threadid=<?= $watched_thread['threadid'] ?>"><?= $thread_info['title'] ?></a><?= $closetag ?></td>
       <td align="center"><?= $opentag ?><?= $thread_info['replies'] ?><?= $closetag ?></td>
       <td align="center"><?= $opentag ?><?= $thread_info['views'] ?><?= $closetag ?></td>
       <td><?= $opentag ?><?= local_time($thread_info['updatedate'], $user['timezone'], $user['daylightsavings']) ?><?= $closetag ?></td>
       <td><?= $opentag ?><?= local_time($watched_thread['lastread'], $user['timezone'], $user['daylightsavings']) ?><?= $closetag ?></td>
      </tr>
<?php
    $bgcolor = alt_row_class($bgcolor);
  }
?>
     </table>
<?php
}
else
{
?>
     <p><i>No such threads exist.</i></p>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
