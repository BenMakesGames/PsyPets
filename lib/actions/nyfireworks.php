<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_SAME = true;

$light_options = array(
  'profiles/fireworks1.png',
  'profiles/fireworks2.png',
);

if(in_array($user['profile_wall'], $light_options))
{
  $command = 'UPDATE monster_users SET profile_wall=\'\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'unstringing icicle lights');

  echo 'The fireworks... um... stop.  Yes.  That also makes sense.</p><p><i>(Your profile background has been changed!)</i>';
}
else
{
  if($_GET['step'] == 2 && array_key_exists($_GET['option'], $light_options))
  {
    $command = 'UPDATE monster_users SET profile_wall=\'' . $light_options[$_GET['option']] . '\',profile_wall_repeat=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'stringing icicle lights');

    echo 'You fire off some fireworks which... um... stay there.  Yes.  That makes sense.</p><p><i>(Your profile background has been changed!)</i>';
  }
  else
  {
    echo 'Which fireworks will you launch? (Click on the graphic you want to use.)';

    foreach($light_options as $id=>$graphic)
      echo '</p><p><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&step=2&option=' . $id . '"><img src="gfx/' . $graphic . '" border="0" /></a>';
  }
}
?>
