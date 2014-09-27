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

  if($target_pet['user'] != $user['user'])
  {
?>
<p>Upon which pet will you unleash the <?= $this_inventory['itemname'] ?>'s powerful magics?</p>
<p class="failure">Warning!  These magics are <strong>seriously deadly!</strong>  Don't unleash them upon a pet you don't want to die.</p>
<form action="itemaction.php?idnum=<?= $_GET['idnum'] ?>" method="post">
<p><input type="submit" name="submit" value="Unleash!" /></p>
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
<p><input type="submit" name="submit" value="Unleash!" /></p>
</form>
<?php
  }
  else
  {
    delete_inventory_byid($this_inventory['idnum']);

    if($target_pet['dead'] == 'no')
    {
      $command = 'UPDATE monster_pets SET dead=\'bonestaff\' WHERE idnum=' . $target_pet['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'updating pet');

      echo '<p>The ' . $this_inventory['itemname'] . ' dissolves into a cloud of smoke which enters into ' . $target_pet['petname'] . ', striking ' . t_pronoun($target_pet['gender']) . ' dead!</p>';

      require_once 'commons/statlib.php';

      record_stat($user['idnum'], 'Killed a Pet Using a ' . $this_inventory['itemname'] . '!', 1);
    }
    else
    {
      echo '<p>The ' . $this_inventory['itemname'] . ' dissolves into a cloud of smoke which hovers around ' . $target_pet['petname'] . ' for a while before settling down.</p>';

      add_inventory($user['user'], '', 'Smoke', 'The remains of a ' . $this_inventory['itemname'] . '.', $this_inventory['location']);
    }

    $AGAIN_WITH_ANOTHER = true;
  }

} // you have any pets
?>
