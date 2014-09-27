<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/petlib.php';

if(count($userpets) == 0)
{
  echo "<i>You have no pet to use this on.</i>\n";
}

if($_POST["petid"] > 0 && (int)$_POST["petid"] == $_POST['petid'])
  $target_pet = get_pet_byid($_POST["petid"]);
else
  $target_pet = array();

if($target_pet["user"] != $user["user"] || $target_pet['changed'] == 'yes' || $target_pet['location'] != 'home')
{
?>
 <p>Hook the helmet up to which pet?</p>
 <form action="itemaction.php?idnum=<?= $_GET["idnum"] ?>" method="post">
 <p>
  <select name="petid">
<?php
  for($i = 0; $i < count($userpets); ++$i)
  {
    if($userpets['changed'] == 'yes')
      echo "<option value=\"" . $userpets[$i]["idnum"] . "\" disabled=\"disabled\">" . $userpets[$i]["petname"] . "</option>\n";
    else
      echo "<option value=\"" . $userpets[$i]["idnum"] . "\">" . $userpets[$i]["petname"] . "</option>\n";
  }
?>
  </select>&nbsp;<input type="submit" name="submit" value="Push the Big, Red Button" style="width: 175px;" />
 </p>
 </form>
<?php
}
else
{
  echo "<p>The machine rumbles a bit, and you're pretty sure you smell smoke.</p>\n<p>The lights in the house flicker.</p>\n<p>Then, suddenly, it all stops, and after a moment of complete silence the machine lets out a sigh and crumbles to the ground.</p>";

  $database->FetchNone('UPDATE monster_pets SET inspired=8 WHERE idnum=' . $target_pet['idnum'] . ' AND inspired<8 LIMIT 1');

  delete_inventory_byid($this_inventory['idnum']);
  add_inventory($user['user'], '', 'Ruins', 'What remains of a ' . $this_inventory['itemname'], $this_inventory['location']);

  require_once 'commons/statlib.php';
  record_stat($user['idnum'], 'Used a ' . $this_inventory['itemname'], 1);

  $AGAIN_WITH_ANOTHER = true;
}
?>
