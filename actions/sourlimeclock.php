<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/farmlib.php';

$farm = get_farm_if_exists($user['idnum']);

if($_GET['action'] != 'attach')
  echo '<p>It seems to be perfectly on-time!  Fantastic!</p>';

if($farm !== false && $farm['coop_has_timer'] == 'no')
{
  if($_GET['action'] == 'attach')
  {
    delete_inventory_byid($this_inventory['idnum']);
    put_clock_in_farm($farm);

    echo '
      <p>Great!  It\'s sure to come in useful!</p>
    ';
    
    $AGAIN_WITH_ANOTHER = true;
  }
  else
  {
    echo '
      <p>It occurs to you that this would be really helpful in your Chicken Coop!  It\'d surely increase feeding efficiency!</p>
      <ul>
       <li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=attach">Oh, that\'s clever!  Do that!</a></li>
      </ul>
    ';
  }
}
?>
