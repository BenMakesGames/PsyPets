<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/sketchbooklib.php';

$id = (int)$_GET['id'];

set_shop_keep($user['idnum'], $id);

header('Location: ./mysketchbook.php');
exit();
?>
