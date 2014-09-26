<?php
if($okay_to_be_here !== true)
  exit();

if($_GET['action'] == 'wshhshwshsh')
{
  delete_inventory_byid($this_inventory['idnum']);

  add_inventory($user['user'], '', 'Sharp Edge', '', $this_inventory['location']);
  add_inventory($user['user'], '', '50-foot Red Ribbon', '', $this_inventory['location']);

  echo '<p>In one, swift motion you pull the ribbon from the sword.</p>';
}
else
{
  echo '
    <p>Pull the ribbon from off of the sword?</p>
    <ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&amp;action=wshhshwshsh">Wshhshwshsh!</a> (The sound of the ribbon getting pulled off? >_>)</li></ul>
  ';
}
?>