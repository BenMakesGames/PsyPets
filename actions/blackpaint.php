<?php
if($okay_to_be_here !== true)
  exit();

$command = 'UPDATE monster_users SET profile_wall=\'paint_back.png\',profile_wall_repeat=\'horizontal\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
$database->FetchNone($command, 'stringing icicle lights');
?>
You splash black paint all over, well, your profile.</p>
<p>Somehow, the can is still full...
