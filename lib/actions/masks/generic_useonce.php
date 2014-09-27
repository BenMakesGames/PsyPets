<?php
 if($okay_to_be_here !== true)
   exit();

 $database->FetchNone("UPDATE monster_users SET graphic=" . quote_smart($action_info[2]) . " WHERE idnum=" . $user["idnum"] . " LIMIT 1");

 delete_inventory_byid($this_inventory["idnum"]);
?>
<i><?= $action_info[3] ?></i></p>
<p><i>(Your avatar has been changed.)</i>
