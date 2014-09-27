<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/petlib.php';

$pet_field = 'special_digital';

if(count($userpets) == 0)
  echo '<p>You have no pet to give this to.</p>';
else
{
  $petid = (int)$_POST['petid'];

  if($petid > 0)
    $target_pet = get_pet_byid($petid);
  else
    $target_pet = array();

  if($target_pet['user'] != $user['user'] || $target_pet['dead'] != 'no' || $target_pet['zombie'] == 'yes' || $target_pet['location'] != 'home')
  {
?>
<p>Who will you feed this strange pill to?</p>
 <form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
 <p>
  <select name="petid">
<?php
    foreach($userpets as $this_pet)
    {
      if($this_pet['dead'] != 'no')
        echo '   <option disabled>' . $this_pet['petname'] . ' (is dead)</option>';
      else if($this_pet['zombie'] == 'yes')
        echo '   <option disabled>' . $this_pet['petname'] . ' (is a zombie)</option>';
      else
        echo '   <option value="' . $this_pet['idnum'] . '">' . $this_pet['petname'] . '</option>';
    }
?>
  </select> <input type="submit" name="submit" value="Give" />
 </p>
 </form>
<?php
  }
  else
  {
    delete_inventory_byid($this_inventory['idnum']);

    if($target_pet[$pet_field] == 'yes')
    {
      $command = 'UPDATE monster_pets SET ' . $pet_field . '=\'no\' WHERE idnum=' . $target_pet['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'granting pet ' . $power_description);
      
      echo '<p><a href="/petprofile.php?petid=' . $petid . '">' . $target_pet['petname'] . '</a> has lost the ability "Dreams in Digital"!</p>';
    }
    else
    {
      echo '<p>It seems to have no effect on ' . $target_pet['petname'] . '...</p>';
    }

    $AGAIN_WITH_ANOTHER = true;
  }
}
?>
