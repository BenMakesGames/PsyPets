<?php
if($okay_to_be_here !== true)
  exit();

if(count($userpets) > 0)
{
  $database->FetchNone('UPDATE monster_users SET graphic=\'special-secret/asciiowl.png\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1');

  $petname = $userpets[array_rand($userpets)]['petname'];

  echo 'You wear the mask for a little while, clawing at things and screeching.  You finally, sheepishly take it off when ' . $petname . ' wanders by and gives you a puzzled look.';
}
else
  echo 'You hold the mask up to your face for a moment before putting it down again.';
?>
</p><p><i>(Your avatar has been changed.)</i>
