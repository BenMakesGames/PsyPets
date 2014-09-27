<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/petlib.php';
require_once 'commons/flavorlib.php';

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

  if($target_pet['user'] != $user['user'])
  {
?>
<p>This item shines with extrasolar energy.  Which pet will you direct <?= $this_inventory['itemname'] ?> at?</p>
<form action="?idnum=<?= $_GET['idnum'] ?>" method="post">
<p><input type="submit" name="submit" value="Use!" /></p>
<table>
<?php
    $rowclass = begin_row_class();

    for($i = 0; $i < count($userpets); ++$i)
    {
      echo '
        <tr class="' . $rowclass . '">
         <td><input type="radio" name="petid" value="' . $userpets[$i]['idnum'] . '" /></td>
         <td>' . pet_graphic($userpets[$i]) . '</td>
         <td>' . $userpets[$i]['petname'] . '</td>
        </tr>
      ';

      $rowclass = alt_row_class($rowclass);
    }
?>
</table>
<p><input type="submit" name="submit" value="Use!" /></p>
</form>
<?php
  }
  else
  {
    $energy = max_energy($target_pet);

    if($target_pet['sleeping'] == 'yes')
      echo '<p>' . $target_pet['petname'] . ' slowly opens ' . his_her($target_pet['gender']) . ' eyes and stands up, apparently fully-rested.</p>';
    else if($energy >= $target_pet['energy'])
      echo '<p>' . $target_pet['petname'] . ' perks up a bit, full of energy.</p>';
    else
      echo '<p>Nothing seems to happen...</p>';

    if(mt_rand(1, 3) == 1)
    {
      delete_inventory_byid($this_inventory['idnum']);
      $AGAIN_WITH_ANOTHER = true;

      if(mt_rand(1, 2) == 1)
      {
        echo '<p>Then, the ' . $this_inventory['itemname'] . ' cracks and crumbles away, leaving behind a Ruby!  (And a bit of ash.)</p>';
        add_inventory($user['user'], '', 'Ruby', 'The remains of ' . $this_inventory['itemname'], $this_inventory['location']);
        add_inventory($user['user'], '', 'Smoke', 'The remains of ' . $this_inventory['itemname'], $this_inventory['location']);
      }
      else
      {
        echo '<p>Then, the ' . $this_inventory['itemname'] . ' cracks and crumbles away, leaving behind a bit of ash.</p>';
        add_inventory($user['user'], '', 'Smoke', 'The remains of ' . $this_inventory['itemname'], $this_inventory['location']);
      }
    }
    else
    {
      $AGAIN_WITH_SAME = true;
    }

    $command = 'UPDATE monster_pets SET sleeping=\'no\',energy=' . (int)$energy . ' WHERE idnum=' . (int)$target_pet['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'updating pet');
  }

} // you have any pets
?>
