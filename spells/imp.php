<?php
if($yes_yes_that_is_fine !== true)
  exit();

$FINISHED_CASTING = true;

$petid = create_random_pet($user['user']);
  
$command = 'UPDATE monster_pets SET graphic=\'imp.png\', ' .
  'gender=\'male\',prolific=\'no\' WHERE idnum=' . $petid . ' LIMIT 1';

fetch_none($command, 'creating imp');

echo '<p>A glowing red portal opens up, out of which steps an imp.</p>' .
     '<p>It takes a look around, squeaks, and waddles into the house.</p>';
?>
  