<?php
if($okay_to_be_here !== true)
  exit();

$database->FetchNone("UPDATE monster_users SET graphic='custom/butterfairy.png' WHERE idnum=" . $user["idnum"] . " LIMIT 1");
?>
<p>The fairy speaks:  "When you are weary of battle, please come back to visit me!"</i></p>