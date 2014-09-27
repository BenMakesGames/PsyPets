<?php
if($okay_to_be_here !== true)
	exit();

$database->FetchNone("UPDATE monster_users SET graphic='custom/artemisa.png' WHERE idnum=" . $user["idnum"] . " LIMIT 1");
?>
<p><i>Fond memories of the events leading to that Christmas hangover...</i></p>