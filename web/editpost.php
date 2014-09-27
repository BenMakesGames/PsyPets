<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/threadfunc.php';
require_once 'commons/psypetsformatting.php';
require_once 'commons/trolllib.php';
/*
if($user['idnum'] != 1)
{
  header('Location: ./plazaupdate.php');
  exit();
}
*/
$postid = (int)$_GET['postid'];

$command = 'SELECT * ' .
           'FROM monster_posts ' .
           'WHERE idnum=' . $postid . ' LIMIT 1';
$post_info = $database->FetchSingle($command, 'editpost.php');

if($post_info === false)
{
  header('Location: ./plaza.php');
  exit();
}

if($post_info['createdby'] != $user['idnum'] || $post_info['locked'] == 'yes')
{
  header('Location: ./viewthread.php?threadid=' . $post_info['threadid'] . '&page=' . (int)$_GET['page']);
  exit();
}

$command = 'SELECT * FROM monster_threads WHERE idnum=' . $post_info['threadid'] . ' LIMIT 1';
$thread_info = $database->FetchSingle($command, 'editpost.php');

if($thread_info === false)
{
  header('Location: ./plaza.php');
  exit();
}

$command = 'SELECT * FROM monster_plaza WHERE idnum=' . $thread_info['plaza'] . ' LIMIT 1';
$plaza_info = $database->FetchSingle($command, 'editpost.php');

if($plaza_info === false)
{
  header('Location: ./plaza.php');
  exit();
}

$watcher_list = explode(',', $plaza_info['admins']);

$a_member = (array_search($plaza_info['groupid'], take_apart(',', $user['groups'])) !== false);

$firstpost = get_first_post($post_info['threadid']);

$may_edit = ($post_info['createdby'] == $user['idnum'] &&
  ($thread_info['locked'] == 'no' || ($firstpost['idnum'] == $post_info['idnum'] && in_array($user['idnum'], $watcher_list))) &&
  ($plaza_info['groupid'] == 0 || $a_member));

if(!$may_edit)
{
  header('Location: ./viewthread.php?threadid=' . $post_info['threadid'] . '&page=' . (int)$_GET['page'] . '&maynotedit');
  exit();
}

if($_POST['action'] == 'Preview')
{
  $body = trim($_POST['body']);
  $title = trim($_POST['title']);

  if(strlen($title) > 120)
    $title = substr($title, 0, 120);

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

	$preview_body = $body;
  $preview_title = $title;
}
else if($_POST['action'] == 'Post')
{
  $now = time();

  $title = trim($_POST['title']);
  $body = trim($_POST['body']);

  if(strlen($title) > 120)
    $title = substr($title, 0, 120);

  if($firstpost['idnum'] == $post_info['idnum'] && strlen($title) == 0)
    $title = '[untitled]';

  $command = 'UPDATE monster_posts ' .
             'SET title=' . quote_smart($title) . ', ' .
                 'body=' . quote_smart($body) . ', ' .
                 "updatedate=$now " .
             'WHERE idnum=' . $post_info['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating post');

  // if we changed the post title and this is the first post of the thread...
  if($title != $post_info['title'] && $firstpost['idnum'] == $post_info['idnum'])
  {
    // ... update the thread's title as well
    $command = 'UPDATE monster_threads ' .
               'SET title=' . quote_smart($title) . ' ' .
               'WHERE idnum=' . $post_info['threadid'] . ' ' .
               'LIMIT 1';
   $database->FetchNone($command, 'updating thread title');
  }

  $text = $user['display'] . ' (' . $user['user'] . ') ' . $title . ' ' . $body;

  if(is_troll($text))
    create_troll_report($postid, $user['idnum'], $text);

  header('Location: ./viewthread.php?threadid=' . $post_info['threadid'] . '&page=' . $_GET['page'] . '#p' . $postid);
  exit();
}
else
{
  $title = $post_info['title'];
  $body = $post_info['body'];
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Plaza Forums &gt; <?= $plaza_info['title'] ?> &gt; <?= $thread_info['title'] ?></title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript">
  $(function() {
    init_textarea_editor();
  });
	</script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4><a href="plaza.php">Plaza Forums</a> &gt; <a href="viewplaza.php?plaza=<?= $plaza_info['idnum'] ?>"><?= $plaza_info['title'] ?></a> &gt; <a href="viewthread.php?threadid=<?= $thread_info['idnum'] ?>"><?= $thread_info['title'] ?></a></h4>
<?php
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
?>
     <form action="editpost.php?postid=<?= $postid ?>&page=<?= $_GET['page'] ?>" method="post" name="editpost" id="editpost">
     <input type="hidden" name="replyto" value="<?= $_POST['replyto'] ?>" />
     <table>
      <tr>
       <th>Title:</th>
       <td><input name="title" maxlength="120" style="width:460px;" value="<?= $title ?>" /></td>
      </tr>
      <tr>
       <th colspan="2">Message:</th>
      </tr>
      <tr>
       <td colspan="2">
			  <ul data-target="post-body" class="textarea-editor"></ul>
			  <textarea id="post-body" name="body" cols="50" rows="10" style="width:500px;"><?= $body ?></textarea>
			 </td>
      </tr>
      <tr>
       <td colspan="2" align="right"><input type="submit" name="action" value="Preview" />&nbsp;<input type="submit" name="action" value="Post" /></td>
      </tr>
     </table>
     <?= formatting_help(); ?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
