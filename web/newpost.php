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
require_once 'commons/threadfunc.php';
require_once 'commons/bannedurls.php';
require_once 'commons/trolllib.php';

if($now_month == 1 && $now_day == 18 && $now_year == 2012)
{
  header('Location: /viewthread.php?threadid=72226');
  exit();
}
/*
if($user['idnum'] != 1)
{
  header('Location: ./plazaupdate.php');
  exit();
}
*/
$reply_id = (int)$_GET['replyto'];
$quote_id = (int)$_GET['quote'];

$quote_text = '';

if($quote_id > 0)
{
  $command = 'SELECT a.body,b.display FROM monster_posts AS a LEFT JOIN monster_users AS b ON a.createdby=b.idnum WHERE a.idnum=' . $quote_id . ' LIMIT 1'; 
  $quoted_post = $database->FetchSingle($command, 'fetching quoted post');

  if($quoted_post !== false)
    $quote_text = "\n\n" . '<blockquote><strong>' . $quoted_post['display'] . " said:</strong>\n" . $quoted_post['body'] . '</blockquote>' . "\n" . $user['defaultstyle'];
}

// get thread info
$thread_info = $database->FetchSingle('SELECT * FROM monster_threads WHERE idnum=' . $reply_id . ' LIMIT 1');

if($thread_info === false)
{
  header('Location: /plaza.php');
  exit();
}

// don't allow replies to locked threads
if($thread_info['locked'] == 'yes')
{
  header('Location: /viewplaza.php?plaza=' . $thread_info['plaza']);
  exit();
}

// get plaza info
$command = 'SELECT * FROM monster_plaza WHERE idnum=' . $thread_info['plaza'] . ' LIMIT 1';
$plazainfo = $database->FetchSingle($command, 'newpost.php');

if(substr($plazainfo['title'], 0, 1) == '#' || $plazainfo['locked'] == 'yes')
{
  header('Location: /plaza.php');
  exit();
}

if($plazainfo['groupid'] > 0)
{
  include 'commons/grouplib.php';

  $group = get_group_byid($plazainfo['groupid']);
  $members = explode(',', $group['members']);

  $a_member = is_a_member($group, $user['idnum']);

  if(!$a_member)
  {
    header('Location: /viewplaza.php?plaza=' . $plazainfo['idnum']);
    exit();
  }
}

$watcher_list = explode(',', $plazainfo['admins']);
$is_watcher = in_array($user['idnum'], $watcher_list);

if($thread_info['updatedate'] < ($now - 6 * 30 * 24 * 60 * 60) && $thread_info['sticky'] == 'no' && !$is_watcher)
{
  header('Location: /viewthread.php?threadid=' . $thread_info['idnum'] . '&page=' . (int)$_GET['page']);
  exit();
}
/*
// protect against double-posting
$lastpost = $database->FetchSingle('SELECT * FROM monster_posts WHERE threadid=' . $thread_info['idnum'] . ' ORDER BY idnum DESC LIMIT 1');

if($lastpost['createdby'] == $user['idnum'])
{
  header('Location: /viewthread.php?threadid=' . $thread_info['idnum'] . '&page=' . ceil(($thread_info['replies'] + 1) / 20) . '#bottom');
  exit();
}
*/
$errors = array();

if($_POST['action'] == 'Post')
{
  $body = trim($_POST['body']);

  // make sure there's a title at all
  $title = trim($_POST['title']);
  if(strlen($title) > 120)
    $errors[] = '<p class="failure">The title is too long.  Shorten it a little :P</p>';

  if(strlen($body) == 0)
    $errors[] = '<p class="failure">You should post <em>something</em>...</p>';
  else
  {
    foreach($BANNED_URLS as $url)
    {
      if(strpos($body, $url) !== false)
        $errors[] = '<p class="failure">Linking to ' . $url . ' is not allowed.  (<a href="/help/bannedurls.php">Why?</a>)</p>';
    }
  }

  if(in_array($_POST['says'], $THREAD_SAYS_ALLOWED))
    $post_says = $_POST['says'];
  else
    $post_says = 'says';
  
  $post_title = $title;
  $post_body = $body;
  
  $body = $post_body;
  $title = $post_title;

  $extra_action = $_POST['extraaction'];
  if($extra_action == 'dices')
  {
    $d_num = (int)$_POST['dice_number'];
    $d_sides = (int)$_POST['dice_sides'];
    $d_plus = (int)$_POST['dice_plus'];

    if($d_num <= 0 || $d_sides <= 1 || $d_num > 20)
      $errors[] = '<p class="failure">The number of dice must be between 1 and 20, and the number of sides cannot be less than 2.</p>';

    $extra_action .= ',' . $d_num . ',' . $d_sides . ',' . $d_plus;
  }
  else if($extra_action == 'mt_rand')
  {
    $min = (int)$_POST['rand_min'];
    $max = (int)$_POST['rand_max'];
    
    if($max > 2000000000 || $max < 0 || $min > 2000000000 || $min < 0)
      $errors[] = '<p class="failure">Let\'s keep the values between zero and two billion...</span>';
    if($min > $max)
      $errors[] = '<p class="failure">The minimum value shouldn\'t be larger than the maximum value &gt;_&gt;</span>';

    $extra_action .= ',' . $min . ',' . $max;
  }

  // rate limiting {
  $data = $database->FetchSingle('SELECT COUNT(idnum) AS c FROM monster_posts WHERE creationdate>=' . ($now - 3 * 60) . ' AND createdby=' . $user['idnum']);
  
  if($data['c'] > 3)
    $errors[] = '<p class="failure">You are posting way too frequently &gt;_&gt; Please turn it down a notch.</p>';
  // }

  if(count($errors) == 0)
  {
    $now = time();

    $command = 'SELECT * FROM monster_threads ' .
               'WHERE idnum=' . $reply_id . ' LIMIT 1';
    $my_topic = $database->FetchSingle($command, 'newpost.php');

    $command = 'UPDATE monster_threads ' .
               'SET replies=replies+1, ' .
                   '`updateby`=' . $user['idnum'] . ', ' .
                   "`updatedate`=$now " .
              'WHERE idnum=' . $reply_id . ' LIMIT 1';
    $database->FetchNone($command, 'newpost.php');

    $command = 'UPDATE monster_plaza ' .
               'SET replies=replies+1, ' .
                   "`updatedate`=$now " .
               'WHERE idnum=' . $thread_info['plaza'] . ' LIMIT 1';
    $database->FetchNone($command, 'newpost.php');

    if($plazainfo['groupid'] == 0)
      $postid = create_post($reply_id, $post_says, $post_title, $extra_action, $post_body, $user['idnum']);
    else
      $postid = create_group_post($reply_id, $post_says, $post_title, $extra_action, $post_body, $user['idnum']);

    $text = $user['display'] . ' (' . $user['user'] . ') ' . $post_title . ' ' . $post_body;

    if(is_troll($text))
      create_troll_report($postid, $user['idnum'], $text);

    $today = date('n j');
    if(mt_rand(1, 20) == 1 && ($today == '12 31' || $today == '1 1'))
    {
      $items = array('Coconut Cordial', 'Redsberry Wine', 'Blueberry Wine', 'Vodka', 'Sake');
      add_inventory($user['user'], $SETTINGS['site_ingame_mailer'], $items[array_rand($items)], 'Happy New Year!', 'storage/incoming');
      flag_new_incoming_items($user['user']);
    }

    // update daily plaza usage stat
    $database->FetchNone('UPDATE monster_users SET daily_posts=daily_posts+1,daily_threadviews=daily_threadviews-1 WHERE idnum=' . $user['idnum'] . ' LIMIT 1');

    // fetch the last read date for this section of the plaza
    $command = 'SELECT lastread FROM monster_watching ' .
               'WHERE threadid=-' . $thread_info['plaza'] . ' AND user=' . quote_smart($user['user']) . ' LIMIT 1';
    $data = $database->FetchSingle($command, 'fetching last read date');

    if($data !== false)
    {
      // if the last time we visited it was the last time it was updated (other than our update)...
      if($plazainfo['updatedate'] == $data['lastread'])
      {
        // ... update the last read date to the date of this post
        $command = 'UPDATE monster_watching SET lastread=' . $now . ' WHERE threadid=-' . $thread_info['plaza'] .
          ' AND user=' . quote_smart($user['user']) . ' LIMIT 1';
        $database->FetchNone($command, 'updating last read date');
      }
    }

    header('Location: /viewthread.php?threadid=' . $reply_id . '&page=' . (floor($my_topic['replies'] / 20) + 1) . '#bottom');
    exit();
  }
}
else
{
  $body = $user['defaultstyle'] . $quote_text;
  $_POST['dice_number'] = 1;
  $_POST['dice_sides'] = 6;
  $_POST['dice_plus'] = 0;
}

$previous_posts = $database->FetchMultiple('SELECT * FROM `monster_posts` WHERE threadid=' . $reply_id . ' ORDER BY `idnum` DESC LIMIT 5');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Plaza Forums &gt; <?= $plazainfo['title'] ?> &gt; <?= $thread_info['title'] ?></title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript">
   function consider_action()
   {
     if(document.getElementById('extraaction').value == 'dices')
       document.getElementById('dice_container').style.display = 'inline';
     else
       document.getElementById('dice_container').style.display = 'none';

     if(document.getElementById('extraaction').value == 'mt_rand')
       document.getElementById('rand_container').style.display = 'inline';
     else
       document.getElementById('rand_container').style.display = 'none';
   }

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
   
   $(function() {
     init_textarea_editor();

     $('#preview').click(function() {
       if($('#preview').val() == 'Preview')
       {
         $('#preview').attr('disabled', 'disabled');
         $('#preview-body').attr('disabled', 'disabled');

         $.ajax({
           'url': '/ajax_formattext.php',
           'data': { 'text': $('#post-body').val() },
           'success': function(data) {
             $('#editor-wrapper').hide();
             $('#post-body-preview').html(data).show();
             $('#preview').removeAttr('disabled').val('Edit');
           }
         });
       }
       else
       {
         $('#preview').val('Preview');
         $('#post-body-preview').hide();
         $('#preview-body').removeAttr('disabled');
         $('#editor-wrapper').show();
       }
     });
   });
  </script>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/thread3.js"></script>
  <link rel="stylesheet" href="//<?= $SETTINGS['static_domain'] ?>/css/plaza.css" />
 </head>
 <body onload="consider_action();">
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <table><tr><td>
     <h4><a href="plaza.php">Plaza Forums</a> &gt; <a href="/viewplaza.php?plaza=<?= $plazainfo['idnum'] ?>"><?= $plazainfo['title'] ?></a> &gt; <a href="viewthread.php?threadid=<?= $thread_info["idnum"] ?>"><?= format_text($thread_info["title"]) ?></a></h4>
     </td></tr></table>
<?php
if(count($errors) > 0)
  echo implode("\n", $errors);

if($plazainfo['groupid'] == 0 && $plazainfo['title'] == 'City Hall News')
  echo '<p><b>Hey!</b> Errors regarding updates posted about in the City Hall should be posted in <a href="viewplaza.php?plaza=4">Error Reporting</a>, and ideas for changes and improvements should be posted in <a href="viewplaza.php?plaza=3">Game Ideas</a>.  Not only does doing so keep City Hall threads easy to read for everyone, it makes it easier for developer ' . $SETTINGS['author_resident_name'] . ' to find and respond to errors and ideas.</p><hr />';
	
$r = hexdec(substr($user['color'], 0, 2));
$g = hexdec(substr($user['color'], 2, 2));
$b = hexdec(substr($user['color'], 4, 2));
$text_color = sqrt(0.241*$r*$r + 0.691*$g*$g + 0.068*$b*$b) < 130 ? '#fff' : '#000';
?>
<form method="post" name="newpost" id="newpost">

<div class="plazapost" style="width:100%; border-color:#<?= $user['color'] ?>;">
<div style="background: url(/gfx/residentbar.png) repeat-y;">
 <div class="plazaposttitlebar" style="color:<?= $text_color ?>;background-color:#<?= $user['color'] ?>;">
  <div class="plazapostdecoration centered">
  </div>
  <div class="plazaposttitle"><a href="/residentprofile.php?resident=<?= link_safe($user['display']) ?>" style="color:<?= $text_color ?>;"><?= $user['display'] ?></a> <select name="says"><?php
foreach($THREAD_SAYS_ALLOWED as $says)
  echo '<option value="' . $says . '">' . $says . '</option>';
?></select>: <input name="title" style="width:450px;" maxlength="120" value="<?= htmlspecialchars($title) ?>" placeholder="post title, optional" /></div>
 </div>
 <div>
  <div class="plazapostresident centered">
<?php
  echo '<a href="/residentprofile.php?resident=' . link_safe($user['display']) . '"><img src="' . user_avatar($user) . '" alt="" width="48" height="48" /></a><br />';

  if($user['badges'] != '')
  {
    $badges = explode(',', $user['badges']);
    foreach($badges as $badge)
      echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/badges/' . $badge . '.png" title="' . $BADGE_DESC[$badge] . '" alt="' . $BADGE_DESC[$badge] . '" width="20" height="20" /> ';
  }

  echo '<div><a href="/writemail.php?sendto=' . link_safe($user['display']) . '"><img src="/gfx/sendmail.gif" width="16" height="16" title="Send Mail" alt="send mail" /></a>';

  if($user['license'] == 'yes')
  {
    echo '<a href="/newtrade.php?user=' . link_safe($user['display']) . '"><img src="/gfx/dotrade.gif" width="16" height="16" title="Initiate Trade" alt="start trade" /></a>';
    if($user['openstore'] == 'yes')
      echo '<a href="/userstore.php?user=' . link_safe($user['display']) . '"><img src="/gfx/forsale.png" width="16" height="16" alt="Visit Store" title="Visit Store" /></a>';
  }

  echo '</div>';
?>
  </div>
  <div class="plazaposttextarea" style="background: transparent;" onclick="togglefireworks(<?= $this_post['idnum'] ?>);">
   <div class="rpaction">
    <select name="extraaction" id="extraaction" onchange="consider_action();" onkeyup="consider_action();">
     <option value="none">No special action</option>
     <option value="mt_rand"<?= $_POST['extraaction'] == 'mt_rand' ? ' selected' : '' ?>>Random number</option>
     <option value="dices"<?= $_POST['extraaction'] == 'dices' ? ' selected' : '' ?>>Roll dice</option>
     <option value="flipcoin"<?= $_POST['extraaction'] == 'flipcoin' ? ' selected' : '' ?>>Flip a coin</option>
     <option value="rps"<?= $_POST['extraaction'] == 'rps' ? ' selected' : '' ?>>Rock, paper, scissors</option>
     <option value="randomcharacter"<?= $_POST['extraaction'] == 'randomcharacter' ? ' selected' : '' ?>>Random character description</option>
     <option value="browser"<?= $_POST['extraaction'] == 'browser' ? ' selected' : '' ?>>Report browser/OS</option>
    </select>
    <span id="rand_container" style="display: none;">from <input name="rand_min" maxlength="10" size="10" value="<?= $_POST['rand_min'] ?>" /> to <input name="rand_max" maxlength="10" size="10" value="<?= $_POST['rand_max'] ?>" /></span>
    <span id="dice_container" style="display: none;"><input name="dice_number" maxlength="2" size="2" value="<?= $_POST['dice_number'] ?>" /> d <input name="dice_sides" maxlength="3" size="3" value="<?= $_POST['dice_sides'] ?>" /> + <input name="dice_plus" maxlength="3" size="3" value="<?= $_POST['dice_plus'] ?>" /></span>
   </div>
   <div class="userformatting">
    <div id="post-body-preview" style="display:none;"></div>
    <div id="editor-wrapper">
     <ul data-target="post-body" class="textarea-editor"></ul>
     <textarea id="post-body" name="body" cols="60" rows="10" style="width:100%;"><?= $body ?></textarea>
    </div>
   </div>
  </div>
  <div class="plazapostfooter plazapostfooterrow" style="margin-left: 100px;">
<?php
  echo '<span id="postvote' . $this_post['idnum'] . '" style="vertical-align:middle;">';

  echo
    '<img src="//' . $SETTINGS['static_domain'] . '/gfx/forum/thumbup.png" class="transparent_image" id="thumbup' . $this_post['idnum'] . '" />',
    '<img src="//' . $SETTINGS['static_domain'] . '/gfx/forum/thumbdown.png" class="transparent_image" id="thumbdown' . $this_post['idnum'] . '" />'
  ;

  echo '</span> <img src="//' . $SETTINGS['static_domain'] . '/gfx/forum/report.png" width="11" height="16" alt="report post" class="transparent_image" id="report' . $this_post['idnum'] . '" /> <i class="dim">Posted... in the near future!</i>';
?>
  </div>
 </div>
</div>
</div>
<p><input type="button" id="preview" value="Preview"> <input type="submit" name="action" value="Post" /></p>
</form>
     <h5>Previous Few Posts</h5>
<?php
foreach($previous_posts as $p_post)
{
  render_post($p_post, $replying_thread, $plazainfo);
}
?>
     <h5>Formatting Help</h5>
     <?= formatting_help(); ?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
