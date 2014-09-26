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
    if($pet['dead'] == 'no' && $pet['changed'] == 'no')
    {
      $alive = true;
      break;
    }
  }

  if(!$alive)
  {
    echo "<i>You have no pet to give this to (dead and wereform pets will not drink the potion).</i>\n";
    exit();
  }
}

if($_POST["petid"] > 0 && (int)$_POST["petid"] == $_POST["petid"])
  $target_pet = get_pet_byid((int)$_POST["petid"]);
else
  $target_pet = array();

if($target_pet['user'] != $user['user'] || $target_pet['dead'] != 'no' || $target_pet['changed'] == 'yes')
{
?>
Which pet will drink <?= $this_inventory["itemname"] ?>?</p>
<form action="itemaction.php?idnum=<?= $_GET["idnum"] ?>" method="POST">
<p><select name="petid">
<?php
  for($i = 0; $i < count($userpets); ++$i)
  {
    echo "   <option value=\"" . $userpets[$i]["idnum"] . "\">" . $userpets[$i]["petname"] . "</option>\n";
  }
?>
</select>&nbsp;<input type="submit" name="submit" value="Give" /></p>
</form>
<p>
<?php
}
else
{
  $safety = max_safety($target_pet);
  $love   = max_love($target_pet);
  $esteem = max_esteem($target_pet);

  $old_food = $target_pet['food'];
  $old_energy = $target_pet['energy'];
  $target_pet['food'] = 1;
  $target_pet['energy'] = 1;

  gain_safety($target_pet, $safety);
  gain_love($target_pet, $love);
  gain_esteem($target_pet, $esteem);

  $target_pet['food'] = $old_food;
  $target_pet['energy'] = $old_energy;
  
  save_pet($target_pet, array('food', 'energy', 'safety', 'love', 'esteem'));

  delete_inventory_byid($this_inventory['idnum']);
  
  echo $target_pet['petname'] . ' drinks the potion and smiles a big smile.';

  $AGAIN_WITH_ANOTHER = true;
}
?>
