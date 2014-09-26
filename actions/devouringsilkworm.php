<?php
if($okay_to_be_here !== true)
  exit();

$time_between_feeding = 60 * 60;

if($this_inventory['itemname'] == 'Sated Silkworm')
  $max_level = 4;
else //ex: "Hungry Silkworm (level 4)"
  $max_level = substr($this_inventory['itemname'], 23, strlen($this_inventory['itemname']) - 24);

$data = explode(';', $this_inventory['data']);

$exp = (int)$data[0];
$last_feed_time = (int)$data[1];
$exp_needed = 5 * ($max_level + 1);

$upgraded = false;

if($_POST['action'] == 'feed' && $max_level < 5 && $last_feed_time + $time_between_feeding <= $now)
{
  $itemid = (int)$_POST['itemid'];
  
  $item = get_inventory_byid($itemid);
  $details = get_item_byname($item['itemname']);

  if($item['user'] == $user['user'] && $item['location'] == $this_inventory['location']
    && $details['custom'] == 'no' && $details['cursed'] == 'no'
    && substr($details['itemtype'], 0, 10) == 'plant/leaf')
  {
    delete_inventory_byid($itemid);

    $food_value = $details['ediblefood'] + 1;

    $extra = '';

    echo 'The Hungry Silkworm devours the ' . $item['itemname'] . '!';

    $exp += $food_value;
    $new_level = $max_level;
    while($exp >= $exp_needed && $new_level < 4)
    {
      $new_level++;
      $exp -= $exp_needed;
      $exp_needed = pow(2, $new_level);
    }

    if($new_level > $max_level)
    {
      $upgraded = true;

      if($new_level == 4)
      {
        echo '</p><p><i>(The Hungry Silkworm has been sated!)</i>';

        $extra = ',itemname=' . quote_smart('Sated Silkworm');
      }
      else
      {
        echo '</p><p><i>(The Hungry Silkworm has increased in level!)</i>';

        $extra = ',itemname=' . quote_smart('Hungry Silkworm (level ' . $new_level . ')');
      }
    }
    else
      echo '</p><p>';

    $command = 'UPDATE monster_inventory SET `data`=' . quote_smart($exp . ';' . $now) . $extra . ' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'updating exp/level');

    $last_feed_time = $now;
  }
}

if($max_level < 4 && !$upgraded)
{
  echo '<b>' . $exp . ' / ' . $exp_needed . ' experience points</b></p><p>';

  if($last_feed_time + $time_between_feeding <= $now)
  {
    $inventory = get_inventory_byuser($user['user'], $this_inventory['location']);

    $items = 0;
    $rowclass = begin_row_class();
    $previous = '';
    $count = 0;

    foreach($inventory as $item)
    {
      if($item['idnum'] != $this_inventory['idnum'])
      {
        $details = get_item_byname($item['itemname']);

        if($details['custom'] == 'no' && $details['cursed'] == 'no'
          && substr($details['itemtype'], 0, 10) == 'plant/leaf')
        {
          $items++;
          $count++;

          if($items == 1)
          {
            echo '<i>(A Hungry Silkworm will only eat items of type "plant/leaf".  Note that the quantity listed is the number available in your house, not the number you will feed.  You will always only give the Hungry Silkworm 1 item to eat.)</i></p>' .
                 '<form method="post">' .
                 '<table>' .
                 '<tr class="titlerow">' .
                 '<th></th><th></th><th>Item</th><th>Quantity</th>' .
                 '</tr>';
            $count--;
          }
          else if($previous != $item['itemname'])
          {
?>
  <td class="centered"><?= $count ?></td>
 </tr>
<?php
            $count = 0;
          }
          else
            continue;
?>
 <tr class="<?= $rowclass ?>">
  <td><input type="radio" name="itemid" value="<?= $item['idnum'] ?>" /></td><td class="centered"><?= item_display($details) ?></td><td><?= $item['itemname'] ?></td>
<?php
          $rowclass = alt_row_class($rowclass);

          $previous = $item['itemname'];
        }
      }
    }

    if($items > 0)
    {
      echo ' <td class="centered">' . ($count + 1) . '</td></tr>' .
           '</table><p>' .
           '<p><input type="hidden" name="action" value="feed" /><input type="submit" value="Feed" /></p></form><p>';
    }
    else
      echo 'There are no items here to feed to the Hungry Silkworm.';
  }
  else
    echo 'This Hungry Silkworm is sated... for the moment.';
}
?>
<h5>Hungry Silkworms in this Room</h5>
<?php
$command = 'SELECT itemname,data,idnum FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=' . quote_smart($this_inventory['location']) . ' AND itemname LIKE \'Hungry Silkworm%\' ORDER BY itemname ASC';
$items = $database->FetchMultiple($command, 'fetching silkworms');

if(count($items) == 0)
  echo '<p>There are no other Hungry Silkworms in this room.</p>';
else
{
  echo '<ul>';

  foreach($items as $item)
  {
    echo '<li>';

    if($item['idnum'] == $this_inventory['idnum'])
      echo '<b>' . $this_inventory['itemname'] . '</b>';
    else
      echo '<a href="itemaction.php?idnum=' . $item['idnum'] . '">' . $item['itemname'] . '</a>';

    echo '</li>';
  }

  echo '</ul>';
}
?>
