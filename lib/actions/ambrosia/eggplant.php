<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/petlib.php';
require_once 'commons/grammar.php';

$command = 'SELECT * FROM monster_pets WHERE user=' . quote_smart($user['user']) . ' AND location=\'home\' AND dead=\'no\' AND eggplant=\'yes\' AND sleeping=\'no\'';
$afflicted_pets = $database->FetchMultipleBy($command, 'idnum', 'fetching pets');

echo '
  <h4>Warnings</h4>
  <p>Eggplant Curse that occurs with or is followed by high fever, headache, rash, swelling and nausea may be serious.  Ask doctor right away.</p>
  <p>Keep out of reach of children.</p>
  <h4>Directions</h4>
  <p>May be taken with or without water.  Repeat as often as necessary to relieve symptoms.</p>
  <h4>Other Information</h4>
  <p>Avoid storing at high temperatures (greater than 200&deg;C).</p>
  <hr />
';

if(count($afflicted_pets) == 0)
  echo '
    <p>None of your pets are afflicted by the Eggplant Curse.</p>
    <p>(Sleeping pets must be awoken first.)</p>
  ';
else
{
  $petid = (int)$_POST['petid'];

  if(!array_key_exists($petid, $afflicted_pets))
  {
?>
 <p>Which pet will you give the <?= $this_inventory['itemname'] ?> to?</p>
 <p>(Sleeping pets must be awoken first, and are not listed here.)</p>
 <form action="itemaction.php?idnum=<?= $_GET['idnum'] ?>" method="post">
 <p>
  <select name="petid">
<?php
    foreach($afflicted_pets as $this_pet)
        echo '   <option value="' . $this_pet['idnum'] . '">' . $this_pet['petname'] . '</option>';
?>
  </select> <input type="submit" name="submit" value="Give" />
 </p>
 </form>
<?php
  }
  else
  {
    $command = 'UPDATE monster_pets SET eggplant=\'no\' WHERE idnum=' . $petid . ' LIMIT 1';
    $database->FetchNone($command, 'lifting the eggplant curse from a pet');

    echo '<p class="success">' . $afflicted_pets[$petid]['petname'] . '\'s color starts to return!  Thank goodness!</p>';

    delete_inventory_byid($this_inventory['idnum']);

    $AGAIN_WITH_ANOTHER = true;
  }
}
?>
