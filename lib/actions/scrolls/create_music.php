<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_ANOTHER = true;

$items = array('Didgeridoo' => 'a ', 'Lyre' => 'a ', 'Ocarina' => 'an ', 'Triangle' => 'a ', 'Piano' => 'a ', 'Puniu' => 'a ', 'Xylophone' => 'a ', 'Bagpipes' => '', 'Maraca' => 'a ');

$itemname = array_rand($items);

add_inventory($user['user'], 'u:' . $user['idnum'], $itemname, $user['display'] . ' summoned this', $this_inventory["location"]);

delete_inventory_byid($_GET["idnum"]);
?>
A poof of smoke slowly clears to reveal <?= $items[$itemname] . $itemname ?>.
