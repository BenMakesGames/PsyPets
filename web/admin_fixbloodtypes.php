<?php
require_once "commons/dbconnect.php";
require_once 'commons/petlib.php';

$command = 'SELECT idnum FROM monster_pets WHERE bloodtype=\'\'';
$pets = $database->FetchMultiple($command, 'fetching pets without bloodtype');

echo '<ul>';

foreach($pets as $pet)
{
  echo '<li>Assigning pet #' . $pet['idnum'] . ' a bloodtype... ';

  $command = 'UPDATE monster_pets SET bloodtype=\'' . random_blood_type() . '\' WHERE idnum=' . $pet['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating pet #' . $pet['idnum']);

  echo 'done!</li>';
}

echo '</ul>';