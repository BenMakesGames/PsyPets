<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/petlib.php';

$phoenix_graphics = array('phoenix/firebird_red.png', 'phoenix/firebird_yellow.png', 'phoenix/icebird.png');

$petid = (int)$_POST['petid'];

if($petid > 0)
  $target_pet = get_pet_byid($petid);
else
  $target_pet = array();

if($target_pet['user'] != $user['user'] || $target_pet['dead'] == 'no' || !in_array($target_pet['graphic'], $phoenix_graphics) || $target_pet['location'] != 'home')
{
?>
<p>This liquid can revive a dead Phoenix...</p>
<p><i>(Only Phoenixes born from a Phoenix Egg may be revived in this way.)</i></p>
<form action="itemaction.php?idnum=<?= $_GET["idnum"] ?>" method="post">
<?php
  $eligible_pets = 0;
  for($i = 0; $i < count($userpets); ++$i)
  {
    if($userpets[$i]['dead'] != 'no' && in_array($userpets[$i]['graphic'], $phoenix_graphics))
    {
      if($eligible_pets == 0)
        echo '<p><select name="petid">'; 
      echo '<option value="' . $userpets[$i]['idnum'] . '">' . $userpets[$i]['petname'] . '</option>';
      $eligible_pets++;
    }
  }
  
  if($eligible_pets > 0)
    echo '</select>&nbsp;<input type="submit" name="submit" value="Give" /></p>';
?>
</form>
<?php
}
else
{
  delete_inventory_byid($this_inventory['idnum']);
  delete_pet($target_pet);

  add_inventory($user['user'], 'u:' . $user['idnum'], 'Phoenix Egg', 'Born from ' . $target_pet['petname'] . '\'s remains', $this_inventory['location']);

  echo $target_pet['petname'] . '\'s body erupts into flame, and is reduced to ashes!</p><p>As the fire dies, some of the ashes are blown away, and a single Phoenix Egg is revealed.';

  $AGAIN_WITH_ANOTHER = true;
}
?>
