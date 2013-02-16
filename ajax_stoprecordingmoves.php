<?php
$require_petload = 'no';
$invisible = 'yes';
$AJAX = true;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';

$command = 'UPDATE monster_users SET autosorterrecording=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
$database->FetchNone($command, 'stopping recording');
?>
