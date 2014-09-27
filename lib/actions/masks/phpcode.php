<?php
if($okay_to_be_here !== true)
  exit();

$database->FetchNone("UPDATE monster_users SET graphic='custom/myhouse.png' WHERE idnum=" . $user["idnum"] . " LIMIT 1");
?>
<i>There's a flicker of pixel artifacts in the surrounding area.</i></p>
<p><i>(Your avatar has been changed.)</i>
