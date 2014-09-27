<?php
$require_petload = 'no';
$invisible = 'yes';
$AJAX = true;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';

$mailid = (int)$_POST['id'];

$command = '
  UPDATE `monster_mail`
  SET `location`=\'Trash\'
  WHERE
    `idnum`=' . $mailid . ' AND
    `to`=' . quote_smart($user['user']) . '
  LIMIT 1
';

$database->FetchNone($command, 'readmail.php');
?>
