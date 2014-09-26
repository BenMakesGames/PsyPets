<?php
if($okay_to_be_here !== true)
  exit();

$database->FetchNone("UPDATE monster_inventory SET message='',message2='',changed=$now WHERE (message LIKE '%vicious%' OR message LIKE '%ruff%' OR message2 LIKE '%vicious%' OR message2 LIKE '%ruff%') AND user=" . quote_smart($user["user"]));

$num = $database->AffectedRows();
?>
<p><?= $num ?> <?= $num == 1 ? "item has" : "items have" ?> been disinfected.</p>
