<?php
require_once 'commons/formatting.php';

function get_mail_byid($idnum)
{
  $command = 'SELECT * FROM monster_mail WHERE idnum=' . (int)$idnum . ' LIMIT 1';
  return fetch_single($command, 'fetching mail');
}

function mark_mail_replied($idnum)
{
  $command = 'UPDATE monster_mail SET replied=\'yes\' WHERE idnum=' . (int)$idnum . ' LIMIT 1';
  fetch_none($command, 'marking mail #' . $idnum . ' as replied-to');
}

function delete_mail_by_id($idnum)
{
  $delete_command = 'DELETE FROM monster_mail WHERE idnum=' . (int)$idnum . ' LIMIT 1';
  fetch_none($delete_command, 'deleting psymail #' . $idnum);
}

function email_psymail(&$mail, $email)
{
	global $SETTINGS;

  $sender = get_user_byuser($mail['from'], 'display');
  if($sender === false)
    $sender['display'] = 'Departed Resident';

  $headers = 'Content-type: text/html; charset=utf-8' . "\n" .
             'To: ' . $email . "\n" .
             'From: ' . $sender['display'] . ' <' . $SETTINGS['site_mailer'] . '>' . "\n" .
             "\n";
    
  $mail['message'] = '<i>Original postdate: ' . date('r', $mail['date']) . "</i><br />\n<br />\n" . $mail['message'];
  return mail($email, $mail['subject'], format_text($mail['message']), $headers);
}
?>
