<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_SAME = true;

$light_options = array(
  'walls/fallingblocks.png',
  'walls/arrows.png',
  'walls/greenstars.png',
  'walls/hexared.png',
  'walls/rings_pink.png',
);

if($_GET['step'] == 2 && $_GET['option'] == -1)
{
  $command = 'UPDATE monster_users SET profile_wall=\'\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'magic paintbrush - clear');

  echo 'The paintbrush lifts itself up, and begins to paint...</p><p><i>(Your profile background has been changed!)</i>';
}
else if($_GET['step'] == 2 && array_key_exists($_GET['option'], $light_options))
{
  $command = 'UPDATE monster_users SET profile_wall=\'' . $light_options[$_GET['option']] . '\',profile_wall_repeat=\'yes\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'magic paintbrush - color!');

  echo 'The paintbrush lifts itself up, and begins to paint...</p><p><i>(Your profile background has been changed!)</i>';
}
else
{
  echo 'What pattern should the paintbrush paint? (Click on the graphic you want to use.)';

  if($user['profile_wall'] != '')
  {
    echo '</p><ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&step=2&option=-1">Plain white</a></li></ul>';
    $first = '';
  }
  else
    $first = '</p>';

  foreach($light_options as $id=>$graphic)
  {
    echo $first . '<p><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&step=2&option=' . $id . '"><img src="gfx/' . $graphic . '" /></a>';
    $first = '</p>';
  }
}
?>
