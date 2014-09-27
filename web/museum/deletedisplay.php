<?php
require_once 'commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/museumlib.php';

$id = (int)$_GET['id'];

$command = 'DELETE FROM psypets_museum_displays WHERE idnum=' . $id . ' AND userid=' . $user['idnum'] . ' LIMIT 1';
fetch_none($command, 'deleting display');

header('Location: /museum/displayeditor.php');
?>
