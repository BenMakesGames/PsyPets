<?php
if($okay_to_be_here !== true)
  exit();

if($_GET['step'] == 2)
{
  $user['profile_wall'] = 'walls/leaves.png';
  $command = 'UPDATE monster_users SET profile_wall=' . quote_smart($user['profile_wall']) . ",profile_wall_repeat='yes' WHERE idnum=" . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'changing wallpaper');

  echo '<p>Leaves go flying <em>everywhere</em>!  (But mostly your profile :P)</p>' .
       '<ul><li><a href="residentprofile.php?resident=' . link_safe($user['display']) . '">Ooh!  Ooh!  I wanna see it!  I wanna see my profile!</a></li></ul>';
}
else
{
  echo '<p>Jumping in the Pile of Leaves will undoubtedly change your profile background!  But it will <strong>never</strong> destroy your Pile of Leaves, so have fun :)</p>' .
       '<ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&step=2">Jump! (for reals this time!)</a></li></ul>';
}
?>
