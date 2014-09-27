<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/petlib.php';
require_once 'commons/grammar.php';

if(count($userpets) == 0)
  echo '<p>You have no pet to give this to.</p>';
else
{
  $petid = (int)$_POST['petid'];

  if($petid > 0)
    $target_pet = get_pet_byid($petid);
  else
    $target_pet = array();

  if($target_pet['user'] != $user['user'] || $target_pet['dead'] != 'no' || $target_pet['zombie'] == 'yes' || $target_pet['location'] != 'home')
  {
?>
<p>Splashing a pet with this will remove the curse of lycanthropy, and return a werepet to normal.</p>
 <form action="itemaction.php?idnum=<?= $_GET['idnum'] ?>" method="post">
 <p>
  <select name="petid">
<?php
    foreach($userpets as $this_pet)
    {
      if($this_pet['changed'] == 'yes')
        echo '   <option value="' . $this_pet['idnum'] . '">' . $this_pet['petname'] . ' (is in wereform)</option>';
      else if($this_pet['sleeping'] == 'yes')
        echo '   <option value="' . $this_pet['idnum'] . '">' . $this_pet['petname'] . ' (is sleeping)</option>';
      else if($this_pet['dead'] != 'no')
        echo '   <option disabled>' . $this_pet['petname'] . ' (is dead)</option>';
      else if($this_pet['zombie'] == 'yes')
        echo '   <option disabled>' . $this_pet['petname'] . ' (is a zombie)</option>';
      else
        echo '   <option value="' . $this_pet['idnum'] . '">' . $this_pet['petname'] . '</option>';
    }
?>
  </select> <input type="submit" name="submit" value="Give" />
 </p>
 </form>
<?php
  }
  else
  {
    $database->FetchNone('
      UPDATE monster_pets
      SET
        lycanthrope=\'no\',
        changed=\'no\'
      WHERE idnum=' . $target_pet['idnum'] . '
      LIMIT 1
    ');

    if($target_pet['sleeping'] == 'yes')
      echo '<p>' . $target_pet['petname'] . ' jumps up, looks around, confused, then falls back asleep.</p>';
    else
      echo '<p>' . $target_pet['petname'] . ' twitches a little, but soon resumes its normal behavior.</p>';

    echo '<p><i>(If ' . $target_pet['petname'] . ' had lycanthropy, it is now cured!)</i></p>';

    delete_inventory_byid($this_inventory['idnum']);

    require_once 'commons/statlib.php';

    record_stat($user['idnum'], 'Used a ' . $this_inventory['itemname'], 1);

    $AGAIN_WITH_ANOTHER = true;
  }
}
?>
