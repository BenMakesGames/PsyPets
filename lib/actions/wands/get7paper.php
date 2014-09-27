<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/statlib.php';
  
if($_GET['step'] == 2)
{
  $AGAIN_WITH_ANOTHER = true;
  $RECOUNT_INVENTORY = true;

  delete_inventory_byid($this_inventory['idnum']);

  add_inventory($user['user'], '', 'Silver', 'The remains of a ' . $this_inventory['itemname'], $this_inventory['location']);

  $database->FetchNone('DELETE FROM `monster_inventory` WHERE `user`=' . quote_smart($user['user']) . ' AND `itemname`=\'Smoke\' AND `location`=' . quote_smart($this_inventory['location']));
  $smokes = $database->AffectedRows();

  $papers = mt_rand(5, mt_rand(9, 13));
  
  if($smokes > 0)
    echo '<p>A draft sweeps through the room, blowing all the Smoke out the windows, and bringing with it ' . $papers . ' pieces of Paper.</p>';
  else
    echo '<p>A draft sweeps through the room, bringing with it ' . $papers . ' pieces of Paper.</p>';

  add_inventory_quantity($user['user'], '', 'Paper', 'Swept in on a draft', $this_inventory['location'], $papers);
    
  echo '<p>The ' . $this_inventory['itemname'] . ', having done it\'s job, crumbles, leaving behind a lump of Silver.</p>';
}
else
{
  echo '
    <p>Will you use the ' . $this_inventory['itemname'] . '?</p>
    <ul><li><a href="?idnum=' . $this_inventory['idnum'] . '&amp;step=2">Believe it!</a></li></ul>
  ';
}
?>
