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
require_once 'commons/questlib.php';

$command = 'SELECT COUNT(postid) AS c FROM psypets_starlog WHERE userid=' . $user['idnum'];
$data = $database->FetchSingle($command, 'fetching count of stars given');

$num_stars = (int)$data['c'];

if($num_stars > 0)
{
  $num_pages = ceil($num_stars / 20);

  $page = (int)$_GET['page'];
  if($page < 1 || $page > $num_pages)
    $page = 1;

  $command = 'SELECT postid,stars FROM psypets_starlog WHERE userid=' . $user['idnum'] . ' ORDER BY stars DESC LIMIT ' . (($page - 1) * 20) . ',20';
  $my_stars = $database->FetchMultipleBy($command, 'postid', 'specialposts.php');

  $page_list = paginate($num_pages, $page, 'specialpostsmade.php?page=%s');
}

$stars_given_quest = get_quest_value($user['idnum'], 'goldstarsgiven');
$stars_given = (int)$stars_given_quest['value'];

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Plaza Forums &gt; Stars I've Given</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/plaza.php">Plaza Forums</a> &gt; Stars I've Given</h4>
     <ul class="tabbed">
      <li><a href="/threadsubscriptions.php">Thread Subscriptions</a></li>
      <li class="activetab"><a href="/specialpostsmade.php">Stars I've Given</a></li>
      <li><a href="/specialposts.php?resident=<?= link_safe($user['display']) ?>">Stars I've Received</a></li>
     </ul>
<?php
if($num_stars == 0)
  echo '     <p>You haven\'t starred any posts.</p>' .
       '     <p>To star a post, you will need <a href="/encyclopedia2.php?item=Gold%20Star%20Stickers">Gold Star Stickers</a>.</p>';
else
{
?>
     <p>You have given a total of <?= $stars_given ?> Gold Star Sticker<?= $stars_given != 1 ? 's' : '' ?>.</p>
     <p>These records will remain even if the poster removes the stars from their post.</p>
     <?= $page_list ?>
     <table>
      <tr class="titlerow">
       <th><img src="/gfx/goldstar.png" height="16" width="16" alt="Gold Stars" /></th>
       <th>Post&nbsp;Title</th>
       <th>Thread&nbsp;Title</th>
       <th>Posted&nbsp;By</th>
      </tr>
<?php
  $cellstyle = begin_row_class();

  foreach($my_stars as $starred_post)
  {
    $post = $database->FetchSingle('SELECT threadid,title,createdby FROM monster_posts WHERE idnum=' . $starred_post['postid'] . ' LIMIT 1');

    $thread = get_thread_byidnum($post['threadid']);
    $poster = get_user_byid($post['createdby'], 'display');

    if($post['title'] == '')
      $post['title'] = '[untitled]';

    $title_link = '<a href="/jumptopost.php?postid=' . $starred_post['postid'] . '">' . $post['title'] . '</a>';
    
    if($poster === false)
      $poster_display = '<i class="dim">[departed #' . $post['createdby'] . ']</i>';
    else
      $poster_display = resident_link($poster['display']);
?>
      <tr class="<?= $cellstyle ?>">
       <td class="centered"><?= $starred_post['stars'] ?></td>
       <td><?= $title_link ?></td>
       <td><a href="/viewthread.php?threadid=<?= $thread['idnum'] ?>"><?= $thread['title'] ?></a></td>
       <td><?= $poster_display ?></td>
      </tr>
<?php

    $cellstyle = alt_row_class($cellstyle);
  }
?>
     </table>
     <?= $page_list ?>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
