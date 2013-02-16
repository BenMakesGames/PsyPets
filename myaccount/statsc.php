<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/statlib.php';

record_stat($user['idnum'], 'Visited Your Statistics Page', 1);

header('Location: /myaccount/stats.php');
?>