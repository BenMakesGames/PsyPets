<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';

if($user['license'] == 'no')
{
  header('Location: ./storage.php');
  exit();
}

$id = (int)$_GET['id'];

$command = 'DELETE FROM psypets_custom_item_store WHERE idnum=' . $id . ' AND ownerid=' . $user['idnum'] . ' LIMIT 1';
$database->FetchNone($command, 'deleting custom item store entry');

header('Location: ./myfavorstore.php');
exit();
?>