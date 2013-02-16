<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/encryption.php';
require_once 'commons/formatting.php';
require_once 'commons/petactivitystats.php';

if(count($userpets) == 0)
{
  header('Location: /myhouse.php');
  exit();
}

$petid = (int)$_POST['petid'];

$pet = get_pet_byid($petid);

if($pet['user'] != $user['user'])
{
  $petid = $userpets[0]['idnum'];
  $pet = get_pet_byid($petid);
}

$note = trim($_POST['mininote']);

if(strlen($note) > 100)
  $note = substr($note, 0, 100);

fetch_none('
  UPDATE monster_pets
  SET mininote=' . quote_smart($note) . '
  WHERE idnum=' . $petid . '
  LIMIT 1
');

header('Location: /petprofile.php?petid=' . $petid);
?>
