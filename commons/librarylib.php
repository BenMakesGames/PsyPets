<?php
$FORBIDDEN_BOOKS = array(
  'The Passage of Time',
);

function get_library_book_count($userid)
{
  $command = 'SELECT COUNT(itemid) AS c FROM psypets_libraries WHERE userid=' . $userid;
  $data = fetch_single($command, 'fetching item count');
  
  return (int)$data['c'];
}

function get_library_books($userid, $page)
{
  $command = 'SELECT a.itemid,a.quantity,b.itemname,b.graphic,b.graphictype,b.action,b.custom FROM psypets_libraries AS a,monster_items AS b WHERE b.idnum=a.itemid AND a.userid=' . $userid . ' ORDER BY b.itemname LIMIT ' . (($page - 1) * 20) . ',20';
  return fetch_multiple($command, 'fetching library books');
}

function add_to_library($userid, $itemid, $quantity)
{
  $command = 'UPDATE psypets_libraries SET quantity=quantity+' . $quantity . ' WHERE userid=' . $userid . ' AND itemid=' . $itemid . ' LIMIT 1';
  fetch_none($command, 'updating book quantity');
  
  if($GLOBALS['database']->AffectedRows() == 0)
  {
    $command = 'INSERT INTO psypets_libraries (userid, itemid, quantity) VALUES ' .
      '(' . $userid . ', ' . $itemid . ', ' . $quantity . ')';
    fetch_none($command, 'inserting book quantity');
  }
}

function remove_all_from_library($userid, $itemid)
{
  $command = 'DELETE FROM psypets_libraries WHERE userid=' . $userid . ' AND itemid=' . $itemid . ' LIMIT 1';
  fetch_none($command, 'removing all copies of book from library');
}

function remove_from_library($userid, $itemid, $quantity)
{
  $command = 'UPDATE psypets_libraries SET quantity=quantity-' . $quantity . ' WHERE userid=' . $userid . ' AND itemid=' . $itemid . ' LIMIT 1';
  fetch_none($command, 'removing some copies of book from library');
}

function get_library_book($userid, $itemid)
{
  $command = 'SELECT a.itemid,a.quantity,b.itemname,b.graphic,b.graphictype,b.action,b.custom FROM psypets_libraries AS a,monster_items AS b WHERE b.idnum=' . $itemid . ' AND a.userid=' . $userid . ' AND a.itemid=' . $itemid . ' LIMIT 1';
  return fetch_single($command, 'fetching library book');
}
?>
