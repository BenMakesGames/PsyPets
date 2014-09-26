<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/petlib.php';

if(count($userpets) == 0)
  echo '<p>You look into the mirror.</p><p>Lookin\' good.</p>';
else
{
  $petid = (int)$_POST['petid'];

  if($petid > 0)
    $target_pet = get_pet_byid($petid);
  else
    $target_pet = array();

  if($target_pet['user'] != $user['user'])
  {
?>
<p>To which pet will you show the <?= $this_inventory['itemname'] ?>?</p>
<form method="post">
<p>
 <select name="petid">
<?php
    foreach($userpets as $this_pet)
    {
      echo '   <option value="' . $this_pet['idnum'] . '">' . $this_pet['petname'] . '</option>';
    }
?>
 </select> <input type="submit" name="submit" value="Show" />
</p>
</form>
<?php
  }
  else
  {
    delete_inventory_byid($this_inventory['idnum']);
    $AGAIN_WITH_ANOTHER = true;
  
    $new_flip = ($target_pet['graphic_flip'] == 'yes' ? 'no' : 'yes');
  
    $database->FetchNone('
      UPDATE monster_pets
      SET graphic_flip=' . quote_smart($new_flip) . '
      WHERE idnum=' . $petid . '
      LIMIT 1
    ');
?>
<p>As you draw the mirror close to <?= $target_pet['petname'] ?>, <?= his_her($target_pet['gender']) ?> image in the mirror begins to flicker wildly until, without warning, the mirror fades from existence, and <?= $target_pet['petname'] ?> has become... backwards!?</p>
<?php
  }
}
?>
