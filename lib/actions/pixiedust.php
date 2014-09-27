<?php
if($okay_to_be_here !== true)
  exit();

require_once "commons/grammar.php";

if(count($userpets) == 0)
  echo "<i>You have no pet to sprinkle this on.</i>\n";
else
{
  if($_POST['petid'] > 0 && (int)$_POST['petid'] == $_POST['petid'])
    $target_pet = get_pet_byid((int)$_POST['petid']);
  else
    $target_pet = array();

  if($target_pet['user'] == $user['user'] && $target_pet['dead'] == 'no' && $target_pet['protected'] == 'no' && $target_pet['location'] == 'home')
  {
?>
You sprinkle the pixie dust over <?= $target_pet['petname'] ?>, whose appearance is silently transformed.
<?php
    $graphics = get_global('petgfx');

    do
    {
      $graphic = $graphics[array_rand($graphics)];
    } while($graphic == $target_pet['graphic']);

    $database->FetchNone("UPDATE monster_pets SET graphic=" . quote_smart($graphic) . " WHERE idnum=" . $target_pet['idnum'] . " LIMIT 1");

    delete_inventory_byid($this_inventory['idnum']);
  }
  else
  {
?>
Which pet will you sprinkle the dust on?</p>
<p><i>(Dead and customized pets cannot be sprinkled with the dust.)</i></p>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
<p>
 <select name="petid">
<?php
    for($i = 0; $i < count($userpets); ++$i)
    {
      if($userpets[$i]['dead'] == 'no' && $userpets[$i]['protected'] == "no")
        echo "   <option value=\"" . $userpets[$i]["idnum"] . "\">" . $userpets[$i]['petname'] . "</option>\n";
    }
?>
 </select>&nbsp;<input type="submit" name="submit" value="Sprinkle" />
</p>
</form>
<p>
<?php
  }
}
?>
