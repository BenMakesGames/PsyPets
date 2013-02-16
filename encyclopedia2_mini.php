<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sellermarketlib.php';
require_once 'commons/rpgfunctions.php';

$itemname = urldecode($_GET['item']);
$itemid = (int)$_GET['i'];

if($itemid > 0)
{
  $command = 'SELECT * FROM monster_items WHERE idnum=' . $itemid . ' LIMIT 1';
  $item = $database->FetchSingle($command, 'fetching item by idnum');
  $itemname = $item['itemname'];
}
else
{
  $command = 'SELECT * FROM monster_items WHERE itemname=' . quote_smart($itemname) . ' LIMIT 1';
  $item = $database->FetchSingle($command, 'fetching item by idnum');
  $itemid = $item['idnum'];
}

if(!$item || $itemid < 1 || $item['custom'] == 'secret')
{
  die('that item does not exist!');
  exit();
}

$is_edible = ($item['is_edible'] == 'yes');
$is_equip = ($item['is_equipment'] == 'yes');
$is_useable = (strlen($item['action']) > 0);
$is_toy = (strlen($item['playdesc']) > 0);
$is_hourly = ($item['hourlyfood'] != 0 || $item['hourlysafety'] != 0 || $item['hourlylove'] != 0 || $item['hourlyesteem'] != 0);
$is_recyclable = ($item['can_recycle'] == 'yes');
$is_pvp_chassis = array_key_exists($item['itemname'], $chassis);
$is_pvp_part = array_key_exists($item['itemname'], $parts); 

if($is_useable > 0)
  $action = explode(';', $item['action']);

if($is_equip)
  $equip_effects = explode(',', $item['equipeffect']);
/*
if($item['durability'] == 0)
  $durability = 'Indestructible';
else if($item['durability'] <= 24)
  $durability = 'Smoke-like';
else if($item['durability'] <= 100)
  $durability = 'Paper-like';
else if($item['durability'] <= 200)
  $durability = 'Cardboard-like';
else if($item['durability'] <= 300)
  $durability = 'Wood-like';
else if($item['durability'] <= 400)
  $durability = 'Metal-like';
else if($item['durability'] <= 500)
  $durability = 'Hypertech/Magic';
else if($item['durability'] <= 600)
  $durability = 'High magic';
else
  $durability = 'Legendary';
*/
$command = 'SELECT COUNT(*) FROM monster_inventory WHERE itemname=' . quote_smart($item['itemname']);
$data = $database->FetchSingle($command, 'encyclopedia2.php');
$number_in_game = (int)$data['COUNT(*)'];

$command = 'SELECT SUM(quantity) FROM monster_market WHERE itemname=' . quote_smart($item['itemname']) . ' GROUP BY itemname';
$data = $database->FetchSingle($command, 'encyclopedia2.php');
$number_in_market = (int)$data['SUM(quantity)'];

$command = 'SELECT SUM(quantity) FROM psypets_basement WHERE itemname=' . quote_smart($item['itemname']) . ' GROUP BY itemname';
$data = $database->FetchSingle($command, 'encyclopedia2.php');
$number_in_basements = (int)$data['SUM(quantity)'];

$total_in_existance = $number_in_game + $number_in_basements + $number_in_market;

$highbid = get_highbid_byitem($item['itemname']);

header('Content-type: text/plain; charset=UTF-8');

$info[] = 'itemname=' . $item['itemname'];
$info[] = 'itemtype=' . $item['itemtype'];
$info[] = 'availability=' . $item['custom'];
$info[] = 'size=' . ($item['bulk'] / 10);
$info[] = 'weight=' . ($item['weight'] / 10);
$info[] = 'number_in_game=' . $total_in_existance;
$info[] = 'number_in_market=' . $number_in_market;

if($item['nosellback'] == 'no')
  $info[] = 'sellback=' . ceil($item['value'] * sellback_rate());

if($item['noexchange'] == 'no')
{
  $command = 'SELECT MIN(price/quantity) AS value,quantity FROM monster_market WHERE itemname=' . quote_smart($item['itemname']) . ' GROUP BY(quantity) ORDER BY value ASC LIMIT 1';
  $marketinfo = $database->FetchSingle($command, 'fetching market info');

  if($marketinfo === false)
    $market_note = 'none';
  else
  {
    if($marketinfo['quantity'] > 1)
      $market_note = round($marketinfo['value'], 2);
    else
      $market_note = (int)$marketinfo['value'];
  }

  $command = 'SELECT monster_inventory.forsale AS min_price,monster_users.display AS display FROM monster_inventory JOIN monster_users WHERE monster_inventory.user=monster_users.user AND monster_inventory.forsale>0 AND monster_users.openstore=\'yes\' AND monster_inventory.itemname=' . quote_smart($itemname) . ' ORDER BY min_price ASC LIMIT 1';
  $fm_item = $database->FetchSingle($command, 'marketsquare.php');

  if($fm_item === false)
    $store_note = 'none';
  else
    $store_note = $fm_item['min_price'];

  $info[] = 'sellers_market_highest_bid=' . ($highbid === false ? 'none' : $highbid['bid']);
  $info[] = 'market_square_lowest_offer=' . $market_note;
  $info[] = 'flea_market_lowest_offer=' . $store_note;
}

$info[] = 'is_food=' . ($is_edible ? 'true' : 'false');
$info[] = 'is_equipment=' . ($is_equip ? 'true' : 'false');
$info[] = 'is_pvp_part=' . (($is_pvp_chassis || $is_pvp_part) ? 'true' : 'false');
$info[] = 'is_recyclable=' . ($is_recyclable ? 'true' : 'false');
$info[] = 'is_toy=' . ($is_toy ? 'true' : 'false');
$info[] = 'is_useable=' . ($is_useable ? 'true' : 'false');
$info[] = 'hourly_effects=' . ($is_hourly ? 'true' : 'false');

echo implode("\r\n", $info) . "\r\n";
?>