<?php
if($okay_to_be_here !== true)
  exit();

require_once "commons/petlib.php";

if(count($userpets) == 0)
{
  echo "<i>You have no pet to use this on.</i>\n";
}

if($_POST["petid"] > 0 && (int)$_POST["petid"] == $_POST["petid"])
  $target_pet = get_pet_byid($_POST["petid"]);
else
  $target_pet = array();

if($target_pet["user"] != $user["user"] || $target_pet['location'] != 'home')
{
?>
 Point the wand at which pet?<br />
 </p>
 <form action="itemaction.php?idnum=<?= $_GET["idnum"] ?>" method="post">
 <p>
  <select name="petid">
<?php
  for($i = 0; $i < count($userpets); ++$i)
  {
    echo '<option value="' . $userpets[$i]['idnum'] . '">' . $userpets[$i]['petname'] . '</option>';
  }
?>
  </select>&nbsp;<input type="submit" name="submit" value="Point" />
 </p>
 </form>
 <p>
<?php
}
else
{
  $breaks = (mt_rand(1, 4) == 1);

  $effect = mt_rand(1, 3);

  echo '<p>The wand sparkles and shines with the glow of a full moon!</p>';

  if($effect == 1)
  {
    echo "<p>Threads of gossamer rain down on " . $target_pet["petname"] . ".</p>";
    add_inventory($user["user"], '', "Gossamer", "", $this_inventory["location"]);
  }
  else if($effect == 2)
  {
    echo "<p>Sparkles waft off of " . $target_pet["petname"] . " for a moment.</p>";

    $maxfood = max_food($target_pet);
    $maxsafety = max_safety($target_pet);
    $maxlove = max_love($target_pet);
    $maxesteem = max_esteem($target_pet);

    $database->FetchNone("UPDATE monster_pets SET food=$maxfood,safety=$maxsafety,love=$maxlove,esteem=$maxesteem WHERE idnum=" . $target_pet['idnum'] . " LIMIT 1");
  }
  else if($effect == 3)
  {
    echo "A ray of light shines on " . $target_pet["petname"] . " for a moment before fading.";

    $database->FetchNone('UPDATE monster_pets SET inspired=6 WHERE idnum=' . $target_pet['idnum'] . ' AND inspired<6 LIMIT 1');
  }

  if($breaks)
  {
    delete_inventory_byid($this_inventory['idnum']);
    echo '<p>Without warning, the wand snaps in two.</p>';
    
    $AGAIN_WITH_ANOTHER = true;
  }
  else
    $AGAIN_WITH_SAME = true;
}
?>
