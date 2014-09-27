<?php
$require_petload = 'no';
$invisible = 'yes';
$AJAX = true;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';

$idnum = (int)$_POST['id'];

$command = 'SELECT `to`,starred FROM monster_mail WHERE idnum=' . $idnum . ' LIMIT 1';
$mail = $database->FetchSingle($command, 'mail');

if($mail === false || $mail['to'] != $user['user'])
  die('mail does not exist');
else if($mail['starred'] == 'no')
  die('OK');
else
{
  $command = 'UPDATE monster_mail SET starred=\'no\' WHERE idnum=' . $idnum . ' LIMIT 1';
  $database->FetchNone($command, 'starring mail');
  die('OK');
}
?>
