<?php
if($okay_to_be_here !== true)
  exit();

require_once "commons/petlib.php";

$valid_pets = array();

if(count($userpets) == 0)
{
  echo "<i>You do not have a pet which this can be applied to.</i>\n";
  exit();
}
else
{
  $alive = false;

  foreach($userpets as $pet)
  {
    if($pet["dead"] == "no" && $pet["graphic"] == "desikh.gif")
    {
      $valid_pets[] = $pet;
      $alive = true;
    }
  }

  if(!$alive)
  {
    echo "<i>You do not have a pet which this can be applied to.</i>\n";
    exit();
  }
}

if($_POST["petid"] > 0 && (int)$_POST["petid"] == $_POST["petid"])
  $target_pet = get_pet_byid((int)$_POST["petid"]);
else
  $target_pet = array();

if($target_pet["user"] != $user["user"] || $target_pet["dead"] != "no" || $target_pet["graphic"] != "desikh.gif" || $target_pet['location'] != 'home')
{
?>
Which pet will you apply the <?= $this_inventory["itemname"] ?> to?</p>
<form action="itemaction.php?idnum=<?= $this_inventory["idnum"] ?>" method="post">
<p><select name="petid">
<?php
  foreach($valid_pets as $pet)
  {
    echo "   <option value=\"" . $pet["idnum"] . "\">" . $pet["petname"] . "</option>\n";
  }
?>
</select>&nbsp;<input type="submit" name="submit" value="Apply" /></p>
</form>
<p>
<?php
}
else
{
  if($this_item["itemname"] == "Desikh Nondepilatory")
  {
    $database->FetchNone("UPDATE monster_pets SET `graphic`='longhairdesikh.png' WHERE idnum=" . $target_pet["idnum"] . " LIMIT 1");

    echo "You empty the can on " . $target_pet["petname"] . ", whose hair lengthens visibly!\n";
  }

  delete_inventory_byid($this_inventory["idnum"]);
  
  $AGAIN_WITH_ANOTHER = true;
}
?>
