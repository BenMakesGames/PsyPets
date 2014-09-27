<?php
function get_dungeon_byuser($userid, $locid = 0)
{
  $command = "SELECT * FROM psypets_dungeons WHERE userid=$userid LIMIT 1";
  $dungeon = fetch_single($command, 'fetching dungeon');

  return $dungeon;
}

function create_dungeon($userid, $locid = 0)
{
  $command = "INSERT INTO psypets_dungeons (userid) VALUES ($userid)";
  fetch_none($command, 'creating dungeon');
}

function get_monster_byid($idnum)
{
  $command = "SELECT * FROM monster_monsters WHERE idnum=$idnum LIMIT 1";
  $monster = fetch_single($command, 'fetching monster');
  
  return $monster;
}
?>