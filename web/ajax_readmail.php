<?php
$require_petload = 'no';
$invisible = 'yes';
$AJAX = true;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/maillib.php';

$mailid = (int)$_POST['id'];

$mail = get_mail_byid($mailid);

if($mail['to'] != $user['user'])
  die('This mail does not exist?!  Reload the page and try again.');

if(strlen($user['mailboxes']) > 0)
  $mailbox_folders = explode(',', $user['mailboxes']);
else
  $mailbox_folders = array();

if($mail['new'] == 'yes')
{
  $command = '
    UPDATE monster_mail
    SET `new`=\'no\'
    WHERE idnum=' . $mailid . '
    LIMIT 1
  ';
  $database->FetchNone($command, 'readmail.php');
}

  if($now_month == 1 && $now_day == 18 && $now_year == 2012)
  {
    $mail['message'] = '{link http://' . $SETTINGS['site_domain'] . '/viewthread.php?threadid=72226 CENSORED}';
  }
?>
<p><?= format_text($mail['message']) ?></p>
<p>
<?php
if($mail['location'] == 'Trash')
  echo '<button disabled="disabled" style="width:100px;">Delete</button>';
else
  echo '<button onclick="deletemail(', $mailid , '); return false;" style="width:100px;">Delete</button>';
?>
 <button onclick="location.href='writemail.php?replyto=<?= $mailid ?>'; return false;" style="width:100px;">Reply</button>
 <button onclick="location.href='writemail.php?forward=<?= $mailid ?>'; return false;" style="width:100px;">Forward</button>
 <button onclick="movemail(<?= $mailid ?>); return false;" style="width:100px;">Move To</button>
 <select name="whereto" id="whereto<?= $mailid ?>" style="width:100px;">
 <?php
if($mail['location'] != 'post')
  echo '<option value="post">Inbox</option>';

foreach($mailbox_folders as $folder)
{
  if($mail['location'] == $folder)
    continue;

  echo '  <option value="' . $folder . '">' . $folder . '</option>';
}

echo '  <option value="' . $user['email'] . '">' . $user['email'] . '</option>';
?>
 </select>
 <span style="float:right;"><a href="/reportmail.php?idnum=<?= $mailid ?>"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/forum/report.png" alt="" width="11" height="16" class="inlineimage" /> Report</a></span>
</p>
<?php
if($mail['attachments'] > 0)
  echo '<p>(<i>' . $mail['attachments'] . ' item' . ($mail['attachments'] == 1 ? ' was' : 's were') . ' sent along with this letter.  Find ' . ($mail['attachments'] == 1 ? 'it' : 'them') . ' in <a href="incoming.php">Incoming</a>.)</i></p>';
?>
