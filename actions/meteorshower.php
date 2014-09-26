<?php
if($okay_to_be_here !== true)
  exit();

$deleted = false;

if($_POST['action'] == 'attack')
{
  $this_user = get_user_bydisplay($_POST['target'], 'display,activated,disabled,idnum,meteor');

  if($this_user == false)
    $errors[] = 'Could not find a resident named "' . $_POST['target'] . '"';
  else if($this_user['activated'] == 'no' || $this_user['disabled'] == 'yes')
    $errors[] = 'Could not find a resident named "' . $_POST['target'] . '"';
  else if($this_user['idnum'] == $user['idnum'])
    $errors[] = 'What?  You want to hit yourself with a meteor shower?  Don\'t be ridiculous!';
  else if($this_user['meteor'] == 'yes')
    $errors[] = $_POST['target'] . ' is already recovering from a meteor shower.';
  else
  {
    if(delete_inventory_byname($user['user'], 'Small Rock', 1, $this_inventory['location']) > 0)
    {
      $command = 'UPDATE monster_users SET meteor=\'yes\' WHERE idnum=' . $this_user['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'trashing profile');
    
      echo '<p>The Small Rock is reduced to dust and scattered into the wind...</p>' .
           '<p>A few moments later, you hear a series of terrible crashing noises!</p>' .
           '<ul><li><a href="residentprofile.php?resident=' . link_safe($_POST['target']) . '">Check out the damage!</a></li></ul>' .
           '<hr />';
    }
    else
      $errors[] = 'You don\'t have any Small Rocks in this room.';
  }
}

if(count($errors) > 0)
  echo '<ul><li class="failure">' . implode('</li><li class="failure">', $errors) . '</li></ul>';

?>
<p>Who will you summon a meteor shower on?  You will need one Small Rock to perform the attack.</p>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
<p>Target: <input type="text" name="target" /> <input type="hidden" name="action" value="attack" /><input type="submit" value="Attack!" /></p>
</form>
