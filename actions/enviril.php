<?php
if($okay_to_be_here !== true)
  exit();

require_once "commons/grammar.php";

$ok_pets = array();
foreach($userpets as $some_pet)
{
  if($some_pet['dead'] == 'no' && $some_pet['prolific'] == 'no' && $some_pet['zombie'] == 'no' && $some_pet['location'] == 'home')
    $ok_pets[] = $some_pet;
}

if(count($ok_pets) == 0)
  echo '<p><i>(You may only feed the Loaf to pets that have been fixed.  Oh, and the pet needs to be alive, too.)</i></p>';
else
{
  if($_POST['petid'] > 0 && (int)$_POST['petid'] == $_POST['petid'])
    $target_pet = get_pet_byid((int)$_POST['petid']);
  else
    $target_pet = array();

  if($target_pet['user'] == $user['user'] && $target_pet['dead'] == 'no' && $target_pet['prolific'] == 'no' && $target_pet['location'] == 'home' && $target_pet['zombie'] == 'no')
  {
?>
<p><?= $target_pet['petname'] ?> eats the Loaf in one bite.</p>
<?php
    $command = "UPDATE monster_pets SET prolific='yes' WHERE idnum=" . $target_pet['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'enviriling pet');

    delete_inventory_byid($this_inventory['idnum']);
  }
  else
  {
?>
<p>Which pet will you feed the Loaf to?</p>
<p><i>(You may only feed it to pets that have been fixed.  Oh, and the pet needs to be alive, too.)</i></p>
<form action="itemaction.php?idnum=<?= $this_inventory["idnum"] ?>" method="post">
<p>
 <select name="petid">
<?php
    foreach($ok_pets as $this_pet)
      echo '<option value="' . $this_pet['idnum'] . '">' . $this_pet['petname'] . '</option>';
?>
 </select>&nbsp;<input type="submit" name="submit" value="Feed" />
</p>
</form>
<?php
  }
}
?>
