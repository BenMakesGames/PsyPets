<?php
namespace PsyPets;

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

if($_POST['action'] == 'Reset to Desikh')
{
  $command = 'UPDATE monster_pets SET graphic=\'desikh.gif\',protected=\'no\' WHERE idnum=' . $petid . ' LIMIT 1';
  $database->FetchNone($command, 'resetting pet graphic to desikh');
}

header('Location: /petprofile.php?petid=' . $petid);
?>
