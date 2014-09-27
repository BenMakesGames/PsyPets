<?php
$child_safe = false;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/formatting.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/checkpet.php';
require_once 'commons/userlib.php';
require_once 'commons/utility.php';
require_once 'commons/grouplib.php';

$groupid = (int)$_GET['id'];
$group = get_group_byid($groupid);

if($group === false)
{
  header('Location: ./directory.php');
  exit();
}

$ranks = get_group_ranks($groupid);
$members = explode(',', $group['members']);
$organizer = get_user_byid($group['leaderid'], 'idnum,display,graphic');

$a_member = is_a_member($group, $user['idnum']);

if($a_member)
{
  $invites = get_invites_bygroup($groupid);
  $rankid = get_member_rank($group, $user['idnum']);
  $can_mail = (rank_has_right($ranks, $rankid, 'groupmail') || $group['leaderid'] == $user['idnum']);
}
else
  $can_mail = false;

if(!$can_mail)
{
  header('Location: ./grouppage.php');
  exit();
}

$group_mail_sig = "\n\n" . '{//}{i}{9}This message was sent to all members of the ' . $group['name'] . ' Group{/}{/}';

if($_POST['submit'] == 'Preview')
{
  if(strlen($_POST['message']) <= 2)
    $error_message[] = 38;
  else
  {
    foreach($BANNED_URLS as $url)
    {
      if(strpos($_POST['message'], $url) !== false)
        $errors[] = '<span class="failure">Linking to ' . $url . ' is not allowed.  (<a href="/help/bannedurls.php">Why?</a>)</span>';
    }
  }
}

if($_POST['submit'] == 'Send Mail')
{
  $mail_success = false;

  $_POST['subject'] = trim($_POST['subject']);
  $_POST['message'] = trim($_POST['message']);

  if(strlen($_POST['subject']) <= 1)
    $errors[] = '<span class="failure">The message needs a subject line...';
  else if(strlen($_POST['subject']) > 100)
    $errors[] = '<span class="failure">The message cannot be longer than 100 characters...</span>';

  if(strlen($_POST['message']) <= 2)
    $errors[] = '<span class="failure">The message needs... a message... &gt;_&gt;</span>';
  else
  {
    foreach($BANNED_URLS as $url)
    {
      if(strpos($_POST['message'], $url) !== false)
        $errors[] = '<span class="failure">Linking to ' . $url . ' is not allowed.  (<a href="/help/bannedurls.php">Why?</a>)</span>';
    }
  }

  if(count($error_message) == 0 && count($errors) == 0)
  {
    $_POST['message'] .= $group_mail_sig;
  
    $message = nl2br(htmlspecialchars($_POST['message']));
    $subject = nl2br(htmlspecialchars($_POST['subject']));

    $members = get_group_member_ids($group['members']);

    psymail_group_byarray($members, $user['user'], $subject, $message);

    $user['newmail'] = 'yes';

    $errors[] = '<span class="success">Message sent successfully!</span>';

    $_POST = array();
  }
}

include 'commons/html.php';
?>
 <head>
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $group['name'] ?> &gt; Send Announcement</title>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><?= $group['name'] ?>  &gt; Send Announcement</h4>
<?php
$activetab = 'groupmail';
include 'commons/grouptabs.php';

if(count($errors) > 0)
  echo '<ul><li>' . implode('</li><li>', $errors) . '</li></ul>';
?>
     <p>This will PsyMail every member of the group.  Even you.  Using this all the time might make your group members hate you.</p>
<?php
if($_POST['submit'] == 'Preview')
{
?>
<h4>Preview</h4>
<p><table class="preview"><tr><td>
<?= format_text($_POST['message'] . $group_mail_sig, false) ?>
</td></tr></table></p>
<h4>Compose Mail</h4>
<?php
}
?>
     <form action="groupmail.php?id=<?= $groupid ?>" method="post">
<table>
 <tr>
  <th>From:</th>
  <td><?= $user['display'] ?></td>
 </tr>
 <tr>
  <th>To:</th>
  <td><?= $group['member_count'] ?> group members</td>
 </tr>
 <tr>
  <th>Subject:</th>
  <td><input name="subject" value="<?= $_POST['subject'] ?>" style="width:440px;" /></td>
 </tr>
 <tr>
  <th colspan="2">Message:</td>
 </tr>
 <tr>
  <td colspan="2">
   <textarea name="message" cols="50" rows="10" style="width:500px;"><?= str_replace(array('<', '>'), array('&lt;', '&gt;'), $_POST['message']) ?></textarea>
  </td>
 </tr>
 <tr>
  <td colspan="2" align="right">
   <input type="submit" name="submit" value="Preview" /> <input type="submit" name="submit" value="Send Mail" />
  </td>
 </tr>
</table>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
