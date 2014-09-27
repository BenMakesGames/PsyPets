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
<p>This magic paintbrush seems strangely attracted to pets.  Upon which pet will you unleash the <?= $this_inventory['itemname'] ?>'s powerful magics?</p>
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

    $new_color = $COLORS[array_rand($COLORS)];

    $command = 'UPDATE monster_pets SET likes_color=' . quote_smart($new_color) . ' WHERE idnum=' . $target_pet['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'updating pet');

    echo '
        <p>The brush dances around ' . $target_pet['petname'] . ', splashing paint on and around ' . t_pronoun($target_pet['gender']) . ' before vanishing, brush, paint, and all.</p>
    ';

    if($database->AffectedRows() == 0)
    {
      echo '
        <p>Despite the show, you get the feeling not a lot has changed...</p>
      ';
    }
    else
    {
      echo '
        <p>Though you have no evidence, you feel as though something has changed about ' . $target_pet['petname'] . '...</p>
      ';
    }

    $AGAIN_WITH_ANOTHER = true;
  }

} // you have any pets
?>
