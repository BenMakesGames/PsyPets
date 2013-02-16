<?php
$require_petload = 'no';
$child_safe = false;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/threadfunc.php';

$search_time = microtime(true);

$command = 'SELECT * FROM monster_watching ' .
           'WHERE user=' . quote_smart($user['user']) . ' AND threadid>0 ORDER BY lastread DESC LIMIT 20';
$watched_threads = $database->FetchMultiple($command, 'threadsubscriptions.php');

$search_time = microtime(true) - $search_time;

$subscription_time = microtime(true);

$command = 'SELECT a.*,b.lastread FROM psypets_watchedthreads AS a LEFT JOIN monster_watching AS b ' .
           'ON a.threadid=b.threadid AND b.user=' . quote_smart($user['user']) . ' WHERE a.userid=' . $user['idnum'] . ' ORDER BY b.lastread DESC';
$thread_subscriptions = $database->FetchMultiple($command, 'threadsubscriptions.php');

$subscription_time = microtime(true) - $subscription_time;

$footer_note = '<br />Took ' . round($search_time, 4) . 's fetching your recently-viewed threads, and ' . round($subscription_time, 4) . 's fetching your thread subscription.';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Plaza Forums &gt; Favorite Threads</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="plaza.php">Plaza Forums</a> &gt; Favorite Threads</h4>
     <ul class="tabbed">
      <li class="activetab"><a href="threadsubscriptions.php">Thread Subscriptions</a></li>
      <li><a href="/specialpostsmade.php">Stars I've Given</a></li>
      <li><a href="/specialposts.php?resident=<?= link_safe($user['display']) ?>">Stars I've Received</a></li>
     </ul>
<?php
if($message)
  echo '<p>' . $message . '</p>';

if(count($thread_subscriptions) > 0)
{
?>
     <form action="threadunsubscribe.php" method="post">
     <table>
      <tr class="titlerow">
       <th></th>
       <th></th>
       <th>Thread Title</th>
       <th>Replies</th>
       <th>Views</th>
       <th class="centered">Last Updated</th>
       <th class="centered">Last Visited</th>
      </tr>
<?php
  $bgcolor = begin_row_class();

  foreach($thread_subscriptions as $thread_subscription)
  {
    $actions = array();

    $thread_info = get_thread_byid($thread_subscription['threadid']);
    if($thread_subscription['lastread'] < $thread_info['updatedate'])
    {
      $opentag = '<b>';
      $closetag = '</b>';

      $actions[] = '<a href="/jumptolatestpost.php?threadid=' . $thread_subscription['threadid'] . '">first unread post</a>';
    }
    else
    {
      $opentag = '';
      $closetag = '';
    }

    if($thread_info['replies'] > 19)
      $actions[] = '<a href="/viewthread.php?threadid=' . $thread_subscription['threadid'] . '&page=' . ceil(($thread_info['replies'] + 1) / 20) . '">last page</a>';

    $flags = '';

    if($thread_info['highlight'] > 0)
      $flags .= '<img src="//' . $SETTINGS['static_domain'] . '/gfx/forum/' . $THREAD_HIGHLIGHTS[$thread_info['highlight']] . '" width="16" height="16" alt="" />';

    if($thread_info['locked'] == 'yes')
      $flags .= '<img src="gfx/lock.gif" width="16" height="16" alt="Locked" />';
    else if($thread_info['updatedate'] < ($now - 6 * 30 * 24 * 60 * 60) && $thread_info['sticky'] == 'no')
      $flags .= '<img src="gfx/lock_soft.png" width="16" height="16" alt="Locked (old)" />';
?>
      <tr class="<?= $bgcolor ?>">
       <td><input type="checkbox" name="s_<?= $thread_subscription['idnum'] ?>" /></td>
       <td><nobr><?= $flags ?></nobr></td>
       <td><?= $opentag ?><a href="viewthread.php?threadid=<?= $thread_subscription['threadid'] ?>"><?= $thread_info['title'] ?></a><?php
    if(count($actions) > 0)
      echo '<br /><span class="size8">[ ' . implode(', ', $actions ) . ' ]</span>';
?><?= $closetag ?></td>
       <td class="centered"><?= $opentag ?><?= $thread_info['replies'] ?><?= $closetag ?></td>
       <td class="centered"><?= $opentag ?><?= $thread_info['views'] ?><?= $closetag ?></td>
       <td class="centered"><nobr><?= $opentag ?><?= Duration($now - $thread_info['updatedate'], 2) ?> ago<?= $closetag ?></nobr></td>
       <td class="centered"><nobr><?= $opentag ?><?= Duration($now - $thread_subscription['lastread'], 2) ?> ago<?= $closetag ?></nobr></td>
      </tr>
<?php
    $bgcolor = alt_row_class($bgcolor);
  }
?>
     </table>
     <p><input type="submit" value="Unsubscribe" class="bigbutton" /></p>
     </form>
<?php
}
else
  echo '<p>You have not subscribed to any threads!  To subscribe to a thread, visit that thread and click the "Subscribe" link.</p>';
?>
     <h5>Your 20 Recently-Visited Threads</h5>
<?php
if(count($watched_threads) > 0)
{
?>
     <table>
      <tr class="titlerow">
       <th></th>
       <th>Thread Title</th>
       <th>Replies</th>
       <th>Views</th>
       <th class="centered">Last Updated</th>
       <th class="centered">Last Visited</th>
      </tr>
<?php
  $bgcolor = begin_row_class();

  foreach($watched_threads as $watched_thread)
  {
    $actions = array();

    $thread_info = get_thread_byid($watched_thread['threadid']);
    if($watched_thread['lastread'] < $thread_info['updatedate'])
    {
      $opentag = '<b>';
      $closetag = '</b>';
      
      $actions[] = '<a href="/jumptolatestpost.php?threadid=' . $watched_thread['threadid'] . '">first unread post</a>';
    }
    else
    {
      $opentag = '';
      $closetag = '';
    }

    if($thread_info['replies'] > 19)
      $actions[] = '<a href="/viewthread.php?threadid=' . $watched_thread['threadid'] . '&page=' . floor($thread_info['replies'] / 20 + 1) . '">last page</a>';

    $flags = '';

    if($thread_info['highlight'] > 0)
      $flags .= '<img src="//' . $SETTINGS['static_domain'] . '/gfx/forum/' . $THREAD_HIGHLIGHTS[$thread_info['highlight']] . '" width="16" height="16" alt="" />';

    if($thread_info['locked'] == 'yes')
      $flags .= '<img src="gfx/lock.gif" width="16" height="16" alt="Locked" />';
    else if($thread_info['updatedate'] < ($now - 6 * 30 * 24 * 60 * 60) && $thread_info['sticky'] == 'no')
      $flags .= '<img src="gfx/lock_soft.png" width="16" height="16" alt="Locked (old)" />';
?>
      <tr class="<?= $bgcolor ?>">
       <td><?= $flags ?></td>
       <td><?= $opentag ?><a href="/viewthread.php?threadid=<?= $watched_thread['threadid'] ?>"><?= $thread_info['title'] ?></a><?php
    if(count($actions) > 0)
      echo '<br /><span class="size8">[ ' . implode(', ', $actions ) . ' ]</span>';
?><?= $closetag ?></td>
       <td class="centered"><?= $opentag ?><?= $thread_info['replies'] ?><?= $closetag ?></td>
       <td class="centered"><?= $opentag ?><?= $thread_info['views'] ?><?= $closetag ?></td>
       <td class="centered"><nobr><?= $opentag ?><?= Duration($now - $thread_info['updatedate'], 2) ?> ago<?= $closetag ?></nobr></td>
       <td class="centered"><nobr><?= $opentag ?><?= Duration($now - $watched_thread['lastread'], 2) ?> ago<?= $closetag ?></nobr></td>
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
