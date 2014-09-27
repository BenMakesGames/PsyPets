<?php
function get_maproom_byuser($userid)
{
  $command = "SELECT * FROM psypets_maprooms WHERE userid=$userid LIMIT 1";
  return fetch_single($command, 'fetching map room');
}

function create_maproom($userid)
{
  $command = "INSERT INTO psypets_maprooms (userid) VALUES ($userid)";
  fetch_none($command, 'creating map room');

  if($GLOBALS['database']->AffectedRows() == 0)
  {
    echo "create_maproom($userid, $locid)<br />\n" .
         "Failed to create your map room.  Try reloading this page; if the problem persists, contact <a href=\"admincontact.php\">an administrator</a>.<br />\n";
    exit();
  }
}

function get_location_byid($idnum)
{
  $command = "SELECT * FROM psypets_locations WHERE idnum=$idnum LIMIT 1";
  return fetch_single($command, 'fetching location');
}
?>
