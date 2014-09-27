<?php
$TOMBSTONES_PER_PAGE = 128;

function get_graveyard_pages_by_user($userid)
{
  global $TOMBSTONES_PER_PAGE;

  $command = 'SELECT COUNT(*) AS c FROM psypets_graveyard WHERE ownerid=' . $userid;
  $data = fetch_single($command, 'fetching tombstone count');

  return ceil($data['c'] / $TOMBSTONES_PER_PAGE);
}

function get_graveyard_by_user($page, $userid)
{
  global $TOMBSTONES_PER_PAGE;

  $first = ($page - 1) * $TOMBSTONES_PER_PAGE;

  $command = 'SELECT * FROM psypets_graveyard WHERE ownerid=' . $userid . ' LIMIT ' . $first . ',' . $TOMBSTONES_PER_PAGE;
  $tombstones = fetch_multiple($command, 'fetching personal graveyard');

  return $tombstones;
}

function get_graveyard_pages()
{
  global $TOMBSTONES_PER_PAGE;

  $command = 'SELECT COUNT(*) AS c FROM psypets_graveyard';
  $data = fetch_single($command, 'fetching tombstone count');

  return ceil($data['c'] / $TOMBSTONES_PER_PAGE);
}

function get_graveyard($page)
{
  global $TOMBSTONES_PER_PAGE;

  $first = ($page - 1) * $TOMBSTONES_PER_PAGE;

  $command = "SELECT * FROM psypets_graveyard LIMIT $first,$TOMBSTONES_PER_PAGE";
  $tombstones = fetch_multiple($command, 'fetching graveyard');

  return $tombstones;
}

function get_random_empty_tombstoneid()
{
  $command = 'SELECT r1.idnum FROM psypets_graveyard AS r1 JOIN ' .
             '(SELECT (RAND() * (SELECT MAX(idnum) FROM psypets_graveyard)) AS idnum) AS r2 ' .
             'WHERE r1.idnum >= r2.idnum AND r1.ghost=\'no\' ORDER BY r1.idnum ASC LIMIT 1';
  $data = fetch_single($command, 'fetching random tombstone');
  
  return $data['idnum'];
}

function reassign_ghost($id)
{
  $command = 'UPDATE psypets_graveyard SET ghost=\'yes\' WHERE idnum=' . get_random_empty_tombstoneid() . ' LIMIT 1';
  fetch_none($command, 'gravelib.php/reassign_ghost()');

  $command = 'UPDATE psypets_graveyard SET ghost=\'no\' WHERE idnum=' . $id . ' LIMIT 1';
  fetch_none($command, 'gravelib.php/reassign_ghost()');
}

function get_tombstone_byid($idnum)
{
  $command = "SELECT * FROM psypets_graveyard WHERE idnum=$idnum LIMIT 1";
  $tombstone = fetch_single($command, 'gravelib.php/get_tombstone_byid()');

  return $tombstone;
}

function update_epitaph($idnum, $epitaph)
{
  $command = "UPDATE psypets_graveyard SET epitaph=" . quote_smart($epitaph) . " WHERE idnum=$idnum LIMIT 1";
  fetch_none($command, 'gravelib.php/update_epitaph()');
}
?>
