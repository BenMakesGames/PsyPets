<?php
if($okay_to_be_here !== true)
  exit();

$menu = true;

require_once 'commons/equiplib.php';

if($_POST['action'] == 'go')
{
  $id = (int)$_POST['targetid'];

  $item = get_inventory_byid($id);

  if($item['user'] == $user['user'])
  {
    $item_details = get_item_byname($item['itemname']);

    if($item_details['is_equipment'] == 'yes')
    {
      echo '<p>You scan the ' . $item['itemname'] . '...</p>';
    
      $bonuses = array();
      
      foreach($EQUIP_FIELDS as $field)
      {
        if($item_details['equip_' . $field] > 0)
          $bonuses[$field] = $item_details['equip_' . $field];
      }
      
      if(count($bonuses) == 0)
        echo '<p>The Characteristicometer displays: "CHARACTERISTICS: NULL"</p>';
      else
      {
        $total = array_sum($bonuses);
        $average = $total / count($bonuses);
        $min = array_search(min($bonuses), $bonuses);
        $max = array_search(max($bonuses), $bonuses);

        echo '<p>The Characteristicometer displays: "AVERAGE BONUS: ', round($average, 1), '; ',
             'MAXIMUM BONUS: ', strtoupper($max), '; ',
             'MINIMUM BONUS: ', strtoupper($min), '"</p>';

        if(mt_rand(1, 5) == 1)
        {
          echo "<p>You press the CLR button, but instead of clearing the display as usual, the machine begins to spark and hum. ";

          // custom and cursed items never break; others break 50% of the time
          if($item_details['custom'] != 'no' || $item_details['cursed'] == 'yes' || mt_rand(1, 2) == 2)
          {
            echo "The Characteristicometer rapidly heats, forcing you to drop it to the ground, whereupon it breaks.";
            delete_inventory_byid($this_inventory["idnum"]);
            add_inventory($user["user"], '', "Rubble", "The charred remains of a once-splendid " . $this_inventory["itemname"], $this_inventory["location"]);
            
            $AGAIN_WITH_ANOTHER = true;
            $menu = false;
          }
          else
          {
            echo "The leads twitch violently as a powerful current races through them and envelops the " . $item["itemname"] . ".  In mere moments, it is reduced to Rubble.";
            delete_inventory_byid($item["idnum"]);
            add_inventory($user["user"], '', "Rubble", "The charred remains of a once-splendid " . $item["itemname"], $this_inventory["location"]);
          }
          
          echo '</p>';
        }
      }
    }
  }
}

if($menu)
{
  $items = get_houseinventory_byuser_forpets($user["user"]);

  $item_count = 0;
  foreach($items as $item)
  {
    $item_details = get_item_byname($item["itemname"]);
    if($item_details['is_equipment'] == 'yes')
    {
      $item_count++;
      break;
    }
  }

  if($item_count > 0)
  {
?>
<p>Which equipment will you analyze? (only items at home and not in the protected room are listed here)</p>
<form method="post">
<p><input type="hidden" name="action" value="go" /><input type="submit" value="Analyze!" /></p>
<table>
 <tr class="titlerow">
  <th></th>
  <th></th>
  <th>Item Name</th>
  <th>Type</th>
  <th>Comments</th>
 </tr>
<?php
    $rowclass = begin_row_class();

    foreach($items as $item)
    {
      $item_details = get_item_byname($item['itemname']);
      if($item_details['is_equipment'] == 'yes')
      {
?>
 <tr class="<?= $rowclass ?>">
  <td><input type="radio" name="targetid" value="<?= $item["idnum"] ?>" /></td>
  <td class="centered"><?= item_display($item_details) ?></td>
  <td><?= $item['itemname'] ?></td>
  <td><?= $item_details['itemtype'] ?></td>
  <td><?= $item['message'] . '<br />' . $item['message2'] ?></td>
 </tr>
<?php
        $rowclass = alt_row_class($rowclass);
      }
    }
?>
</table>
<p><input type="submit" value="Analyze!" /></p>
<?php
  }
  else
    echo '<p>Cannot find any equipment to analyze.  (Only items at home and not in a protected room can be analyzed.)</p>';
}
?>
