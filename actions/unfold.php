<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/paperlib.php';

$AGAIN_WITH_ANOTHER = true;

delete_inventory_byid($this_inventory['idnum']);

$item = unfold_hat();

if($item === 'Torn Paper')
{
  echo 'You start to unfold the ' . $this_inventory['itemname'] . ', but accidentally tear it!';
  add_inventory($user['user'], '', 'Torn Paper', 'Tore while unfolding a ' . $this_inventory['itemname'] . '.', $this_inventory['location']);

  require_once 'commons/statlib.php';
  record_stat($user['idnum'], 'Tore a Piece of Paper', 1);
}
else
{
  echo 'You unfold the ' . $this_inventory['itemname'] . ' and smooth out the Paper.';
  if($item != 'Paper')
    echo ' Wait a sec!  It\'s a ' . $item . '!';

  add_inventory($user['user'], '', $item, 'Unfolded from a ' . $this_inventory['itemname'] . '.', $this_inventory['location']);

  require_once 'commons/statlib.php';
  record_stat($user['idnum'], 'Unfolded a Piece of Paper', 1);
}
?>
