<?php
$whereat = 'petmarket';
$wiki = 'Pet_Market';

require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';

if($user['breeder'] != 'yes')
{
  header('Location: /breederslicense.php?dialog=2');
  exit();
}

$listid = (int)$_GET['id'];

$command = 'DELETE FROM psypets_pet_market WHERE idnum=' . $listid . ' AND ownerid=' . $user['idnum'] . ' LIMIT 1';
$database->FetchNone($command, 'unlisting pets');

if($database->AffectedRows() > 0)
  header('Location: /petmarket.php?msg=104');
else
  header('Location: /petmarket.php');
?>
