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

$command = 'SELECT * FROM monster_threads WHERE idnum=' . $threadid . ' LIMIT 1';
$this_thread = $database->FetchSingle($command, 'viewthread.php?idnum=' . $threadid);

if($this_thread === false)
{
  header('Location: /plaza.php');
  exit();
}

$command = 'SELECT * FROM monster_plaza WHERE idnum=' . $this_thread['plaza'] . ' LIMIT 1';
$plazainfo = $database->FetchSingle($command, 'viewthread.php?idnum=' . $threadid);

$watcher_list = explode(',', $plazainfo['admins']);
$is_watcher = in_array($user['idnum'], $watcher_list);

$command = 'SELECT COUNT(idnum) AS c FROM monster_posts ' .
           'WHERE threadid=' . $threadid;
$data = $database->FetchSingle($command, 'fetching thread count');
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
$command = 'SELECT * FROM monster_watching ' .
           'WHERE `user`=' . quote_smart($user['user']) . ' ' .
           'AND threadid=' . $threadid . ' LIMIT 1';
$thread_watch = $database->FetchSingle($command, 'viewthread.php?idnum=' . $threadid);

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
  $command = "UPDATE monster_watching " .
             "SET `lastread`='" . time() . "' " .
             "WHERE `user`=" . quote_smart($user['user']) . ' ' .
             "AND threadid=" . $threadid . ' LIMIT 1';
  $database->FetchNone($command, 'viewthread.php?idnum=' . $threadid);
}

$a_member = (array_search($plazainfo['groupid'], take_apart(',', $user['groups'])) !== false);

$command = 'SELECT * FROM psypets_watchedthreads WHERE userid=' . $user['idnum'] . ' AND threadid=' . $threadid . ' LIMIT 1';
$thread_subscription = $database->FetchSingle($command, 'viewthread.php?threadid=' . $threadid);

$command = 'UPDATE monster_users SET daily_threadviews=daily_threadviews+1 WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
$database->FetchNone($command, 'updating plaza usage');

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
  
  $command = 'UPDATE monster_threads SET opening_post_id=' . $opening_post['idnum'] . ' WHERE idnum=' . $threadid . ' LIMIT 1';
  $database->FetchNone($command, 'updating thread to know its opening post');
}

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; Plaza Forums &gt; <?= $plazainfo['title'] ?> &gt; <?= $this_thread['title'] ?></title>
<?php include 'commons/head.php'; ?>
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
  <script type="text/javascript" src="http://saffron.psypets.net/js/thread3.js"></script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
    <div class="shadowed-box" id="kitchen" style="display: none;"></div>
    <table class="nomargin"><tr><td>
     <h4><a href="plaza.php">Plaza Forums</a> &gt; <a href="viewplaza.php?plaza=<?= $plazainfo['idnum'] ?>"><?= $plazainfo['title'] ?></a> &gt; <?= format_text($this_thread['title']) ?></h4>
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
  echo '     <p><i><img src="gfx/lock.gif" width="16" height="16" alt="" />This plaza is locked.</i></p>';
else if($this_thread['locked'] == 'yes')
  echo '     <p><i><img src="gfx/lock.gif" width="16" height="16" alt="" />This thread is locked.</i></p>';
else if($this_thread['updatedate'] < ($now - 6 * 30 * 24 * 60 * 60) && $this_thread['sticky'] == 'no' && !$is_watcher)
  echo '     <p><i><img src="gfx/lock_soft.png" width="16" height="16" alt="" />This thread was locked due to age.</i></p>';
else if($plazainfo['groupid'] != 0 && !$a_member)
  echo '     <p><i>You must be a member of this group to post.</i></p>';
else if($last_id == $user['idnum'])
  echo '     <p><i>You were the last person to post.</i></p>';
else
  $may_reply = true;

echo '<ul>';
 
if($may_reply)
  echo '<li><a href="newpost.php?replyto=' . $this_thread['idnum'] . '">Reply</a></li>';

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

$post_num = 0;

foreach($thread_posts as $this_post)
{
/*
  if($this_post['forkedthreadid'] > 0)
  {
    echo '<p class="dim">Thread got de-railed here.  (<a href="viewthread.php?threadid=' $this_post['forkedthreadid'] . '">Take me there; I like tangents!</a>)</p>';
    continue;
  }
*/
  $my_user = get_user_byid($this_post['createdby'], 'is_npc,user,display,graphic,is_a_whale,color,donated,badges,license,openstore');
  $my_user['admin'] = get_admin_byuser($my_user['user']);

  $is_action = (strlen($this_post['action']) > 0);

  $may_edit = ($this_post['createdby'] == $user['idnum'] &&
    $this_post['locked'] == 'no' &&
    ($this_thread['locked'] == 'no' || ($post_num == 0 && in_array($user['idnum'], $watcher_list))) &&
    ($plazainfo['groupid'] == 0 || $a_member));

  mt_srand($this_post['idnum']);
  $x = mt_rand(1, 1000);
  $y = mt_rand(1, 1000);

  $post_style = 'width: 100%;';
  if($this_post['background'] > 0)
    $post_style .= ' background: #fff url(\'gfx/postwalls/' . $POST_BACKGROUNDS[$this_post['background']] . '.png\') repeat ' . $x . 'px ' . $y . 'px;';
  else
    $post_style .= ' background-color: #fff;';

  if($this_post['troll_flag'] == 'yes')
  {
    echo '<p class="dim" id="warn' . $this_post['idnum'] . '">Flammable post concealed for your safety.  (<a href="#" onclick="reveal_flames(' . $this_post['idnum'] . '); return false;">Show me; I\'m heat-resistant.</a>)</p>',
         '<div id="troll' . $this_post['idnum'] . '" style="display:none;">';
  }
?>
<div class="plazapost" style="<?= $post_style ?> border-color:#<?= $my_user['color'] ?>;" id="p<?= $this_post['idnum'] ?>">
 <div class="plazaposttitlebar" style="background-color:#<?= $my_user['color'] ?>;">
  <div class="plazapostdecoration centered">
<?php
  if($start_page == 1 && $this_thread['createdby'] == $user['idnum'] && $post_num == 0)
    echo '<a href="/selftrash_thread.php?threadid=' . $threadid . '" onclick="return confirm(\'Really?  Trash this tread?  Are you totally OK with that?\');"><img src="gfx/trash.png" width="16" height="16" alt="(trash my thread)" /></a>';

  if($this_post['goldstars'] > 0)
    echo '<img src="/gfx/goldstar.png" width="16" height="16" alt="Gold Star" />&times;' . $this_post['goldstars'];

  if($this_post['createdby'] != $user['idnum'] && $user['stickers_to_give'] > 0)
    echo ' <a href="/giveastar.php?postid=' . $this_post['idnum'] . '"><img src="gfx/goldstar_add.png" width="16" height="16" alt="Give a Star" /></a>';

  if($user['fireworks'] != '' && $this_post['background'] == 0)
    echo ' <a href="#" onclick="firework_popup(' . $this_post['idnum'] . ', \'' . tip_safe($my_user['display']) . '\'); return false;"><img src="gfx/fireworks.png" width="16" height="16" alt="Apply Background" /></a>';
?>
  </div>
  <div class="plazaposttitle"><?= $this_post['title'] == '' ? '&nbsp;' : format_text($this_post['title']) ?></div>
 </div>
 <div>
  <div class="plazapostresident centered">
<?php
  if(strlen($my_user['display']) > 0)
  {
    echo '<a href="/residentprofile.php?resident=' . link_safe($my_user['display']) . '"><img src="' . user_avatar($my_user) . '" alt="" width="48" height="48" /><br />' .
         $my_user['display'] . '</a><br />';

    if($my_user['badges'] != '')
    {
      $badges = explode(',', $my_user['badges']);
      foreach($badges as $badge)
        echo '<img src="http://saffron.psypets.net/gfx/badges/' . $badge . '.png" title="' . $BADGE_DESC[$badge] . '" alt="' . $BADGE_DESC[$badge] . '" width="20" height="20" /> ';
    }
  }
  else
    echo '<img src="/gfx/shim.gif" width="48" height="48" alt="" /><br />' .
         '<i class="dim">[departed #' . $this_post['createdby'] . ']</i>';

  if($this_post['createdby'] != $user['idnum'] && strlen($my_user['display']) > 0 && $my_user['is_npc'] == 'no')
  {
    echo '<div>';
    
    echo '<a href="/writemail.php?sendto=' . link_safe($my_user['display']) . '"><img src="/gfx/sendmail.gif" width="16" height="16" title="Send Mail" alt="send mail" /></a>';

    if($my_user['license'] == 'yes' && $user['license'] == 'yes')
    {
      echo '<a href="/newtrade.php?user=' . link_safe($my_user['display']) . '"><img src="/gfx/dotrade.gif" width="16" height="16" title="Initiate Trade" alt="start trade" /></a>';
      if($my_user['openstore'] == 'yes')
        echo '<a href="/userstore.php?user=' . link_safe($my_user['display']) . '"><img src="/gfx/forsale.png" width="16" height="16" alt="Visit Store" title="Visit Store" /></a>';
    }

    echo '</div>';
  }
?>
  </div>
  <div class="plazaposttextarea" onclick="togglefireworks(<?= $this_post['idnum'] ?>);">
<?php

  if($this_post['egg'] != 'none' && $this_post['egg'] != 'taken')
  {
    $is_action = true;
    $this_post['action'] .= '<p class="nomargin"><a href="/grabegg.php?id=' . $this_post['idnum'] . '"><img src="http://saffron.psypets.net/gfx/items/egg_dyed_' . $this_post['egg'] . '.png" border="0" style="vertical-align:middle;" /> OMG!  You found an egg!  Click it!  Click it!</a></p>';
  }

  if($is_action)
  {
?>
   <div class="rpaction"><?= $this_post['action'] ?></div>
<?php
  }

  if($this_post['troll_flag'] == 'yes')
    echo '<div style="background-color:#fee; border: 1px dashed #c00; padding: 0.5em; margin-bottom: 1em;"><p style="margin-bottom: 0;">Warning: this post may have been written to <em>try</em> to start a fight.  If you <em>must</em> respond to it, please do so in a civil manner.  (If this warning seems to have been applied inappropriately, please <a href="admincontact.php">contact an administrator</a>.)</p></div>';
?>
   <div class="userformatting"><?= format_text($this_post['body']) ?></div>
  </div>
  <div class="plazapostfooter plazapostfooterrow">
<?php
  echo '<span id="postvote' . $this_post['idnum'] . '" style="vertical-align:middle;">';

  if($this_post['voted_on'] == 'no')
  {
    echo
      '<a href="#" onmouseover="hoveron(\'thumbup' . $this_post['idnum'] . '\')" onmouseout="hoveroff(\'thumbup' . $this_post['idnum'] . '\')" onclick="thumbsup(' . $this_post['idnum'] . '); return false;"><img src="http://saffron.psypets.net/gfx/forum/thumbup.png" class="transparent_image" id="thumbup' . $this_post['idnum'] . '" /></a>',
      '<a href="#" onmouseover="hoveron(\'thumbdown' . $this_post['idnum'] . '\')" onmouseout="hoveroff(\'thumbdown' . $this_post['idnum'] . '\')" onclick="thumbsdown(' . $this_post['idnum'] . '); return false;"><img src="http://saffron.psypets.net/gfx/forum/thumbdown.png" class="transparent_image" id="thumbdown' . $this_post['idnum'] . '" /></a>'
    ;
  }
  else
  {
    $vote = get_post_vote($this_post['idnum'], $user['idnum']);

    if($vote === false)
      echo
        '<a href="#" onmouseover="hoveron(\'thumbup' . $this_post['idnum'] . '\')" onmouseout="hoveroff(\'thumbup' . $this_post['idnum'] . '\')" onclick="thumbsup(' . $this_post['idnum'] . '); return false;"><img src="http://saffron.psypets.net/gfx/forum/thumbup.png" class="transparent_image" id="thumbup' . $this_post['idnum'] . '" /></a>',
        '<a href="#" onmouseover="hoveron(\'thumbdown' . $this_post['idnum'] . '\')" onmouseout="hoveroff(\'thumbdown' . $this_post['idnum'] . '\')" onclick="thumbsdown(' . $this_post['idnum'] . '); return false;"><img src="http://saffron.psypets.net/gfx/forum/thumbdown.png" class="transparent_image" id="thumbdown' . $this_post['idnum'] . '" /></a>'
      ;
    else if($vote['vote'] == -1)
      echo
        '<a href="#" onmouseover="hoveron(\'thumbup' . $this_post['idnum'] . '\')" onmouseout="hoveroff(\'thumbup' . $this_post['idnum'] . '\')" onclick="thumbsup(' . $this_post['idnum'] . '); return false;"><img src="http://saffron.psypets.net/gfx/forum/thumbup.png" class="transparent_image" id="thumbup' . $this_post['idnum'] . '" /></a>',
        '<a href="#" onclick="return false;"><img src="http://saffron.psypets.net/gfx/forum/thumbdown.png" id="thumbdown' . $this_post['idnum'] . '" /></a>'
      ;
    else if($vote['vote'] == 1)
      echo
        '<a href="#" onclick="return false;"><img src="http://saffron.psypets.net/gfx/forum/thumbup.png" id="thumbup' . $this_post['idnum'] . '" /></a>',
        '<a href="#" onmouseover="hoveron(\'thumbdown' . $this_post['idnum'] . '\')" onmouseout="hoveroff(\'thumbdown' . $this_post['idnum'] . '\')" onclick="thumbsdown(' . $this_post['idnum'] . '); return false;"><img src="http://saffron.psypets.net/gfx/forum/thumbdown.png" class="transparent_image" id="thumbdown' . $this_post['idnum'] . '" /></a>'
      ;
  }

  echo '</span> <a href="#" onmouseover="hoveron(\'report' . $this_post['idnum'] . '\')" onmouseout="hoveroff(\'report' . $this_post['idnum'] . '\')" onclick="report_post(' . $this_post['idnum'] . ', \'' . tip_safe($my_user['display']) . '\'); return false;" style="vertical-align:middle;"><img src="http://saffron.psypets.net/gfx/forum/report.png" width="11" height="16" alt="report post" class="transparent_image" id="report' . $this_post['idnum'] . '" /></a> <i class="dim">Posted ' . local_time($this_post['creationdate'], $user['timezone'], $user['daylightsavings']);

  if($this_post['updatedate'] > $this_post['creationdate'])
    echo '; edited ' . local_time($this_post['updatedate'], $user["timezone"], $user['daylightsavings']);

  $admin_commands = array();

  if($user['admin']['manageaccounts'] == 'yes')
    $admin_commands[] = '<i>#' . $this_post['idnum'] . '</i>';

  if($admin['deletespam'] == 'yes')
    $admin_commands[] = '<a href="/admin/spamcontrol.php?userid=' . $this_post['createdby'] . '&amp;action=delete&amp;p_' . $this_post['idnum'] . '=yes" onclick="return confirm(\'Really delete this post?\');">delete</a>';

  if($admin['deletespam'] == 'yes' && $user['admin']['manageaccounts'] == 'yes')
  {
    if($this_post['troll_flag'] == 'no')
      $admin_commands[] = '<a href="/admin/markastroll.php?postid=' . $this_post['idnum'] . '" onclick="return confirm(\'Flag this post as containing trolls?\');">trolls</a>';
    else
      $admin_commands[] = '<a href="/admin/unmarkastroll.php?postid=' . $this_post['idnum'] . '">no trolls</a>';
  }

  if(count($admin_commands) > 0)
    echo ' (' . implode(', ', $admin_commands) . ')';

  echo '</i>';

  if($this_post['createdby'] != $user['idnum'] && $my_user['is_npc'] == 'no')
    echo '<a href="/writemail.php?quotepost=' . $this_post['idnum'] . '" title="Reply via PsyMail"><div class="button smallbutton" style="margin-left:10px;"><div><img src="gfx/sendmail_15.png" class="inlineimage" alt="" /></div></div></a>';

  if($may_edit)
    echo '<a href="/editpost.php?postid=' . $this_post['idnum'] . '&amp;page=' . $start_page .'"><div class="button"><div>Edit Post</div></div></a>';
  else if($may_reply)
    echo '<a href="/newpost.php?replyto=' . $this_thread['idnum'] . '&amp;quote=' . $this_post['idnum'] . '"><div class="button"><div>Quote</div></div></a>';

  echo '</div></div></div>';

  if($this_post['troll_flag'] == 'yes')
    echo '</div>';

  $post_num++;
}

echo $page_list;

if($plazainfo['locked'] == 'no' && ($plazainfo['groupid'] == 0 || $a_member))
{
  if($this_thread['locked'] == 'no')
  {
    if($last_id == $user['idnum'])
      echo '<p><i>You were the last person to post.</i></p>';
  }
}

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
