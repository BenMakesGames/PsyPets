<?php
$require_petload = 'no';

if($_GET['resident'] == $SETTINGS['site_ingame_mailer'])
{
  header('Location: /cityhall.php');
  exit();
}

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/formatting.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/userlib.php';
require_once 'commons/threadfunc.php';

$profile_user = get_user_bydisplay($_GET['resident']);

if($profile_user['activated'] != 'yes' || $profile_user['disabled'] != 'no')
{
  header('Location: /directory.php');
  exit();
}

if($profile_user['idnum'] == $user['idnum'] && $user['newgoldstar'] == 'yes')
{
  $command = 'UPDATE monster_users SET newgoldstar=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'specialposts.php');
  $user['newgoldstar'] = 'no';

  $search_time = microtime(true);

  $command = 'SELECT postid FROM psypets_starlog WHERE authorid=' . $user['idnum'] . ' AND new=\'yes\'';
  $new_posts = $database->FetchMultipleBy($command, 'postid', 'specialposts.php');
}
else
{
  $new_posts = array();
  $search_time = microtime(true);
}

$command = 'SELECT * FROM monster_posts WHERE createdby=' . $profile_user['idnum'] . ' AND goldstars>0 ORDER BY goldstars DESC,creationdate DESC';
$posts = $database->FetchMultipleBy($command, 'idnum', 'specialposts.php');

$search_time = microtime(true) - $search_time;

if(count($new_posts) > 0)
{
  $command = 'UPDATE psypets_starlog SET new=\'no\' WHERE authorid=' . $user['idnum'] . ' AND new=\'yes\' LIMIT ' . count($new_posts);
  $database->FetchNone($command, 'specialposts.php');
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Plaza Forums &gt; Top Posts by <?= $profile_user['display'] ?></title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/plaza.php">Plaza Forums</a> &gt; Top Posts by <?= $profile_user["display"] ?></h4>
<?php
if($profile_user['idnum'] == $user['idnum'])
{
?>
     <ul class="tabbed">
      <li><a href="/threadsubscriptions.php">Thread Subscriptions</a></li>
      <li><a href="/specialpostsmade.php">Stars I've Given</a></li>
      <li class="activetab"><a href="/specialposts.php?resident=<?= link_safe($user['display']) ?>">Stars I've Received</a></li>
     </ul>
<?php
}

if(count($posts) == 0)
{
  if($profile_user['idnum'] == $user['idnum'])
    echo '     <p>None of your posts have been given a gold star sticker.</p>';
  else
    echo '     <p>None of this resident\'s posts have been given a gold star sticker.</p>';
}
else
{
?>
     <p><?= ($profile_user['idnum'] == $user['idnum']) ? 'You have' : ($profile_user['display'] . ' has') ?> been given a total of <?= $profile_user['stickers_given'] ?> Gold Star Sticker<?= $profile_user['stickers_given'] != 1 ? 's' : '' ?>.</p>
     <p>Click on a post title to jump to that post.  Click on a thread title to visit the first page of the thread.</p>
<?php
  if(count($new_posts) > 0)
  {
?>
     <h5>Newly Starred Posts</h5>
     <table>
      <tr class="titlerow">
       <th class="centered"><img src="/gfx/goldstar.png" height="16" width="16" alt="Gold Stars" /></th>
       <th>Post&nbsp;Title</th>
       <th>Thread&nbsp;Title</th>
       <th>Posted</th>
      </tr>
<?php
    $bgcolor = begin_row_class();

    foreach($new_posts as $post_id)
    {
      $post = $posts[$post_id['postid']];
      $thread = get_thread_byidnum($post['threadid']);

      if($post['title'] == '')
        $post['title'] = '[untitled]';

      $title_link = '<a href="/jumptopost.php?postid=' . $post['idnum'] . '">' . $post['title'] . '</a>';
?>
      <tr class="<?= $bgcolor ?>">
       <td class="centered"><?= $post['goldstars'] ?></td>
       <td><?= $title_link ?></td>
       <td><a href="/viewthread.php?threadid=<?= $thread['idnum'] ?>"><?= $thread['title'] ?></a></td>
       <td><nobr><?= duration($now - $post['creationdate']) ?> ago</nobr></td>
      </tr>
<?php

      $bgcolor = alt_row_class($bgcolor);
    }
?>
     </table>     
     <h5>All Starred Posts</h5>
<?php
  }
?>
     <table>
      <tr class="titlerow">
       <th class="centered"><img src="/gfx/goldstar.png" height="16" width="16" alt="Gold Stars" /></th>
       <th>Post&nbsp;Title</th>
       <th>Thread&nbsp;Title</th>
       <th>Posted</th>
      </tr>
<?php
  $bgcolor = begin_row_class();

  foreach($posts as $post)
  {
    $thread = get_thread_byidnum($post['threadid']);

    if($post['title'] == '')
      $post['title'] = '[untitled]';

    $title_link = '<a href="/jumptopost.php?postid=' . $post['idnum'] . '">' . $post['title'] . '</a>';
?>
      <tr class="<?= $bgcolor ?>">
       <td class="centered"><?= $post['goldstars'] ?></td>
       <td><?= $title_link ?></td>
       <td><a href="/viewthread.php?threadid=<?= $thread['idnum'] ?>"><?= $thread['title'] ?></a></td>
       <td><nobr><?= duration($now - $post['creationdate']) ?> ago</nobr></td>
      </tr>
<?php

    $bgcolor = alt_row_class($bgcolor);
  }
?>
     </table>
<?php
}

$footer_note = '<br />Took ' . round($search_time, 4) . 's fetching posts.';

include 'commons/footer_2.php';
?>
 </body>
</html>
