<?php
$require_petload = 'no';
$invisible = 'yes';
$AJAX = true;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';

$mailid = (int)$_POST['id'];
$to = trim($_POST['to']);

if(strlen($user['mailboxes']) > 0)
  $mailbox_folders = explode(',', $user['mailboxes']);
else
  $mailbox_folders = array();

if(in_array($to, $mailbox_folders) || $to == 'post')
{
  $command = '
    UPDATE `monster_mail`
    SET location=' . quote_smart($to) . '
    WHERE
      `idnum`=' . $mailid . ' AND
      `to`=' . quote_smart($user['user']) . '
    LIMIT 1
  ';
  $database->FetchNone($command, 'readmail.php');
}
else if($to == $user['email'])
{
  require_once 'commons/maillib.php';
  require_once 'commons/userlib.php';

  $mail = get_mail_byid($mailid);

  email_psymail($mail, $user['email']);
  delete_mail_by_id($mailid);
}
?>
