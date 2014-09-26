<?php
if($okay_to_be_here !== true)
  exit();

if($_POST['action'] == 'addprofile')
{
  $user['profile_wall'] = 'snooowww.png';
  $command = 'UPDATE monster_users SET profile_wall=' . quote_smart($user['profile_wall']) . ",profile_wall_repeat='yes' WHERE idnum=" . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'itemaction.php?idnum=' . $this_inventory['idnum']);
  echo 'The Snow Machine hums as it is brought to life... <i>(Your profile background has been changed!)</i></p><p>';
}
else if($_POST['action'] == 'removeprofile')
{
  $user['profile_wall'] = '';
  $command = 'UPDATE monster_users SET profile_wall=' . quote_smart($user['profile_wall']) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'itemaction.php?idnum=' . $this_inventory['idnum']);
  echo 'As the Snow Machine powers down, the lights in the house get a little brighter.  Damn, that thing eats electricity.  <i>(Your profile background has been changed!)</i></p><p>';
}
?>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
<?php
if($user['profile_wall'] == 'snooowww.png')
{
?>
<p><input type="hidden" name="action" value="removeprofile" /><input type="submit" value="Turn off Snow Machine" style="width: 200px;" /></p>
<?php
}
else
{
?>
<p><input type="hidden" name="action" value="addprofile" /><input type="submit" value="Turn on Snow Machine" style="width: 200px;" /></p>
<?php
}
?>
</form>
