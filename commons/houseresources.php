<?php
function resources_available($list)
{
  global $MATERIALS_LIST;

  $itemlist = take_apart(',', $list);

  if(count($itemlist) == 0)
    return true;

  $ingredients = array();

  foreach($itemlist as $item)
    $ingredients[$item]++;

  foreach($ingredients as $itemname=>$quantity)
  {
    if($MATERIALS_LIST[$itemname] < $quantity)
      return false;
  }

  return true;
}

function resources_unavailable($list)
{
  global $MATERIALS_LIST;

  $itemlist = take_apart(',', $list);

  if(count($itemlist) == 0)
    return array();

  $ingredients = array();

  foreach($itemlist as $item)
    $ingredients[$item]++;

  $unavailable = array();

  foreach($ingredients as $itemname=>$quantity)
  {
    if($MATERIALS_LIST[$itemname] < $quantity)
      $unavailable[$itemname] = array($MATERIALS_LIST[$itemname], $quantity);
  }

  return $unavailable;
}

function expend_resources($list, $username)
{
  global $MATERIALS_LIST;

  $itemlist = take_apart(',', $list);

  if(count($itemlist) == 0)
    return true;

  $ingredients = array();

  foreach($itemlist as $item)
    $ingredients[$item]++;

  foreach($ingredients as $itemname=>$quantity)
  {
    $MATERIALS_LIST[$itemname] -= $quantity;
    if($MATERIALS_LIST[$itemname] == 0)
      unset($MATERIALS_LIST[$itemname]);
    $amount = delete_inventory_fromhome($username, $itemname, $quantity);
  }

  return true;
}
?>
