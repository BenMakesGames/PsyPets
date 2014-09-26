<?php
if($okay_to_be_here !== true)
  exit();

require_once "commons/petlib.php";

if(count($userpets) == 0)
{
  echo "<i>You have no pet to give this to.</i>\n";
}

if($_POST["petid"] > 0 && (int)$_POST["petid"] == $_POST["petid"])
  $target_pet = get_pet_byid((int)$_POST["petid"]);
else
  $target_pet = array();

if($target_pet["user"] != $user["user"] || $target_pet['location'] != 'home')
{
?>
 <p>The engraving on the back of this amulet reads:</p>
 <p><i>According to an old wives' tale, the dragon thinks first with its teeth.  To this the dragon replies, "A sharp mind serves one just as well as a strong claw."</i></p>
 <p>You may sacrifice the Tooth of Ramoth to aid in your pet's quest for knowledge.</p>
 <form action="itemaction.php?idnum=<?= $_GET["idnum"] ?>" method="POST">
 <p>
  <select name="petid">
<?php
  for($i = 0; $i < count($userpets); ++$i)
  {
    echo "   <option value=\"" . $userpets[$i]["idnum"] . "\">" . $userpets[$i]["petname"] . "</option>\n";
  }
?>
  </select>&nbsp;<input type="submit" name="submit" value="Sacrifice Tooth of Ramoth" />
 </p>
 </form>
 <p>
<?php
}
else
{
  $l = $target_pet["level"];

  if($target_pet["str"] > 1)
  {
/*
    $target_pet["int"]++;
    $target_pet["str"]--;
*/
    $command = "UPDATE monster_pets SET str=str-1,monster_pets.int=monster_pets.int+1 WHERE idnum=" . $target_pet["idnum"] . " LIMIT 1";
    $database->FetchNone($command, 'altering pet stats');
?>
   <p>The Tooth of Ramoth disappears in a flash of blinding light.  Your pet collapses, suddenly feeling as though the life has been drained out of <?= t_pronoun($target_pet["gender"]) ?>.  A moment later <?= pronoun($target_pet["gender"]) ?> leaps to <?= p_pronoun($target_pet["gender"]) ?> feet and starts writing a novel.</p>
<?php

    delete_inventory_byid($this_inventory['idnum']);
  }
  else
  {
?>
   <p>There is a blinding flash of light.  You utter a cry of anguish as the Tooth of Ramoth sears the flesh of your palm.  Before realizing you've dropped it, the amulet clatters dully on the ground.</p>
<?php
  }
}
?>
