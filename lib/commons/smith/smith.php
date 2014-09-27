<?php
$quantity = (int)$_POST['quantity'];
if($quantity < 1)
  $quantity = 1;

$smith_recipe = $this_smith[(int)$_POST['smithid']];
$ingredients = take_apart(',', $smith_recipe['supplies']);
$itemcounts = array();
foreach($ingredients as $item)
{
  if(strpos($item, '|') !== false)
  {
    $data = explode('|', $item);
    $itemcounts[$data[1]] += $data[0];
  }
  else
    $itemcounts[$item]++;
}

$ok = true;

$itemdescripts = array();
foreach($itemcounts as $item=>$count)
{
  if($inventory[$item]['qty'] < $count * $quantity)
    $ok = false;
}

if(!$ok)
  $error_messages[] = "You don't have all the items I need to do it.";

if($smith_recipe['makes'] == 'Pot of Gold')
  $real_cost = $smith_recipe['cost'];
else
  $real_cost = value_with_inflation($smith_recipe['cost']);

$transaction_cost = $real_cost * $quantity;

if($user['money'] < $transaction_cost)
{
  $ok = false;
  $error_messages[] = "You don't seem to have enough money for it.";
}

if($ok)
{
  require_once 'commons/statlib.php';

  $transaction_value = $transaction_cost;

  foreach($itemcounts as $item=>$count)
  {
    delete_inventory_byname($user['user'], $item, $count * $quantity, 'storage');
    $inventory[$item]['qty'] -= $count;

    $item_details = get_item_byname($item);
    $transaction_value += $item_details['value'] * $count;
  }

  $smithy = get_user_byuser('nfaber', 'idnum');

  $make_list = take_apart(',', $smith_recipe['makes']);
  foreach($make_list as $item)
  {
    // as per favor request by RainRibbon...
    if($item == 'Dragon Pillow')
      $message = 'Ramoth created this item.';
    else
      $message = 'This item was forged at The Smithery.';

    if($item == 'Fancy Chess Set')
      record_stat($user['idnum'], 'Forged a Fancy Chess Set', 1);

    add_inventory_quantity($user['user'], 'u:' . $smithy['idnum'], $item, $message, $user['incomingto'], $quantity);
    $inventory[$item]['qty']++;
  }

  if($transaction_cost > 0)
    take_money($user, $transaction_cost, "Smithery fee");

  header('Location: ./smith.php?msg=13:' . $user['incomingto']);
  exit();
}
?>
