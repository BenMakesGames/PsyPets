<?php
if($okay_to_be_here !== true)
  exit();

require_once "commons/petlib.php";

if(count($userpets) == 0)
{
  echo "<i>You have no pet to give this to.</i>\n";
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
    echo "<i>You have no pet to give this to.  (Dead pets and protected pets may not eat this.)</i>\n";
    exit();
  }
}

if((int)$_POST['petid'] > 0)
  $target_pet = get_pet_byid((int)$_POST['petid']);
else
  $target_pet = array();

if($target_pet['user'] != $user['user'] || $target_pet['dead'] != 'no' || $target_pet['protected'] != 'no' || $target_pet['location'] != 'home')
{
?>
<p>Which pet will eat the <?= $this_inventory['itemname'] ?>?</p>
<p><i>(Dead pets and protected pets may not eat it, and are not listed.  This item will permanently change your pet's appearance, and is consumed after one use!)</i></p>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
<p><select name="petid">
<?php
  for($i = 0; $i < count($userpets); ++$i)
  {
    if($userpets[$i]['dead'] == 'no' && $userpets[$i]['protected'] == 'no')
      echo '   <option value="' . $userpets[$i]['idnum'] . '">' . $userpets[$i]['petname'] . "</option>\n";
  }
?>
</select>&nbsp;<input type="submit" name="submit" value="Give" /></p>
</form>
<?php
}
else
{
  $command = 'UPDATE monster_pets SET protected=\'yes\',graphic=\'special/panda.png\' WHERE idnum=' . $target_pet['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating pet graphic');

  delete_inventory_byid($this_inventory['idnum']);
  
  echo $target_pet['petname'] . ' gobbles up the ' . $this_inventory['itemname'] . ' in no time.</p><p>For a while nothing seems to happen, but then, slowly at first, ' . $target_pet['petname'] . '\'s features begin to deform - white and black fur, claws, short ears, patches around the eyes - until finally, ' . $target_pet['petname'] . ' has become a panda!';
}
?>
