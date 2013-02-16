<?php
$require_petload = 'no';
$_GET['maintenance'] = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/psypetsformatting.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
require_once 'commons/newslib.php';
require_once 'commons/threadfunc.php';
require_once 'commons/rsslib.php';

if($user['admin']['mailpsypets'] == 'no')
{
  header('Location: /');
  exit();
}

$error_message = array();

if($_POST['submit'] == 'Post')
{
  if($user['admin']['proxypost'])
  {
    $post_as = get_user_bydisplay($_POST['from']);
    if($post_as === false)
      $error_message[] = '39:' . $_POST['from'];
    else
      $from_id = $post_as['idnum'];
  }
  else
    $from_id = $user['idnum'];

  if($_POST['thread'] == 'yes' || $_POST['thread'] == 'no')
    $has_thread = $_POST['thread'];
  else
    $error_message[] = '<p>Will this post have an associated thread, or no?</p>';

  if(count($error_message) == 0)
  {
    $message = trim($_POST['message']);

    $post_id = news_post($from_id, $_POST['category'], $_POST['subject'], $message, false);

    if($has_thread == 'yes')
    {
      $total_title = $_POST['category'] . ': ' . $_POST['subject'];

      $command = 'SELECT * FROM monster_plaza WHERE title=\'City Hall News\' LIMIT 1';
      $news_plaza = $database->FetchSingle($command, 'writenewspost.php');

      $database->FetchNone('INSERT INTO `monster_threads` ' .
                 '(`plaza`, `title`, `creationdate`, `updatedate`, `createdby`, `updateby`) VALUES ' .
                 '(' . $news_plaza['idnum'] . ', ' . quote_smart($total_title) . ", '" . $now . "', '" . $now . "', '" . $from_id . "', '" . $from_id . "')");

      $thread_id = $database->InsertID();

      $database->FetchNone('UPDATE monster_plaza ' .
                 'SET replies=replies+1, `updatedate`=' . $now . ' ' .
                 'WHERE idnum=' . $news_plaza['idnum'] . ' LIMIT 1');

      create_post($thread_id, 'says', $total_title, '', $message, $from_id);

      $command = 'UPDATE psypets_news SET threadid=' . $thread_id . ' WHERE idnum=' . $post_id . ' LIMIT 1';
      $database->FetchNone($command, 'writenewspost.php');
    }

    $command = 'UPDATE monster_users SET newcityhallpost=\'yes\'';
    $database->FetchNone($command, 'writenewspost.php');

    render_xml_latest_news();

    header('Location: ./cityhall.php');
    exit();
  }
}

if(!array_key_exists('submit', $_POST))
  $_POST['from'] = $SETTINGS['site_ingame_mailer'];

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Admin Tools &gt; Write News Post</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Admin Tools</a> &gt; Write News Post</h4>
<?php
if(count($error_message) > 0)
  echo "<p>" . form_message($error_message) . "</p>";

if($_POST['submit'] == 'Preview')
{
?>
<h4>Preview</h4>
<div class="category_<?= $_POST['category'] ?>">
<h5><?= $_POST['subject'] ?></h5>
<?= format_text($_POST['message'], false) ?>
</div>
<h4>Write Post</h4>
<?php
}

if($admin['coder'] == 'yes')
  echo '<p class="progress">Do you also need to post to the <a href="/admin/changelog.php">changelog</a>?</p>';
?>
<form action="writenewspost.php" method="post">
<table>
<?php
if($user['admin']['proxypost'] == 'yes')
{
?>
 <tr>
  <th>As:&nbsp;</th>
  <td><input name="from" value="<?= $_POST['from'] ?>" style="width:440px;" /></td>
 </tr>
<?php
}
?>
 <tr>
  <th>Subject:&nbsp;</th>
  <td><input name="subject" value="<?= str_replace('"', '&quot;', $_POST['subject']) ?>" style="width:440px;" /></td>
 </tr>
 <tr>
  <th colspan="2">Message:</td>
 </tr>
 <tr>
  <td colspan="2">
   <textarea name="message" cols=50 rows=10 style="width:500px;"><?= str_replace(array("<", ">"), array("&lt;", "&gt;"), $_POST["message"]) ?></textarea>
  </td>
 </tr>
 <tr>
  <td colspan="2">
   <b>Category:</b>
   <select name="category">
    <option value="routine"<?= ($_POST["category"] == "routine" ? " selected" : "") ?>>Routine</option>
    <option value="ramble"<?= ($_POST["category"] == "ramble" ? " selected" : "") ?>>Ramble</option>
    <option value="important"<?= ($_POST["category"] == "important" ? " selected" : "") ?>>Important</option>
    <option value="severe"<?= ($_POST["category"] == "severe" ? " selected" : "") ?>>Urgent!</option>
    <option value="broadcast"<?= ($_POST["category"] == "broadcast" ? " selected" : "") ?>>Broadcasting</option>
    <option value="comic"<?= ($_POST["category"] == "comic" ? " selected" : "") ?>>Comics</option>
    <option value="event"<?= ($_POST["category"] == "event" ? " selected" : "") ?>>Event</option>
   </select>

   <b>Thread?</b>
   <select name="thread">
    <option value="">
    <option value="yes"<?= ($_POST['thread'] == 'yes' ? ' selected' : '') ?>>Yes</option>
    <option value="no"<?= ($_POST['thread'] == 'no' ? ' selected' : '') ?>>No</option>
   </select>
  </td>
 </tr>
 <tr>
  <td colspan="2" align="right">
   <input type="submit" name="submit" value="Preview" />&nbsp;<input type="submit" name="submit" value="Post" />
  </td>
 </tr>
</table>
</form>
<?php echo formatting_help(); ?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
