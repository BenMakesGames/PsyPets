<?php
$PSYPETS_SKETCH_VERSION = 'PsyPetsSketch-0-15-2';

function get_sketch_byid($idnum)
{
  $command = 'SELECT * FROM psypets_store_portraits WHERE idnum=' . $idnum . ' LIMIT 1';
  return fetch_single($command, 'fetching sketch #' . $idnum);
}

function get_store_sketch_id($userid)
{
  $command = 'SELECT idnum FROM psypets_store_portraits WHERE userid=' . $userid . ' AND use_for_store=\'yes\' LIMIT 1';
  $data = fetch_single($command, 'fetching resident\'s shop keep sketch idnum');
  
  return $data['idnum'];
}

function delete_sketch($idnum, $userid)
{
  $command = 'DELETE FROM psypets_store_portraits WHERE idnum=' . $idnum . ' AND userid=' . $userid . ' LIMIT 1';
  fetch_none($command, 'deleting sketch');
}

function set_shop_keep($userid, $sketchid)
{
  $command = 'UPDATE psypets_store_portraits SET use_for_store=\'no\' WHERE userid=' . $userid . ' AND use_for_store=\'yes\'';
  fetch_none($command, 'clearing current shop keep');

  $command = 'UPDATE psypets_store_portraits SET use_for_store=\'yes\' WHERE userid=' . $userid . ' AND idnum=' . $sketchid . ' LIMIT 1';
  fetch_none($command, 'setting shop keep');
}
?>
