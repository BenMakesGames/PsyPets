<?php
if($okay_to_be_here !== true)
  exit();

if($_GET['step'] == 2)
{
  delete_inventory_byid($this_inventory['idnum']);

  $idnum = create_random_pet($user['user']);

  $database->FetchNone('
    UPDATE monster_pets
    SET
      graphic=\'scrib.png\',
      chemistry=2,
      `int`=1,
      extraverted=FLOOR(extraverted/2)
    WHERE idnum=' . $idnum . '
    LIMIT 1
  ');

  echo '
    <p>The ' . $this_inventory['itemname'] . ' bursts open, revealing a tiny Scrib!</p>
  ';

  require_once 'commons/statlib.php';
  record_stat($user['idnum'], 'Scribs Created', 1);
}
else
{
  echo '
    <p>As you approach the ' . $this_inventory['itemname'] . ', it begins to quiver.</p>
    <p>Something violent might happen if you proceed...</p>
    <ul><li><a href="?idnum=' . $this_inventory['idnum'] . '&amp;step=2">Proceed anyway!</a></li></ul>
  ';
}
?>
