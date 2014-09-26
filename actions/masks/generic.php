<?php
if($okay_to_be_here !== true)
  exit();

$database->FetchNone('UPDATE monster_users SET graphic=' . $database->Quote($action_info[2]) . ' WHERE idnum=' . (int)$user['idnum'] . ' LIMIT 1');
?>
<i><?= $action_info[3] ?></i></p>
<p><i>(Your avatar has been changed.)</i>
