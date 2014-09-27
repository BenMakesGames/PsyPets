<?php
 if($okay_to_be_here !== true)
   exit();

 $database->FetchNone("UPDATE monster_users SET graphic='custom/ankh.png' WHERE idnum=" . $user["idnum"] . " LIMIT 1");
?>
<p><i>You put on the ankh necklace.</i></p>