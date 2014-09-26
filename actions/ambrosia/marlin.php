<?php
if($okay_to_be_here !== true)
  exit();

require_once "commons/petlib.php";

if(count($userpets) == 0)
{
  echo "<i>You have no pet to use this.</i>\n";
  exit();
}
else
{
  $alive = false;

  foreach($userpets as $pet)
  {
    if($pet["dead"] == 'no' && $pet['protected'] == 'no')
    {
      $alive = true;
      break;
    }
  }

  if(!$alive)
  {
    echo "<i>You have no pet to use this.  (Protected pets, dead pets, and pets in were form may not use this.)</i>\n";
    exit();
  }
}

if((int)$_POST['petid'] > 0)
  $target_pet = get_pet_byid((int)$_POST['petid']);
else
  $target_pet = array();

if($target_pet['user'] != $user['user'] || $target_pet['dead'] != 'no' || $target_pet['protected'] != 'no' || $target_pet['changed'] != 'no' || $target_pet['location'] != 'home')
{
?>
Which pet will sing the Call of the Marlin?</p>
<p><i>(Protected pets, dead pets, and pets in were form may not do this, and are not listed.  This item will permanently change your pet's appearance!)</i></p>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
<p><select name="petid">
<?php
  for($i = 0; $i < count($userpets); ++$i)
  {
    if($userpets[$i]['dead'] == 'no' && $userpets[$i]['protected'] == 'no')
      echo '   <option value="' . $userpets[$i]['idnum'] . '">' . $userpets[$i]['petname'] . "</option>\n";
  }
?>
</select>&nbsp;<input type="submit" name="submit" value="Sing" /></p>
</form>
<p>
<?php
}
else
{
  $command = 'UPDATE monster_pets SET protected=\'yes\',graphic=\'special/marlin.png\' WHERE idnum=' . $target_pet['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating pet graphic');
  
  echo 'As ' . $target_pet['petname'] . ' sings, a transformation begins to take place... ' . $target_pet['petname'] . ' has become a Marlin!';
}
?>
