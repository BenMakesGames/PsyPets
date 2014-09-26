<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_SAME = true;

$light_options = array(
  'iciclelights.gif',
  'iciclelightscolored.gif',
  'blinkylights.gif',
);

if(in_array($user['profile_wall'], $light_options) || $user['profile_wall'] == 'icicle_back.png')
{
  $command = 'UPDATE monster_users SET profile_wall=\'\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'unstringing icicle lights');

  echo 'You take the lights back down, and promptly tangle them up.</p><p><i>(Your profile background has been changed!)</i>';
}
else
{
  if($_GET['step'] == 2 && array_key_exists($_GET['option'], $light_options))
  {
    $command = 'UPDATE monster_users SET profile_wall=\'' . $light_options[$_GET['option']] . '\',profile_wall_repeat=\'horizontal\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'stringing icicle lights');

    echo 'After a good deal of untangling you manage to string the lights up.</p><p><i>(Your profile background has been changed!)</i>';
  }
  else
  {
    echo 'What style lights will you hang? (Click on the graphic you want to use.)';

    foreach($light_options as $id=>$graphic)
      echo '</p><p><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&step=2&option=' . $id . '"><img src="gfx/' . $graphic . '" border="0" /></a>';
  }
}
?>
