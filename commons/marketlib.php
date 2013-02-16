<?php
require_once 'commons/itemlib.php';
require_once 'commons/marketcommons.php';

function add_marketitem($itemname, $seller, $quantity, $price, $num_batches)
{
  global $categories, $now;

  $item = get_item_byname($itemname);

  if($item === false)
    return false;
  else
  {
    $i = strpos($item['itemtype'], '/');
    if($i === false)
      $type = $item['itemtype'];
    else
      $type = substr($item['itemtype'], 0, $i);

    if(!array_key_exists($type, $categories))
      $type = 'misc.';
    else
      $type = $item['itemtype'];

    $q_itemname = quote_smart($itemname);
    $q_itemtype = quote_smart($type);
    $values = "($q_itemname, $q_itemtype, '$seller', '$quantity', '$price', $now)";

    $command = "INSERT INTO `monster_market` (`itemname`, `itemtype`, `seller`, `quantity`, `price`, `postdate`) VALUES " .
               $values;
    if($num_batches > 1)
      $command .= str_repeat(',' . $values, $num_batches - 1);
    
    fetch_none($command, 'listing items in market');

    return true;
  }
}

function get_market_byid($id)
{
  $command = "SELECT * FROM `monster_market` WHERE `idnum`=$id LIMIT 1";
  $item = fetch_single($command, 'marketlib.php/get_market_byid()');

  return $item;
}

function delete_market_byid($id)
{
  $command = "DELETE FROM `monster_market` WHERE `idnum`=$id LIMIT 1";
  fetch_none($command, 'marketlib.php/delete_market_byid()');
}

function get_market_pages_byuser($userid)
{
  $command = 'SELECT COUNT(idnum) AS c FROM monster_market WHERE seller=' . $userid;
  $data = fetch_single($command, 'fetching count');
  
  return ceil($data['c'] / 50);
}

function get_market_byuser($userid, $sort = 'itemname ASC', $page)
{
  $command = 'SELECT * FROM `monster_market` WHERE `seller`=' . $userid . ' ORDER BY ' . $sort . ' LIMIT ' . (($page - 1) * 50) . ',50';
  $items = fetch_multiple($command, 'marketlib.php/get_market_byuser()');

  return $items;
}

function get_market_byitem($itemname, $quantity)
{
  $command = 'SELECT * FROM `monster_market` WHERE `itemname`=' . quote_smart($itemname) . ' AND `quantity`=' . quote_smart($quantity) . ' ORDER BY price ASC LIMIT 1';
  $item = fetch_single($command, 'marketlib.php/get_market_byitem()');

  return $item;
}

function get_market($itemtype)
{
  $command = 'SELECT MIN(price) AS price,idnum,itemname,itemtype,seller,quantity FROM `monster_market` WHERE `itemtype` LIKE ' . quote_smart($itemtype) . ' GROUP BY itemname,quantity ORDER BY itemname ASC';
  $items = fetch_multiple($command, 'marketlib.php/get_market()');

  return $items;
}
?>
