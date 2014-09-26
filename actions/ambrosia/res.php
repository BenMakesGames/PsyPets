<?php
$command = 'SELECT petname,idnum FROM monster_pets WHERE user=' . quote_smart($user['user']) . ' AND dead!=\'no\'';
$dead_pets = $database->FetchMultiple($command, 'fetching dead pets');

if(count($dead_pets) == 0)
  echo '<p>You have no dead pets to give this to.</p>';
else
{
  $petid = (int)$_POST['petid'];

  if($petid > 0)
    $target_pet = get_pet_byid($petid);
  else
    $target_pet = array();

  if($target_pet['user'] != $user['user'] || $target_pet['dead'] == 'no')
  {
?>
<p>Which pet will drink?  <i>(Only dead pets are listed here.)</i></p>
 <form action="itemaction.php?idnum=<?= $_GET['idnum'] ?>" method="post">
 <p>
  <select name="petid">
<?php
    foreach($dead_pets as $this_pet)
      echo '   <option value="' . $this_pet['idnum'] . '">' . $this_pet['petname'] . '</option>';
?>
  </select> <input type="submit" name="submit" value="Give" />
 </p>
 </form>
<?php
  }
  else
  {
    delete_inventory_byid($this_inventory['idnum']);

    $target_pet['dead'] = 'no';
    $target_pet['food'] = 10;
    $target_pet['energy'] = 10;
    save_pet($target_pet, array('dead', 'food', 'energy'));
?>
   <p>A rush of wind blows the color into <?= $target_pet['petname'] ?>'s body, which rises up and faces you.  It twitches slightly, then looks around as if waking from a dream.</p>
<?php
    $AGAIN_WITH_ANOTHER = true;
  }
}
?>
