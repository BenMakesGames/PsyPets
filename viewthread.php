<?php
$require_petload = 'no';
$child_safe = false;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/psypetsformatting.php';
require_once 'commons/messages.php';
require_once 'commons/threadfunc.php';
require_once 'commons/badges.php';
require_once 'commons/plazapostvoting.php';
/*
if($user['idnum'] != 1)
{
  header('Location: ./plazaupdate.php');
  exit();
}
*/
$threadid = (int)$_GET['threadid'];

$this_thread = $database->FetchSingle('SELECT * FROM monster_threads WHERE idnum=' . $threadid . ' LIMIT 1');

if($this_thread === false)
{
  header('Location: /plaza.php');
  exit();
}

$plazainfo = $database->FetchSingle('SELECT * FROM monster_plaza WHERE idnum=' . $this_thread['plaza'] . ' LIMIT 1');

$watcher_list = explode(',', $plazainfo['admins']);
$is_watcher = in_array($user['idnum'], $watcher_list);

$data = $database->FetchSingle('
  SELECT COUNT(idnum) AS c
  FROM monster_posts
  WHERE threadid=' . $threadid . '
');
$post_count = (int)$data['c'];

if($post_count == 0)
{
  echo 'this thread has no posts?  odd... should notify an admin... >_>';
  exit();
}

$last_id = $this_thread['updateby'];
 
// figure out where to start from

$num_posts = 20;
$num_pages = floor(($post_count - 1) / $num_posts) + 1;

$start_page = (int)$_GET['page'];

if($start_page < 1 || $start_page > $num_pages)
  $start_page = 1;

$start_post = ($start_page - 1) * 20;

$command = 'SELECT * FROM monster_posts ' .
           'WHERE threadid=' . $threadid . ' ORDER BY idnum ASC ' .
           'LIMIT ' . $start_post . ',' . $num_posts;
$thread_posts = $database->FetchMultiple($command, 'fetching thread posts');

// ---

$command = 'UPDATE monster_threads SET views=views+1 ' .
           'WHERE idnum=' . $threadid . ' LIMIT 1';
$database->FetchNone($command, 'viewthread.php?idnum=' . $threadid);

// set the read status of this thread
$thread_watch = $database->FetchSingle('
  SELECT *
  FROM monster_watching
  WHERE user=' . quote_smart($user['user']) . '
  AND threadid=' . $threadid . ' LIMIT 1
');

if($thread_watch === false)
{
  $thread_watch = array();
  $thread_watch['user'] = $user['user'];
  $thread_watch['threadid'] = $threadid;
  $thread_watch['lastread'] = time();
  $thread_watch['reported'] = 'no';
  $thread_watch['voted'] = 'no';

  $command = 'INSERT INTO `monster_watching` ' .
             '(`user`, `threadid`, `lastread`) ' .
             'VALUES ' .
             '(' . quote_smart($user['user']) . ', ' . $threadid . ', ' . $thread_watch['lastread'] . ')';
  $database->FetchNone($command, 'viewthread.php?idnum=' . $threadid);
}
else
{
  $database->FetchNone('
    UPDATE monster_watching
    SET `lastread`=' . $now . '
    WHERE `user`=' . quote_smart($user['user']) . '
    AND threadid=' . $threadid . ' LIMIT 1
  ');
}

$a_member = (array_search($plazainfo['groupid'], take_apart(',', $user['groups'])) !== false);

$command = 'SELECT * FROM psypets_watchedthreads WHERE userid=' . $user['idnum'] . ' AND threadid=' . $threadid . ' LIMIT 1';
$thread_subscription = $database->FetchSingle($command, 'viewthread.php?threadid=' . $threadid);

$database->FetchNone('UPDATE monster_users SET daily_threadviews=daily_threadviews+1 WHERE idnum=' . $user['idnum'] . ' LIMIT 1');

$CONTENT_STYLE .= ' overflow: visible;';

if($user['fireworks'] != '')
{
  $firework_string = '<div><p>How will you decorate this post by <b>%resident%</b>?</p><table>';

  $fireworks = explode(',', $user['fireworks']);

  foreach($fireworks as $firework)
  {
    list($fireworkid, $quantity) = explode(':', $firework);
    
    $firework_string .= '<tr style="border-top: 1px solid #000;"><td style="background-image: url(gfx/postwalls/' . $POST_BACKGROUNDS[$fireworkid] . '.png); text-align: center;"><img src="gfx/shim.png" width="260" height="50" alt="" /><p><a href="givepostbackground.php?postid=%postid%&firework=' . $fireworkid . '">Like this!</a> (' . $quantity . ' available)</p><img src="gfx/shim.png" width="260" height="50" alt="" /></td>';
  }

  $firework_string .= '</table><center>[ <a href="#" onclick="firework_hide(); return false;">oops! nvm!</a> ]</center></div>';
}

if($this_thread['opening_post_id'] == 0)
{
  $command = 'SELECT idnum FROM monster_posts WHERE threadid=' . $threadid . ' ORDER BY idnum ASC LIMIT 1';
  $opening_post = $database->FetchSingle($command, 'fetching first post');
  
  $database->FetchNone('UPDATE monster_threads SET opening_post_id=' . $opening_post['idnum'] . ' WHERE idnum=' . $threadid . ' LIMIT 1');
}

include 'commons/html.php';
?>
 <head>
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; Plaza Forums &gt; <?= $plazainfo['title'] ?> &gt; <?= $this_thread['title'] ?></title>
  <link rel="stylesheet" href="//<?= $SETTINGS['static_domain'] ?>/css/plaza.css" />
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/scrolldetect.js"></script>
  <script type="text/javascript">
   var firework_string = '<?= $firework_string ?>';
   var toggled = new Array();
   
   function reveal_flames(id)
   {
     $('#warn' + id).hide();
     $('#troll' + id).slideDown();
   }

   function togglefireworks(id)
   {
     if(toggled[id] == undefined)
       toggled[id] = 'none';

     var temp = $('#p' + id).css('backgroundImage');
     $('#p' + id).css('backgroundImage', toggled[id]);
     toggled[id] = temp;
   }
  </script>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/thread3.js"></script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
    <div class="shadowed-box" id="kitchen" style="display: none;"></div>
    <table class="nomargin"><tr><td>
     <h4><a href="/plaza.php">Plaza Forums</a> &gt; <a href="/viewplaza.php?plaza=<?= $plazainfo['idnum'] ?>"><?= $plazainfo['title'] ?></a> &gt; <?= format_text($this_thread['title']) ?></h4>
    </td></tr></table>
<?php
if($plazainfo['groupid'] > 0)
{
  require_once 'commons/grouplib.php';

  $groupid = $plazainfo['groupid'];

  $group = get_group_byid($groupid);

  $organizer = get_user_byid($group['leaderid'], 'idnum');

  $activetab = 'forum';
  include 'commons/grouptabs.php';
}

if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";

$may_reply = false;

if($plazainfo['locked'] == 'yes')
  echo '     <p><i><img src="/gfx/lock.gif" width="16" height="16" alt="" />This plaza is locked.</i></p>';
else if($this_thread['locked'] == 'yes')
  echo '     <p><i><img src="/gfx/lock.gif" width="16" height="16" alt="" />This thread is locked.</i></p>';
else if($this_thread['updatedate'] < ($now - 6 * 30 * 24 * 60 * 60) && $this_thread['sticky'] == 'no' && !$is_watcher)
  echo '     <p><i><img src="/gfx/lock_soft.png" width="16" height="16" alt="" />This thread was locked due to age.</i></p>';
else if($plazainfo['groupid'] != 0 && !$a_member)
  echo '     <p><i>You must be a member of this group to post.</i></p>';
/*
// prevent double-posting
else if($last_id == $user['idnum'])
  echo '     <p><i>You were the last person to post.</i></p>';
*/
else
  $may_reply = true;

echo '<ul>';
 
if($may_reply)
  echo '<li><a href="/newpost.php?replyto=' . $this_thread['idnum'] . '">Reply</a></li>';

if($thread_subscription === false)
  echo '<li><a href="/threadsubscribe.php?threadid=' . $threadid . '&amp;page=' . $start_page . '">Subscribe</a></li>';
else
  echo '<li><a href="/threadunsubscribe.php?subscriptionid=' . $thread_subscription['idnum'] . '&amp;threadid=' . $threadid . '&amp;page=' . $start_page . '" onclick="return confirm(\'Unsubscribe from this thread?\');">Unsubscribe</a></li>';

echo '</ul>';

if($is_watcher)
  echo '<ul><li><a href="/watchtools.php?threadid=' . $this_thread['idnum'] . '">Access Watcher Tools for this thread</a></li></ul>';

$page_list = paginate($num_pages, $start_page, '/viewthread.php?threadid=' . $this_thread['idnum'] . '&amp;page=%s');

echo $page_list;

$collected_user = array();

foreach($thread_posts as $this_post)
{
/*
  if($this_post['forkedthreadid'] > 0)
  {
    echo '<p class="dim">Thread got de-railed here.  (<a href="viewthread.php?threadid=' $this_post['forkedthreadid'] . '">Take me there; I like tangents!</a>)</p>';
    continue;
  }
*/

  if($now_month == 1 && $now_day == 18 && $now_year == 2012 && $this_post['idnum'] != 1108412)
  {
    $this_post['body'] = '<a href="/viewthread.php?threadid=72226">CENSORED</a>';
  }

  render_post($this_post, $this_thread, $plazainfo, $user, $start_page);
}

echo $page_list;
/*
if($plazainfo['locked'] == 'no' && ($plazainfo['groupid'] == 0 || $a_member))
{
  if($this_thread['locked'] == 'no')
  {
    if($last_id == $user['idnum'])
      echo '<p><i>You were the last person to post.</i></p>';
  }
}
*/
echo '<ul>';

if($may_reply)
  echo '<li><a href="/newpost.php?replyto=' . $this_thread['idnum'] . '">Reply</a></li>';

if($thread_subscription === false)
  echo '<li><a href="/threadsubscribe.php?threadid=' . $threadid . '&amp;page=' . $start_page . '">Subscribe</a></li>';
else
  echo '<li><a href="/threadunsubscribe.php?subscriptionid=' . $thread_subscription['idnum'] . '&amp;threadid=' . $threadid . '&amp;page=' . $start_page . '" onclick="return confirm(\'Unsubscribe from this thread?\');">Unsubscribe</a></li>';

echo '</ul>';

if($thread_watch['reported'] == 'no' && $this_thread['plaza'] != 30 && $plazainfo['groupid'] == 0)
  echo '<ul><li><a href="/reportthread.php?threadid=' . $thread_watch['threadid'] . '" onclick="return confirm(\'Anonymously request this thread be moved to another section?\');">Request this thread be moved to another section</a></li></ul>';

if($thread_watch['reported'] == 'yes')
  echo '     <p><i>You have already reported this thread for misplacement.</i></p>';

if($plazainfo['groupid'] == 0)
  echo '     <h5><a href="/plaza.php">Plaza Forums</a> &gt; <a href="viewplaza.php?plaza=' . $plazainfo['idnum'] . '">' . $plazainfo['title'] . '</a> &gt; ' . format_text($this_thread['title']) . '</h5>';
else
  echo '     <h5><a href="/grouppage.php?id=' . $plazainfo['groupid'] . '">' . $plazainfo['title'] . '</a> &gt; <a href="viewplaza.php?plaza=' . $plazainfo['idnum'] . '">Forum</a> &gt; ' . format_text($this_thread['title']) . '</h5>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
