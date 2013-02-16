<?php
if($yes_yes_that_is_fine !== true)
  exit();

require_once 'commons/petlib.php';

if(count($userpets) == 0)
{
  echo "<i>You have no pets to cast this spell on.</i>\n";
  exit();
}
else
{
  $alive = false;

  foreach($userpets as $pet)
  {
    if($pet['dead'] == 'no')
    {
      $alive = true;
      break;
    }
  }

  if(!$alive)
  {
    echo "<i>You have no pet to give this to (dead pets cannot be targetted).</i>\n";
    exit();
  }
}

if($_POST['petid'] > 0 && (int)$_POST['petid'] == $_POST['petid'])
  $target_pet = get_pet_byid((int)$_POST['petid']);
else
  $target_pet = array();

if($target_pet['user'] != $user['user'] || $target_pet['dead'] != 'no' || $target_pet['changed'] == 'yes')
{
?>
Which pet will you cast this spell on?</p>
<form action="/myhouse/addon/shrine_spell.php?spell=<?= $_GET['spell'] ?>" method="post">
<p><select name="petid">
<?php
  for($i = 0; $i < count($userpets); ++$i)
    echo '<option value="' . $userpets[$i]['idnum'] . '">' . $userpets[$i]['petname'] . '</option>';
?>
</select>&nbsp;<input type="submit" name="submit" value="Cast!" /></p>
</form>
<p>
<?php
}
else
{
  $FINISHED_CASTING = true;

  $food = max_food($target_pet);
  $energy = max_energy($target_pet);

  gain_food($target_pet, $food);
  gain_energy($target_pet, $energy);

  save_pet($target_pet, array('food', 'energy'));

  echo $target_pet['petname'] . ' is sated.';
}
?>
