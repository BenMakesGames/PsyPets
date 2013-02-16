<?php
require_once 'commons/itemstats.php';

$recovery = false;
$recovered = array();

foreach($_POST as $key=>$value)
{
  if($key{0} == 'i' && $key{1} == '_' && $value > 0)
  {
    $itemid = (int)substr($key, 2);
    $quantity = (int)$value;

    $item = get_inventory_byid($itemid);

    if($item['user'] == $user['user'] && $item['location'] == 'storage')
    {
      $details = get_item_byname($item['itemname']);

      if(strlen($details['recycle_for']) > 0 && $details['can_recycle'] == 'yes')
      {
        $recovery = true;
        $parts = explode(',', $details['recycle_for']);

        if($details['recycle_fraction'] > 1)
        {
          $num_parts = floor(count($parts) / $details['recycle_fraction']);
          $parts = array_slice($parts, mt_rand(0, $details['recycle_fraction'] - 1) * $num_parts, $num_parts);
        }

        if($details['durability'] > 0)
          $percent = floor((60 * $item['health']) / $details['durability']);
        else
          $percent = 60;

        $database->FetchNone('
          DELETE FROM monster_inventory
          WHERE
            user=' . quote_smart($user['user']) . '
            AND location=\'storage\'
            AND itemname=' . quote_smart($item['itemname']) . '
            AND health=' . $item['health'] . '
          LIMIT ' . $quantity . '
        ');

        $quantity = $database->AffectedRows();

        foreach($parts as $part)
        {
          if($part == 'Bat Wing')
            $r_percent = 100;
          else if($part == 'Red Dye' || $part == 'Yellow Dye' || $part == 'Blue Dye' || $part == 'Black Dye')
            $r_percent = $percent * 3 / 4;
          else
            $r_percent = $percent;

          for($x = 0; $x < $quantity; ++$x)
          {
            if(mt_rand(1, 100) <= $r_percent)
            {
              add_inventory_cached($user['user'], '', $part, 'Recovered from Recycling', $user['incomingto']);
              $recovered[] = $part;
            }
          }
        }

        $points = greenhouse_food_value($item['itemname']) * $quantity;
        $greenhouse_points += $points;

        record_item_disposal($item['itemname'], 'recycled', $quantity);

        process_cached_inventory();
        
        $recycle_count += $quantity;
      } // if the item can be recycled
    } // if we own the item, and it's in the correct place
  } // if the key is an item number prefixed with "i_"
} // for each key
?>