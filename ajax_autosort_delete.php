<?php
$require_petload = 'no';
$invisible = 'yes';
$AJAX = true;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';

$ruleid = (int)$_POST['ruleid'];

$command = 'DELETE FROM psypets_autosort WHERE userid=' . $user['idnum'] . ' AND idnum=' . $ruleid . ' LIMIT 1';
$database->FetchNone($command, 'deleting rule');

echo 'remrule:' . $ruleid;
?>
