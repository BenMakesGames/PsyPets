<?php
require_once 'commons/globals.php';

// game economy as of Jan 13th, 2009
$core_items = array(
  '3-Leaf Clover' => 4.39,
  'Amber' => 248.18,
  'Arugula' => 3.46,
  'Baking Chocolate' => 55.89,
  'Baking Soda' => 3.86,
  'Beet' => 65.64,
  'Blood' => 9.71,
  'Blue Dye' => 20.39,
  'Blueberries' => 46.68,
  'Carrot' => 36.18,
  'Celery' => 5.93,
  'Chalk' => 4.57,
  'Chicken' => 76.36,
  'Clay' => 73.75,
  'Clover Leaf' => 300.54,
  'Coal' => 59.43,
  'Coconut' => 92.61,
  'Copper' => 84.89,
  'Corn' => 115.82,
  'Delicious' => 8.96,
  'Egg' => 74.25,
  'Eggplant' => 23.68,
  'Feather' => 59.36,
  'Fish' => 56.36,
  'Fluff' => 26.75,
  'Ginger' => 4.50,
  'Gold' => 95.57,
  'Gossamer' => 971.25,
  'Greenish Leaf' => 4.39,
  'Iron' => 129.86,
  'Leafy Cabbage' => 55.25,
  'Leather' => 72.36,
  'Mint Leaves' => 7.86,
  'Onion' => 38.61,
  'Orange' => 16.25,
  'Paper' => 16.89,
  'Peanuts' => 10.96,
  'Potato' => 99.68,
  'Prickly Green' => 47.61,
  'Raw Milk' => 70.29,
  'Red Dye' => 86.04,
  'Redsberries' => 35.89,
  'Rice' => 38.75,
  'Rubber' => 218.07,
  'Scales' => 6.00,
  'Shortening' => 29.11,
  'Silver' => 85.75,
  'Small Giamond' => 110.32,
  'Small Rock' => 36.11,
  'Sour Lime' => 7.54,
  'Soy Bean' => 9.29,
  'Speckled Egg' => 80.36,
  'Steak' => 75.04,
  'Sugar Beet' => 17.39,
  'Talon' => 87.93,
  'Tea Leaves' => 40.71,
  'Tin' => 123.54,
  'Venom' => 45.00,
  'Wax' => 73.57,
  'Wheat' => 19.18,
  'White Radish' => 92.36,
  'Wild Oats' => 7.93,
  'Wood' => 142.82,
  'Yam' => 108.25,
  'Yellow Dye' => 20.50,
);
/*
LtC: 500m
BL: 10000m
allowance: 40m
*/

function value_with_inflation($value)
{
  $factor = get_global('economy_factor');

  if($factor == 0) die('Economy inflation factor appears to be zero!?  ' . $SETTINGS['author_resident_name'] . ' should certainly be notified of this terrible error.');

  return round($value * $factor);
}

function storage_fees($oversize, $fraction)
{
  if($oversize <= 0)
    return 0;

  $factor = get_global('economy_factor');

  if($factor == 0) die('Economy inflation factor appears to be zero!?  ' . $SETTINGS['author_resident_name'] . ' should certainly be notified of this terrible error.');

  return floor($oversize * $factor / $fraction);
}

function recycle_value($item, $factor = 1.0)
{
  $recycle_for = take_apart(',', $item['recycle_for']);

  $value = 0;

  if(count($recycle_for) > 0)
  {
    foreach($recycle_for as $part)
    {
      $part_item = get_item_byname($part);

      $value += $part_item['value'];
    }
  }

  $value = floor($value / $item['recycle_fraction']);

  return ceil($value * $factor);
}
?>