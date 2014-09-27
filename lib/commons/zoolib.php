<?php
function get_zoo_byuser($userid, $locid = 0)
{
  $command = "SELECT * FROM psypets_zoos WHERE userid=$userid LIMIT 1";
  $zoo = fetch_single($command, 'fetching zoo');

  return $zoo;
}

function create_zoo($userid, $locid = 0)
{
  $command = "INSERT INTO psypets_zoos (userid) VALUES ($userid)";
  fetch_none($command, 'creating zoo');
}

function get_prey_byid($idnum)
{
  $command = "SELECT * FROM monster_prey WHERE idnum=$idnum LIMIT 1";
  $prey = fetch_single($command, 'fetching prey by id');

  return $prey;
}
?>
