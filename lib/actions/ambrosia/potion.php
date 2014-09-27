<?php
if($okay_to_be_here !== true)
  exit();

require_once "commons/petlib.php";

if(count($userpets) == 0)
{
  echo "<i>You have no pet to give this to.</i>\n";
  exit();
}
else
{
  $alive = false;

  foreach($userpets as $pet)
  {
    if($pet["dead"] == "no")
    {
      $alive = true;
      break;
    }
  }

  if(!$alive)
  {
    echo "<i>You have no pet to give this to.</i>\n";
    exit();
  }
}

if($_POST["petid"] > 0 && (int)$_POST["petid"] == $_POST["petid"])
  $target_pet = get_pet_byid((int)$_POST["petid"]);
else
  $target_pet = array();

if($target_pet["user"] != $user["user"] || $target_pet["dead"] != "no" || $target_pet['location'] != 'home' || $target_pet['zombie'] != 'no')
{
?>
<p>Which pet will drink <?= $this_inventory["itemname"] ?>?</p>
<form action="itemaction.php?idnum=<?= $_GET["idnum"] ?>" method="post">
<p><select name="petid">
<?php
  for($i = 0; $i < count($userpets); ++$i)
  {
    echo "   <option value=\"" . $userpets[$i]["idnum"] . "\">" . $userpets[$i]["petname"] . "</option>\n";
  }
?>
</select>&nbsp;<input type="submit" name="submit" value="Give" /></p>
</form>
<p>
<?php
}
else
{
  if($this_item["itemname"] == "Nature's Embrace")
  {
    $training = ceil(level_stat_exp($target_pet['sur']) / 4);
    train_stat($target_pet, 'sur', $training, true, true);

    echo "Grass springs up around " . $target_pet["petname"] . "'s feet.\n";
  }
  else if($this_item["itemname"] == "Artisan's Craft")
  {
    $training = ceil(level_stat_exp($target_pet['cra']) / 4);
    train_stat($target_pet, 'cra', $training, true, true);

    echo "For a moment " . $target_pet["petname"] . " shines a brilliant blue.\n";
  }
  else if($this_item["itemname"] == "Adventurer's Courage")
  {
    $training = ceil(level_stat_exp($target_pet['bra']) / 4);
    train_stat($target_pet, 'bra', $training, true, true);

    echo $target_pet["petname"] . "'s eyes glow blood red.\n";
  }
  else if($this_item["itemname"] == "Night's Touch")
  {
    $training = ceil(level_stat_exp($target_pet['stealth']) / 4);
    train_stat($target_pet, 'stealth', $training, true, true);

    echo "Black tendrils grow from " . $target_pet["petname"] . ", then fade away.\n";
  }
  else if($this_item["itemname"] == "Athlete's Victory")
  {
    $training = ceil(level_stat_exp($target_pet['athletics']) / 4);
    train_stat($target_pet, 'athletics', $training, true, true);

    echo "There is a loud applause, clearly directed at " . $target_pet["petname"] . ".\n";
  }
  else if($this_item['itemname'] == 'Child\'s Play')
  {
    $min_exp_up = ceil(level_exp($target_pet['love_level']) / 5);
    $max_exp_up = ceil(level_exp($target_pet['love_level']) / 4) + 1;
    
    $exp_up = mt_rand($min_exp_up, $max_exp_up);

    gain_love_exp($target_pet, $exp_up, 0, true);

    echo '<p>' . $target_pet['petname'] . ' smiles a big, broad smile.</p>';
  }
  else
  {
    die('broken potion item.  please alert That Guy Ben.');
  }

  delete_inventory_byid($this_inventory['idnum']);
  
  $AGAIN_WITH_ANOTHER = true;
}
?>
