<?php
if($okay_to_be_here !== true)
  exit();

$database->FetchNone("UPDATE monster_users SET graphic='custom/zinnia.png' WHERE idnum=" . $user["idnum"] . " LIMIT 1");
?>
<p><i>The seed begins to grow...</i></p>