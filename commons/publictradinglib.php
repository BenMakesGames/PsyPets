<?php
function get_bids_on_trade($tradeid)
{
  $command = 'SELECT * FROM psypets_trading_house_bids WHERE tradeid=' . $tradeid;
  return fetch_multiple($command, 'fetching bids for trade #' . $tradeid);
}

function has_bid_on_trade($userid, $tradeid)
{
  $command = 'SELECT idnum FROM psypets_trading_house_bids WHERE userid=' . $userid . ' AND tradeid=' . $tradeid . ' LIMIT 1';
  return fetch_single($command, 'fetching resident\'s trade on this bid');
}

function get_bid($idnum)
{
  $command = 'SELECT * FROM psypets_trading_house_bids WHERE idnum=' . $idnum . ' LIMIT 1';
  return fetch_single($command, 'fetching bid #' . $idnum);
}

function update_trade_description($idnum, $sdesc, $ldesc)
{
  $command = 'UPDATE psypets_trading_house_requests SET sdesc=' . quote_smart($sdesc) . ',ldesc=' . quote_smart($ldesc) . ' WHERE idnum=' . $idnum . ' LIMIT 1';
  fetch_none($command, 'updating trade description');
}

function post_public_trade_cost($num_trades)
{
  return value_with_inflation($num_trades * 10);
}

function get_bid_count($tradeid)
{
  $command = 'SELECT COUNT(idnum) AS c FROM psypets_trading_house_bids WHERE tradeid=' . $tradeid;
  $data = fetch_single($command, 'fetching bid count');
  
  return (int)$data['c'];
}
?>
