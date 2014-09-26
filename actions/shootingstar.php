<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

$updates = array();

$database->FetchNone('
  UPDATE monster_pets
  SET inspired=4
  WHERE
    user=' . quote_smart($user['user']) . '
    AND location=\'home\'
    AND sleeping=\'no\'
    AND dead=\'no\'
    AND zombie=\'no\'
    AND inspired<4
');

$affected_pets = $database->AffectedRows();

echo '<p>';

if($affected_pets > 1)
  echo 'You and your pets watch the star as it streaks across the sky and vanishes over the horizon.';
else if($affected_pets == 1)
  echo 'You and your pet watch the star as it streaks across the sky and vanishes over the horizon.';
else
  echo 'You watch the star as it streaks across the sky and vanishes over the horizon.';

echo '</p>';

delete_inventory_byid($this_inventory['idnum']);
?>
