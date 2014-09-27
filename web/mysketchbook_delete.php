<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/sketchbooklib.php';

$id = (int)$_GET['id'];

delete_sketch($id, $user['idnum']);

header('Location: ./mysketchbook.php');
exit();
?>