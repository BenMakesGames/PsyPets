<?php
$whereat = 'home';
$wiki = 'My_House';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';

$command = 'UPDATE monster_users SET no_hours_fool=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
$database->FetchNone($command, 'good to go for house hours');

header('Location: /incoming.php');
