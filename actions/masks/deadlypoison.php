<?php
if($okay_to_be_here !== true)
  exit();

do
{
  $dead_avatar = 'dead/dead0' . mt_rand(1, 6) . '.png';
} while($user['graphic'] == $dead_avatar);

$command = 'UPDATE monster_users SET graphic=' . quote_smart($dead_avatar) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
$database->FetchNone($command, 'updating avatar');

delete_inventory_byid($this_inventory['idnum']);
?>
<p>You have been killed.</p>
<p><i>(Well, or more accurately, your avatar has been changed >_>)</i></p>
