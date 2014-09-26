<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/hungryvalue.php';

$SATED_LEVEL = 4;

$time_between_feeding = (int)$action_info[2] * 60 * 60;

if($this_inventory['itemname'] == 'Sated Cherub')
  $max_level = $SATED_LEVEL;
else //ex: "Hungry Cherub (level 2)"
  $max_level = substr($this_inventory['itemname'], 21, strlen($this_inventory['itemname']) - 22);

$data = explode(';', $this_inventory['data']);

$exp = (int)$data[0];
$last_feed_time = (int)$data[1];
$exp_needed = 2 * ceil(pow(2.7, $max_level)) + 2;

$upgraded = false;

if($_POST['action'] == 'feed' && $max_level < $SATED_LEVEL && $last_feed_time + $time_between_feeding <= $now)
{
  $itemid = (int)$_POST['itemid'];
  
  $item = get_inventory_byid($itemid);
  $details = get_item_byname($item['itemname']);

  if($item['user'] == $user['user'] && $item['location'] == $this_inventory['location']
    && $details['custom'] == 'no' && $details['cursed'] == 'no'
    && substr($details['itemtype'], 0, 5) != 'food/' && substr($details['itemtype'], 0, 6) != 'plant/')
  {
    delete_inventory_byid($itemid);

    $food_value = cherub_food_value($item['itemname']);

    $extra = '';

    if($food_value > 0)
    {
      echo 'The Hungry Cherub devours the ' . $item['itemname'] . ' in one bite, and grins.';

      $exp += $food_value;
      $new_level = $max_level;
      while($exp >= $exp_needed && $new_level < $SATED_LEVEL)
      {
        $new_level++;
        $exp -= $exp_needed;
        $exp_needed = pow(2, $new_level);
      }

      if($new_level > $max_level)
      {
        $AGAIN_WITH_SAME = true;

        $upgraded = true;

        if($new_level == $SATED_LEVEL)
        {
          echo '</p><p><i>(The Hungry Cherub has been sated!)</i>';

          $extra = ',itemname=' . quote_smart('Sated Cherub');
        }
        else
        {
          echo '</p><p><i>(The Hungry Cherub has increased in level!)</i>';

          $extra = ',itemname=' . quote_smart('Hungry Cherub (level ' . $new_level . ')');
        }

        require_once 'commons/statlib.php';
        record_stat($user['idnum'], 'Leveled-up a Hungry Cherub', 1);
      }
      else
        echo '</p><p>';
    }
    else
    {
      echo 'The Hungry Cherub devours the ' . $item['itemname'] . ' in one bite, coughs a little, and spits up some Rubble.  It apparently didn\'t like the meal you gave it.</p><p>';
      add_inventory($user['user'], '', 'Rubble', 'Coughed up by a Hungry Cherub', $this_inventory['location']);
    }

    $command = 'UPDATE monster_inventory SET `data`=' . quote_smart($exp . ';' . $now) . $extra . ' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'updating exp/level');

    $last_feed_time = $now;
  }
}

if($max_level < $SATED_LEVEL && !$upgraded)
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
          && substr($details['itemtype'], 0, 5) != 'food/' && substr($details['itemtype'], 0, 6) != 'plant/')
        {
          $items++;
          $count++;

          if($items == 1)
          {
            echo '<i>(A Hungry Cherub can eat a variety of items, however not all are good for it.  Note that the quantity listed is the number available in your house, not the number you will feed.  You will always only give the Hungry Cherub 1 item to eat.)</i></p>' .
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
      echo 'There are no items here to feed to the Hungry Cherub.';
  }
  else
    echo 'This Hungry Cherub is sated... for the moment.';
}
?>
<h5>Hungry Cherubim in this Room</h5>
<?php
$command = 'SELECT itemname,data,idnum FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=' . quote_smart($this_inventory['location']) . ' AND itemname LIKE \'Hungry Cherub%\' ORDER BY itemname ASC';
$items = $database->FetchMultiple($command, 'fetching cherubim');

if(count($items) == 0)
  echo '<p>There are no other Hungry Cherubim in this room.</p>';
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
