<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

$idnum = create_random_pet($user['user']);

$command = 'UPDATE monster_pets SET graphic=\'turkey.png\' WHERE idnum=' . $idnum . ' LIMIT 1';
$database->FetchNone($command, 'making pet a turkey');
?>
<p>The egg wiggles a little, cracks, and after a couple more minutes of wiggling and cracking, finally breaks open.</p>
<p>Hm?  What's this?  It's a Turkey!?</p>
<p>...</p>
