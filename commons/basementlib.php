<?php
function get_basement_count($userid, $locid = 0)
{
  $command = 'SELECT COUNT(idnum) AS c FROM psypets_basement WHERE userid=' . (int)$userid;
  $data = fetch_single($command, 'fetching basement count');

  return (int)$data['c'];
}

function levelup_basement($userid, $locid = 0)
{
  $command = 'UPDATE monster_houses SET maxbasement=maxbasement+100 WHERE userid=' . $userid . ' LIMIT 1';
  fetch_none($command, 'updating basement size');
}

function leveldown_basement($userid, $floors = 1, $locid = 0)
{
  $command = 'UPDATE monster_houses SET maxbasement=maxbasement-' . ($floors * 100) . ' WHERE userid=' . $userid . ' LIMIT 1';
  fetch_none($command, 'updating basement size');
}

function get_basement_items($userid, $locid, $first, $limit)
{
  $command = 'SELECT * FROM psypets_basement WHERE userid=' . (int)$userid . ' ORDER BY itemname ASC LIMIT ' . $first . ',' . $limit;
  $items = fetch_multiple($command, 'fetching basement items');

  return $items;
}

function get_basement_item_byname($userid, $locid, $itemname)
{
  $command = 'SELECT * FROM psypets_basement WHERE userid=' . (int)$userid . ' AND itemname=' . quote_smart($itemname) . ' LIMIT 1';
  $item = fetch_single($command, 'fetching item from basement');

  return $item;
}

function add_to_basement($userid, $locid, $itemname, $quantity)
{
  $command = 'SELECT idnum FROM psypets_basement WHERE userid=' . (int)$userid . ' AND itemname=' . quote_smart($itemname) . ' LIMIT 1';
  $existing_item = fetch_single($command, 'fetching existing item from basement');
  
  if($existing_item === false)
  {
    $command = 'INSERT INTO psypets_basement (userid, itemname, quantity) VALUES ' .
               '(' . $userid . ', ' . quote_smart($itemname) . ', ' . (int)$quantity . ')';
    fetch_none($command, 'basementlib.php/add_to_basement()');
  }
  else
  {
    $id = $existing_item['idnum'];

    $command = 'UPDATE psypets_basement SET quantity=quantity+' . (int)$quantity . ' WHERE idnum=' . $id . ' LIMIT 1';
    fetch_none($command, 'basementlib.php/add_to_basement()');
  }
}

function clean_up_basement($userid)
{
  $command = 'DELETE FROM psypets_basement WHERE userid=' . (int)$userid . ' AND quantity=0';
  fetch_none($command, 'basementlib.php/clean_up_basement()');
}

function remove_from_basement($userid, $locid, $itemname, $quantity)
{
  $command = 'UPDATE psypets_basement SET quantity=quantity-' . (int)$quantity . ' WHERE userid=' . $userid . ' AND itemname=' . quote_smart($itemname) . ' LIMIT 1';
  fetch_none($command, 'basementlib.php/remove_from_basement()');
}

function recalc_basement_bulk($userid, $locid)
{
  $command = 'SELECT SUM(quantity) AS c FROM psypets_basement WHERE userid=' . (int)$userid;
  $data = fetch_single($command, 'fetching basement bulk');
  
  $command = 'UPDATE monster_houses SET curbasement=' . (int)$data['c'] . ' WHERE userid=' . $userid . ' LIMIT 1';
  fetch_none($command, 'recalculating basement bulk');
}
?>
