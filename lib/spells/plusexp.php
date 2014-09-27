<?php
if($yes_yes_that_is_fine !== true)
  exit();

require_once 'commons/petlib.php';

if(count($userpets) == 0)
{
  echo '<i>You have no pets to cast this spell on.</i>';
  exit();
}
else
{
  $alive = false;

  foreach($userpets as $pet)
  {
    if($pet['dead'] == 'no' && $pet['zombie'] == 'no')
    {
      $alive = true;
      break;
    }
  }

  if(!$alive)
  {
    echo '<i>You have no pet to give this to (dead pets cannot be targetted).</i>';
    exit();
  }
}

if($_POST['petid'] > 0 && (int)$_POST['petid'] == $_POST['petid'])
  $target_pet = get_pet_byid((int)$_POST['petid']);
else
  $target_pet = array();

if($target_pet['user'] != $user['user'] || $target_pet['dead'] != 'no' || $target_pet['zombie'] != 'no' || $target_pet['changed'] == 'yes' || $target_pet['location'] != 'home')
{
?>
<p>Which pet will you cast this spell on?</p>
<form action="/myhouse/addon/shrine_spell.php?spell=<?= $_GET['spell'] ?>" method="post">
<p><select name="petid">
<?php
  for($i = 0; $i < count($userpets); ++$i)
  {
    if($userpets[$i]['dead'] == 'no' && $userpets[$i]['zombie'] == 'no')
      echo '<option value="' . $userpets[$i]['idnum'] . '">' . $userpets[$i]['petname'] . '</option>';
  }
?>
</select>&nbsp;<input type="submit" name="submit" value="Cast!" /></p>
</form>
<?php
}
else
{
  $FINISHED_CASTING = true;

  echo 'A ray of light shines on ' . $target_pet['petname'] . ' for a moment before fading.';

  if($target_pet['inspired'] < 72)
    $database->FetchNone('UPDATE monster_pets SET inspired=inspired+' . mt_rand(4, 8) . ' WHERE idnum=' . $target_pet['idnum'] . ' LIMIT 1');
}
?>
