<?php
if($okay_to_be_here !== true)
  exit();

$database->FetchNone("UPDATE monster_users SET graphic='custom/mum.png' WHERE idnum=" . $user["idnum"] . " LIMIT 1");
?>
<p><i>You nudge the glasses in to place.</i></p>