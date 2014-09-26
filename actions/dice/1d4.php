<?php
if($okay_to_be_here !== true)
  exit();

$AGAIN_WITH_SAME = true;

if(rand(1, 100) > 1)
{
  $roll = rand(1, 4);
  echo "You roll the die and get a " . $roll . ".";
  $MP = $roll * 5;

  if($user['mazemp'] < 100 && $user['show_pattern'] == 'yes')
  {
    $command = 'UPDATE monster_users SET mazemp=mazemp+' . $MP . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'giving player movement points');

    delete_inventory_byid($_GET["idnum"]);

    add_inventory($user["user"], '', "Plastic", "Recovered from an exploded " . $this_inventory["itemname"], $this_inventory["location"]);
    echo "</p>\n<p>You're pretty sure this doesn't make any sense, but the die completely explodes.  At least the Plastic is recoverable.  (And you got $MP movement points to use in The Pattern >_>)";

    require_once 'commons/statlib.php';
    if(record_stat_with_badge($user['idnum'], 'Rolled a Die for Movement Points', 1, 100, 'rollem'))
      echo '<p><i>You received the Roll Them Bones Badge.</i></p>';

    $AGAIN_WITH_ANOTHER = true;
    $AGAIN_WITH_SAME = false;
  }
}
else
{
  $side = rand(1, 4);
  do
  {
    $o_side = rand(1, 4);
  } while($side + $o_side == 5 || $side == $o_side);
  echo "You roll the die, which inexplicably lands balancing on the edge between $side and $o_side!";
}
?>
