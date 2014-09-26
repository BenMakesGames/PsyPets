<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_SAME = true;

require_once 'commons/questlib.php';

$harvest = get_quest_value($user['idnum'], 'Cot money');

$data = (int)$harvest['value'];
$day = 60 * 60 * 23;

$ripe = false;
$harvested = false;

if($harvest === false || $now > $data)
{
  $data = $now + $day;

  if($harvest === false)
    add_quest_value($user['idnum'], 'Cot money', $data);
  else
    update_quest_value($harvest['idnum'], $data);

  give_money($user, 1, 'Found under a ' . $this_inventory['itemname']);

  echo '<p>You find 1 money!</p>';
}
else
  echo '<p>You don\'t see anything under the ' . $this_inventory['itemname'] . '.</p>';
?>
