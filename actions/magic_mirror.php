<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_SAME = true;
$RECOUNT_INVENTORY = false;

require_once 'commons/grammar.php';

if(count($userpets) == 0)
  echo "<i>You have no pet to show this to.</i>\n";
else
{
  if($_POST['petid'] > 0 && (int)$_POST['petid'] == $_POST['petid'])
    $target_pet = get_pet_byid((int)$_POST['petid']);
  else
    $target_pet = array();

  if($target_pet['user'] == $user['user'] && $target_pet['dead'] == "no" && $target_pet['sleeping'] == "no" && $target_pet['location'] == 'home')
  {
?>
 "Mirror, mirror!" you intone, "who's the fairest of them all?" and give the mirror to <?= $target_pet['petname'] ?>.</p>
<?php
    if((int)$this_inventory['data'] > $now)
    {
      echo "<p>" . $target_pet['petname'] . " stares into the mirror for a while, expectantly, but nothing seems happen.";
    }
    else if($target_pet['protected'] == "no")
    {
      echo "<p>" . $target_pet['petname'] . " stares into the mirror for a while, motionless.  You almost worry that something's wrong, but then " . pronoun($target_pet['gender']) . " turns to face you, and you realize that " . $target_pet['petname'] . " looks completely different!";

      $graphics = get_global('petgfx');

      do
      {
        $graphic = $graphics[array_rand($graphics)];
      } while($graphic == $target_pet['graphic']);

      $database->FetchNone("UPDATE monster_pets SET graphic=" . quote_smart($graphic) . " WHERE idnum=" . $target_pet['idnum'] . " LIMIT 1");
      $database->FetchNone("UPDATE monster_inventory SET data=" . ($now + 8 * 60 * 60) . " WHERE idnum=" . $this_inventory['idnum'] . " LIMIT 1");
    }
    else
      echo "<p>The mirror responds: \"This beauty!  This radiance!  I can do nothing for this creature.\"";
  }
  else
  {
?>
 Which pet will gaze into the mirror?</p>
 <p><i>Sleeping and dead pets cannot gaze into the mirror, and are excluded from the list below.</i></p>
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
  </select>&nbsp;<input type="submit" name="submit" value="Gaze" />
 </p>
 </form>
 <p>
<?php
  }
}
?>
