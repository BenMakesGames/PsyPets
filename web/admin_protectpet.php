<?php
// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";

if($user['admin']['manageaccounts'] != 'yes')
{
  header('Location: ./404.php');
  exit();
}

$petid = (int)$_GET['id'];

$command = 'SELECT * ' .
           'FROM `monster_pets` ' .
           'WHERE idnum=' . $petid . ' LIMIT 1';
$this_pet = $database->FetchSingle($command, 'petprofile.php');

if($this_pet === false)
{
  header('Location: ./directory.php');
  exit();
}

if($_POST['action'] == 'Protect Pet')
{
  $command = 'UPDATE monster_pets SET protected=\'yes\' WHERE idnum=' . $petid . ' LIMIT 1';
  $database->FetchNone($command, 'marking pet as protected');
}
else if($_POST['action'] == 'Unprotect Pet')
{
  $command = 'UPDATE monster_pets SET protected=\'no\' WHERE idnum=' . $petid . ' LIMIT 1';
  $database->FetchNone($command, 'marking pet as unprotected');
}

header('Location: /petprofile.php?petid=' . $petid);
?>
