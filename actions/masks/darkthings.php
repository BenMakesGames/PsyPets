<?php
if($okay_to_be_here !== true)
  exit();

$database->FetchNone("UPDATE monster_users SET graphic='../pets/thoughts.gif' WHERE idnum=" . $user["idnum"] . " LIMIT 1");
?>
<p><i>There's a distant scream as you slip the dark mask over your face.</i></p>