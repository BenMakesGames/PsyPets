<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_SAME = true;

if(count($userpets) == 0)
  echo "<i>You have no pet to use this on.</i>\n";
else
{
  if($_POST['petid'] > 0 && (int)$_POST['petid'] == $_POST['petid'])
    $target_pet = get_pet_byid((int)$_POST['petid']);
  else
    $target_pet = array();

  if($target_pet['user'] == $user['user'] && $target_pet['dead'] == "no" && $target_pet['sleeping'] == "no" && $target_pet['location'] == 'home')
  {
?>
You swing the mace, carefully avoiding a hit on <?= $target_pet['petname'] ?> who falls asleep immediately.
<?php
    $database->FetchNone("UPDATE monster_pets SET sleeping='yes' WHERE idnum=" . $target_pet['idnum'] . " LIMIT 1");
  }
  else
  {
?>
 Which pet will you target?</p>
 <p><i>Sleeping and dead pets cannot be targetted, and are excluded from the list below.</i></p>
 <form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
 <p>
  <select name="petid">
<?php
    for($i = 0; $i < count($userpets); ++$i)
    {
      if($userpets[$i]['dead'] == "no" && $userpets[$i]['sleeping'] == "no")
        echo "   <option value=\"" . $userpets[$i]["idnum"] . "\">" . $userpets[$i]['petname'] . "</option>\n";
    }
?>
  </select>&nbsp;<input type="submit" name="submit" value="Swing" />
 </p>
 </form>
 <p>
<?php
  }
}
?>
