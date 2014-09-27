<?php
require_once 'commons/itemlib.php';
require_once 'commons/marketcommons.php';

function add_sellermarketbid($itemname, $buyer, $quantity, $bid)
{
  $item = get_item_byname($itemname);

  if($item === false)
    return false;
  else
  {
    $q_itemname = quote_smart($itemname);
    $command = 'INSERT INTO `psypets_reversemarket` (`itemname`, `buyer`, `quantity`, `bid`) VALUES ' .
               "($q_itemname, '$buyer', '$quantity', '$bid')";
    
    fetch_none($command, 'listing items in reverse market');

    return true;
  }
}

function get_sellermarket_byid($id)
{
  $command = "SELECT * FROM `psypets_reversemarket` WHERE `idnum`=$id LIMIT 1";
  $item = fetch_single($command, 'marketlib.php/get_market_byid()');

  return $item;
}

function delete_sellermarket_byid($id)
{
  $command = "DELETE FROM `psypets_reversemarket` WHERE `idnum`=$id LIMIT 1";
  fetch_none($command, 'deleting bid');
}

function get_sellermarket_byuser($userid)
{
  $command = "SELECT * FROM `psypets_reversemarket` WHERE `buyer`=$userid ORDER BY itemname ASC";
  $items = fetch_multiple($command, 'marketlib.php/get_market_byuser()');

  return $items;
}

function get_sellermarket_byitem($itemname, $quantity)
{
  $command = 'SELECT * FROM `psypets_reversemarket` WHERE `itemname`=' . quote_smart($itemname) . ' AND `quantity`=' . quote_smart($quantity) . ' ORDER BY bid DESC LIMIT 1';
  $item = fetch_single($command, 'marketlib.php/get_market_byitem()');

  return $item;
}

function get_sellermarket($itemtype)
{
  $command = 'SELECT bid,idnum,itemname,itemtype,buyer,SUM(quantity) AS quantity FROM `psypets_reversemarket` WHERE `itemtype` LIKE ' . quote_smart($itemtype) . ' GROUP BY bid ORDER BY itemname ASC,bid ASC';
  $items = fetch_multiple($command, 'fetching seller\'s market');

  return $items;
}

function sell_to_sellers_market($item, $minimum)
{
  $bid = get_highbid_byitem($item['itemname'], $minimum);
  
  if($bid === false)
    return false;
  else
  {
    $sold = meet_bids($item['itemname'], 1, $bid['bid']);
    
    if($sold == 0)
      return false;
    else
      return $sold * $bid['bid'];
  }
}

function get_highbid_byitem($itemname, $minimum)
{
  $command = 'SELECT bid,SUM(quantity) AS quantity FROM psypets_reversemarket WHERE itemname=' . quote_smart($itemname) . ' AND bid>=' . (int)$minimum . ' GROUP BY(bid) ORDER BY bid DESC LIMIT 1';
  $bid = $GLOBALS['database']->FetchSingle($command);
  
  return $bid;
}

function get_low_highbid_byitem($itemname)
{
  $command = 'SELECT bid,SUM(quantity) AS quantity FROM psypets_reversemarket WHERE itemname=' . quote_smart($itemname) . ' GROUP BY(bid) ORDER BY bid DESC LIMIT 1';
  $bid = $GLOBALS['database']->FetchSingle($command);

  return $bid;
}

function get_bid_byid($id)
{
  $command = 'SELECT * FROM psypets_reversemarket WHERE idnum=' . (int)$id . ' LIMIT 1';
  $item = $GLOBALS['database']->FetchSingle($command);

  return $item;
}

function get_bids_byuser($userid, $sort = 0)
{
  if($sort == 1)
    $order_by = 'bid DESC';
  else if($sort == 2)
    $order_by = 'quantity DESC';
  else
    $order_by = 'itemname ASC';

  $command = 'SELECT * FROM psypets_reversemarket WHERE buyer=' . (int)$userid . ' ORDER BY ' . $order_by;
  $items = fetch_multiple($command, 'fetching player\'s bids');
  
  return $items;
}

function meet_bids($itemname, $quantity, $bid_amount)
{
  global $SETTINGS;

  if($quantity == 0)
    return 0;

  $command = 'SELECT idnum,buyer,quantity FROM psypets_reversemarket WHERE itemname=' . quote_smart($itemname) . ' AND bid=' . $bid_amount; 
  $bids = fetch_multiple($command);

  $sold = 0;

  if(count($bids) == 0)
    return 0;
  else
  {
    foreach($bids as $bid)
    {
      $buyer = get_user_byid($bid['buyer'], 'user');

      if($quantity >= $bid['quantity'])
      {
        delete_sellermarket_byid($bid['idnum']);

        if($buyer === false)
          psymail_user($SETTINGS['author_login_name'], $SETTINGS['site_ingame_mailer'], 'Seller\'s Market: selling items to non-existant user', '{r ' . $user['display'] . '} sold ' . $bid['quantity'] . 'x ' . $itemname . ' to {i}{#888888}[Departed #' . $bid['buyer'] . ']{/}{/}');
        else
        {
          add_inventory_quantity($buyer['user'], '', $itemname, 'Bought from the Seller\'s Market', 'storage/incoming', $bid['quantity']);
          flag_new_incoming_items($buyer['user']);
        }

        $sold += $bid['quantity'];

        $quantity -= $bid['quantity'];

        if($quantity == 0)
          break;
      }
      else
      {
        $command = 'UPDATE psypets_reversemarket SET quantity=quantity-' . $quantity . ' WHERE idnum=' . $bid['idnum'] . ' LIMIT 1';
        fetch_none($command, 'updating seller\'s market record');

        if($buyer === false)
          psymail_user($SETTINGS['author_login_name'], $SETTINGS['site_ingame_mailer'], 'Seller\'s Market: selling items to non-existant user', '{r ' . $user['display'] . '} sold ' . $quantity . 'x ' . $itemname . ' to {i}{#888888}[Departed #' . $bid['buyer'] . ']{/}{/}');
        else
        {
          add_inventory_quantity($buyer['user'], '', $itemname, 'Bought from the Seller\'s Market', 'storage/incoming', $quantity);
          flag_new_incoming_items($buyer['user']);
        }

        $sold += $quantity;

        break;
      }
    }
  }
  
  return $sold;
}
?>
