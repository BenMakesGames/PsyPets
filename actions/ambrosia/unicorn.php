<?php
if($okay_to_be_here !== true)
  exit();

require_once "commons/petlib.php";

if(count($userpets) == 0)
{
  echo "<i>You have no pet to give this to.</i>";
  exit();
}
else
{
  $alive = false;

  foreach($userpets as $pet)
  {
    if($pet['dead'] == 'no' && $pet['protected'] == 'no' && $pet['changed'] == 'no' && $pet['zombie'] == 'no')
    {
      $alive = true;
      break;
    }
  }

  if(!$alive)
  {
    echo "<i>You have no pet to give this to.  (Dead pets, protected pets, and pets in wereform may not eat this.)</i>";
    exit();
  }
}

if((int)$_POST['petid'] > 0)
  $target_pet = get_pet_byid((int)$_POST['petid']);
else
  $target_pet = array();

if($target_pet['user'] != $user['user'] || $target_pet['dead'] != 'no' || $target_pet['protected'] != 'no' || $target_pet['changed'] != 'no' || $target_pet['zombie'] != 'no' || $target_pet['location'] != 'home')
{
?>
<p>Which pet will eat the <?= $this_inventory['itemname'] ?>?</p>
<p><i>(Dead pets, protected pets, and pets in wereform may not eat it, and are not listed.  This item will permanently change your pet's appearance, and is consumed after one use!)</i></p>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
<p><select name="petid">
<?php
  foreach($userpets as $pet)
  {
    if($pet['dead'] == 'no' && $pet['protected'] == 'no' && $pet['changed'] == 'no' && $pet['zombie'] == 'no')
      echo '   <option value="' . $pet['idnum'] . '">' . $pet['petname'] . "</option>\n";
  }
?>
</select>&nbsp;<input type="submit" name="submit" value="Give" /></p>
</form>
<?php
}
else
{
  if(substr($target_pet['graphic'], 0, 6) == 'dragon')
    $graphics = array('dragon_iridescent.png');
  else
    $graphics = array('unicorn.png', 'unicorn_candy.png', 'unicorn_citron.png');

  $command = 'UPDATE monster_pets SET graphic=\'' . $graphics[array_rand($graphics)] . '\' WHERE idnum=' . $target_pet['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating pet graphic');

  delete_inventory_byid($this_inventory['idnum']);

  echo '<p>' . $target_pet['petname'] . ' swallows the ' . $this_inventory['itemname'] . ' in one gulp.  Within seconds, a strange transformation takes hold of ' . t_pronoun($target_pet['gender']) . '...</p>';

  $AGAIN_WITH_ANOTHER = true;
}
?>
