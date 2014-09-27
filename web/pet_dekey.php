<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/petlib.php';

$pet = get_pet_byid((int)$_GET["id"]);

if($pet['keyid'] > 0 && $pet['user'] == $user['user'] && $pet['location'] == 'home')
{
  $command = "UPDATE monster_inventory SET location='home',user=" . quote_smart($user['user']) . ",changed='" . time() . "' WHERE idnum=" . $pet['keyid'] . ' LIMIT 1';
  $database->FetchNone($command, 'unequipping item');

  $command = 'UPDATE monster_pets SET keyid=0 WHERE idnum=' . $pet['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'marking pet as unequipped');
}

header('Location: ./myhouse.php');
?>
