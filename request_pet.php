<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/globals.php';
require_once 'commons/petlib.php';

if($user['breeder'] == 'yes')
{
  header('Location: /myhouse.php');
  exit();
}

// confirm that the user has no pets
$any_pet = $database->FetchSingle('SELECT * FROM monster_pets WHERE user=' . quote_smart($user['user']) . ' LIMIT 1');

if($any_pet === false)
{
	$petgfx = get_global('petgfx');

  // make a pet!
  $petid = create_random_offspring($user['user'], 1, $petgfx);
  $command = 'UPDATE monster_pets SET protected=\'yes\' WHERE idnum=' . $petid . ' LIMIT 1';
  $database->FetchNone($command);
}

header('Location: /myhouse.php');
exit();
