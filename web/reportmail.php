<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/maillib.php';
require_once 'commons/messages.php';

if(strlen($user['mailboxes']) > 0)
  $mailbox_folders = explode(',', $user['mailboxes']);
else
  $mailbox_folders = array();

$mailid = (int)$_GET['idnum'];

$mail = get_mail_byid($mailid);

if($mail['to'] != $user['user'])
{
  header('Location: ./post.php');
  exit();
}  

if($_POST['action'] == 'report')
{
  $author = get_user_byuser($mail['from'], 'idnum,display');

  $note = trim($_POST['notes']);
  
  if($note != '')
    $message = trim($_POST['notes']) . '<hr />';


  $message .= '<hr /><b>From:</b> ' . $author['display'] . ' (#' . $author['idnum'] . ')<br /><b>To:</b> ' . $user['display'] . ' (#' . $user['idnum'] . ')<br /><b>Subject:</b> ' . $mail['subject'] . '<br /><br />' . $mail['message'];

  $command = 'INSERT INTO psypets_abusereports (timestamp, type, threadid, reporter, comment) VALUES ' .
             '(' . $now . ', \'mail\', ' . $mailid . ', ' . $user['idnum'] . ', ' . quote_smart($message) . ')';

  $database->FetchNone($command, 'logging abuse report');

  mail($SETTINGS['author_email'], 'PsyMail notification: a PsyMail has been reported for abuse!', 'original text: ' . $mail['text'] . $mail['body'] . $mail['message'], "MIME-Version: 1.0\nContent-type: text/html; charset=utf-8\nFrom: " . $SETTINGS['site_mailer']);

  header('Location: ./readmail.php?mail=' . $maild . '&msg=100');
  exit();
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s Mailbox<?= $mail['location'] == 'post' ? '' : ' &gt; ' . $mail['location'] ?> &gt; <?= $mail['subject'] ?> &gt; Report</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="post.php"><?= $user['display'] ?>'s Mailbox</a><?= $mail['location'] == 'post' ? '' : ' &gt; <a href="post.php?mailbox=' . $mail['location'] . '">' . $mail['location'] . '</a>' ?> &gt; <a href="readmail.php?mail=<?= $mailid ?>"><?= $mail['subject'] ?></a> &gt; Report</h4>
<?php include 'commons/abuseexamples.php' ?>
     <h5>Comment (optional)</h5>
     <form action="reportmail.php?idnum=<?= $mailid ?>" method="post">
     <p><textarea name="notes" style="width:300px;"></textarea></p>
     <p><input type="hidden" name="action" value="report" /><input type="submit" value="Report Abuse" class="bigbutton"></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
