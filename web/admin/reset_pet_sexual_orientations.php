<?php
require_once 'commons/init.php';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';

$pets = $database->FetchMultiple(('SELECT idnum,gender FROM monster_pets');

foreach($pets as $pet)
{
	if($pet['gender'] == 'male')
	{
		$pet['attracted_to_males'] = mt_rand(0, mt_rand(0, 100));
		$pet['attracted_to_females'] = mt_rand(mt_rand(0, 50), mt_rand(50, 100));
	}
	else if($pet['gender'] == 'female')
	{
		$pet['attracted_to_males'] = mt_rand(mt_rand(0, 50), mt_rand(50, 100));
		$pet['attracted_to_females'] = mt_rand(0, mt_rand(0, 100));
	}

  $database->FetchNone(('
    UPDATE monster_pets
    SET
      attraction_to_males=' . $pet['attracted_to_males'] . ',
      attraction_to_females=' . $pet['attracted_to_females'] . '
    WHERE idnum=' . $pet['idnum'] . '
    LIMIT 1
  ');
}
?>