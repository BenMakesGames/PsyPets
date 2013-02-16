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
$plazaid = (int)$_GET['plaza'];

$command = 'SELECT * FROM monster_plaza WHERE `idnum`=' . $plazaid . ' LIMIT 1';
$plazainfo = $database->FetchSingle($command, 'fetching plaza');

if(substr($plazainfo['title'], 0, 1) == '#' || $plazainfo === false)
{
  header('Location: ./plaza.php');
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
    header('Location: ./viewplaza.php?plaza=' . $plazainfo['idnum']);
    exit();
  }
}

// you _can_ make a new thread here, right?
if($plazainfo['locked'] == 'yes' || $plazainfo['newthreadlock'] == 'yes')
{
  header('Location: ./viewplaza.php?plaza=' . $plazainfo['idnum']);
  exit();
}

if($_POST['action'] == 'Preview')
{
  $body = trim($_POST['body']);

  // make sure there's a title at all
  $title = trim($_POST['title']);
  if(strlen($title) > 120)
    $errors[] = '<p class="failure">The title is too long.  It should not be more than 120 characters.</p>';

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

  $preview = true;

  $preview_title = $title;
  $preview_body = $body;

  $title = $preview_title;
  $body = $preview_body;
}
else if($_POST['action'] == 'Post')
{
  $body = trim($_POST['body']);
  $title = trim($_POST['title']);

  if(strlen($title) == 0)
    $title = '[untitled]';
  else if(strlen($title) > 120)
    $errors[] = '<p class="failure">The title is too long.  It should not be more than 120 characters.</p>';

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

  $post_title = htmlentities($title);
  $post_body = $body;
  
  $body = $post_body;

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

  $command = 'SELECT COUNT(idnum) AS c FROM monster_posts WHERE creationdate>=' . ($now - 3 * 60) . ' AND createdby=' . $user['idnum'];
  $data = $database->FetchSingle($command, 'fetching post count');
  
  if($data['c'] > 3)
    $errors[] = '<p class="failure">You are posting way too frequently &gt;_&gt; Please turn it down a notch.</p>';

  if(count($errors) == 0)
  {
    $now = time();

    $command = 'INSERT INTO `monster_threads` ' .
               "(`plaza`, `title`, `sticky`, `locked`, `creationdate`, `updatedate`, `createdby`, `updateby`, `replies`, `views`) " .
               'VALUES ' .
               '(' . $plazaid . ', ' . quote_smart($post_title) . ", 'no', 'no', '" . $now . "', '" . $now . "', '" . $user['idnum'] . "', '" . $user['idnum'] . "', '0', '0')";
    $database->FetchNone($command, 'newthread.php');

    $thread_id = $database->InsertID();

    $command = 'UPDATE monster_plaza ' .
               "SET replies=replies+1, `updatedate`='$now' " .
               'WHERE idnum=' . $plazainfo['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'newthread.php');

    if($plazainfo['groupid'] == 0)
      $postid = create_post($thread_id, 'says', $post_title, $extra_action, $post_body, $user['idnum']);
    else
      $postid = create_group_post($thread_id, 'says', $post_title, $extra_action, $post_body, $user['idnum']);

    $text = $user['display'] . ' (' . $user['user'] . ') ' . $post_title . ' ' . $post_body;

    if(is_troll($text))
      create_troll_report($postid, $user['idnum'], $text);

    $command = 'UPDATE monster_users SET daily_posts=daily_posts+1,daily_threadviews=daily_threadviews-1 WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'updating plaza usage');

    $command = 'UPDATE monster_threads SET opening_post_id=' . $postid . ' WHERE idnum=' . $thread_id . ' LIMIT 1';
    $database->FetchNone($command, 'updating thread to know its first post');

    // fetch the last read date for this section of the plaza
    $command = 'SELECT lastread FROM monster_watching ' .
               'WHERE threadid=-' . $plazaid . ' AND user=' . quote_smart($user['user']) . ' LIMIT 1';
    $data = $database->FetchSingle($command, 'fetching last read date');

    if($data !== false)
    {
      // if the last time we visited it was the last time it was updated (other than our update)...
      if($plazainfo['updatedate'] == $data['lastread'])
      {
        // ... update the last read date to the date of this post
        $command = 'UPDATE monster_watching SET lastread=' . $now . ' WHERE threadid=-' . $plazaid .
          ' AND user=' . quote_smart($user['user']) . ' LIMIT 1';
        $database->FetchNone($command, 'updating last read date');
      }
    }

    header('Location: ./viewthread.php?threadid=' . $thread_id);
    exit();
  }
}
else if($plazaid == 4) // if posting to error-reporting
  $_POST['extraaction'] = 'browser';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Plaza Forums &gt; <?= $plazainfo['title'] ?></title>
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

   $(function() {
     init_textarea_editor();
   });
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4><a href="plaza.php">Plaza Forums</a> &gt; <a href="viewplaza.php?plaza=<?= $plazainfo["idnum"] ?>"><?= $plazainfo["title"] ?></a></h4>
<?php
if(count($errors) > 0)
  echo '<ul><li>' . implode('</li><li>', $errors) . '</li></ul>';

if($preview)
{
?>
     <h4>Preview</h4>
     <p><table class="preview"><tr><td>
     <?= format_text($preview_body, false) ?>
     </td></tr></table></p>
     <h4>Write Reply</h4>
<?php
}
else if(strlen($body) == 0)
  $body = $user['defaultstyle'];
?>
     <form action="newthread.php?plaza=<?= $plazaid ?>" method="post" name="newthread" id="newthread">
     <input type="hidden" name="replyto" value="<?= $_POST['replyto'] ?>">
     <table>
      <tr>
       <th>Title:</th>
       <td><input name="title" style="width:460px;" maxlength=120 value="<?= htmlspecialchars($_POST['title']) ?>" /></td>
      </tr>
      <tr>
       <th>Action:</th>
       <td>
        <select name="extraaction" id="extraaction" onchange="consider_action();" onkeyup="consider_action();">
         <option value="none">None</option>
         <option value="mt_rand"<?= $_POST['extraaction'] == 'mt_rand' ? ' selected' : '' ?>>Random number</option>
         <option value="dices"<?= $_POST['extraaction'] == 'dices' ? ' selected' : '' ?>>Roll dice</option>
         <option value="flipcoin"<?= $_POST['extraaction'] == 'flipcoin' ? ' selected' : '' ?>>Flip a coin</option>
         <option value="rps"<?= $_POST['extraaction'] == 'rps' ? ' selected' : '' ?>>Rock, paper, scissors</option>
         <option value="randomcharacter"<?= $_POST['extraaction'] == 'randomcharacter' ? ' selected' : '' ?>>Random character description</option>
         <option value="browser"<?= $_POST['extraaction'] == 'browser' ? ' selected' : '' ?>>Report browser/OS</option>
        </select>
        <span id="rand_container" style="display: none;">from <input name="rand_min" maxlength="10" size="10" value="<?= $_POST['rand_min'] ?>" /> to <input name="rand_max" maxlength="10" size="10" value="<?= $_POST['rand_max'] ?>" /></span>
        <span id="dice_container" style="display: none;"><input name="dice_number" maxlength="2" size="2" value="<?= $_POST['dice_number'] ?>" /> d <input name="dice_sides" maxlength="3" size="3" value="<?= $_POST['dice_sides'] ?>" /> + <input name="dice_plus" maxlength="3" size="3" value="<?= $_POST['dice_plus'] ?>" /></span>
       </td>
      </tr>
      <tr>
       <th colspan="2">Message:</th>
      </tr>
      <tr>
       <td colspan="2"><ul data-target="postbody" class="textarea-editor"></ul><textarea id="postbody" name="body" cols="60" rows="10" style="width:600px;"><?= htmlentities($body) ?></textarea></td>
      </tr>
      <tr>
       <td colspan="2" align="right"><input type="submit" name="action" value="Preview" />&nbsp;<input type="submit" name="action" value="Post" /></td>
      </tr>
     </table>
     <?= formatting_help(); ?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
