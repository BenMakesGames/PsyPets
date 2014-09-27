<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/petlib.php';

if(count($userpets) == 0)
{
  echo '<p>You have no pet to use this on.</p>';
}
else
{
  if($_POST['petid'] > 0 && (int)$_POST['petid'] == $_POST['petid'])
    $target_pet = get_pet_byid((int)$_POST['petid']);
  else
    $target_pet = array();

  if($target_pet['user'] != $user['user'] || $target_pet['dead'] != 'no' || $target_pet['sleeping'] == 'yes' || $target_pet['changed'] == 'yes' || $target_pet['zombie'] == 'yes')
  {
?>
<p>Which pet will you give the <?= $this_inventory['itemname'] ?> to?</p>
<form action="?idnum=<?= $_GET['idnum'] ?>" method="post">
<p><input type="submit" name="submit" value="Give" /></p>
<?= render_choose_pet_xhtml($userpets, array('alive', 'awake', 'sane')) ?>
<p><input type="submit" name="submit" value="Give" /></p>
</form>
<?php
  }
  else
  {
    delete_inventory_byid($this_inventory['idnum']);

    add_inventory($user['user'], '', 'Erlenmeyer Flask', 'The remains of a ' . $this_inventory['itemname'] . '.', $this_inventory['location']);

    if($target_pet['graphic_size'] > 24)
    {
      $new_size = max(24, $target_pet['graphic_size'] - mt_rand(4, 8));
    
      $database->FetchNone('UPDATE monster_pets SET graphic_size=' . (int)$new_size . ' WHERE idnum=' . $target_pet['idnum'] . ' LIMIT 1');

      echo '<p>' . $target_pet['petname'] . ' shrinks, as if being deflated!</p>';
    }
    else
    {
      echo '<p>The ' . $target_pet['petname'] . ' shrinks, as if being deflated, but then, after a moment of nothing happening, a series of abrupt growths restore ' . him_her($target_pet['gender']) . ' to ' . his_her($target_pet['gender']) . ' original size!</p>';
    }

    $AGAIN_WITH_ANOTHER = true;
  }

} // you have any pets
?>
