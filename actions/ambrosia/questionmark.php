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

    $command = 'UPDATE monster_pets SET levelquestion=0 WHERE idnum=' . $target_pet['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'updating pet');

    if($database->AffectedRows() == 0)
    {
      echo '
        <p>The end of the ' . $this_inventory['itemname'] . ' glows brilliantly until the entire thing vanishes in a puff of Smoke.</p>
        <p>Despite the show, you get the feeling not a lot has changed...</p>
      ';
    }
    else
    {
      $command = 'UPDATE monster_pets SET lastlevelquestion=' . $target_pet['levelquestion'] . ' WHERE idnum=' . $target_pet['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'updating pet (2)');

      echo '
        <p>The end of the ' . $this_inventory['itemname'] . ' glows brilliantly until the entire thing vanishes in a puff of Smoke.</p>
        <p>Though you have no evidence, you feel as though something has changed about ' . $target_pet['petname'] . '...</p>
      ';
    }

    add_inventory($user['user'], '', 'Smoke', 'The remains of a ' . $this_inventory['itemname'] . '.', $this_inventory['location']);

    $AGAIN_WITH_ANOTHER = true;
  }

} // you have any pets
?>
