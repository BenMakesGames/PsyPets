<?php
if($okay_to_be_here !== true)
  exit();

if($user['breeder'] == 'yes')
  $time_between_feeding = 45 * 60;
else
  $time_between_feeding = 60 * 60;

$data = explode(';', $this_inventory['data']);

$exp = (int)$data[0];
$last_feed_time = (int)$data[1];
$exp_needed = 100;

$upgraded = false;

if($_POST['action'] == 'feed' && $last_feed_time + $time_between_feeding <= $now)
{
  $itemid = (int)$_POST['itemid'];
  
  $item = get_inventory_byid($itemid);

  if($item['user'] == $user['user'] && $item['location'] == $this_inventory['location'] &&
    ($item['itemname'] == 'Feather' || $item['itemname'] == 'Fluff' || $item['itemname'] == 'Phoenix Down'))
  {
    if($item['itemname'] == 'Fluff')
      $food_value = 2;
    else if($item['itemname'] == 'Feather')
      $food_value = 8;
    else if($item['itemname'] == 'Phoenix Down')
      $food_value = 20;

    $extra = '';

    if($food_value > 0)
    {
      delete_inventory_byid($itemid);

      echo 'You carefully wrap the ' . $item['itemname'] . ' around the ' . $this_inventory['itemname'] . '...</p><p>';

      $exp += $food_value;

      if($exp >= $exp_needed)
      {
        if($this_inventory['itemname'] == 'Phoenix Egg')
        {
          create_random_offspring($user['user'], 1, array('phoenix/firebird_red.png', 'phoenix/firebird_yellow.png', 'phoenix/icebird.png'), true);
          echo 'Oh!  It\'s shaking!</p><p>You take a step back and watch, entranced, as a small bird emerges from the egg...</p><p>';
        }

        delete_inventory_byid($this_inventory['idnum']);
      }
      else
      {
        $command = 'UPDATE monster_inventory SET `data`=' . quote_smart($exp . ';' . $now) . $extra . ' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
        $database->FetchNone($command, 'updating exp/level');

        $last_feed_time = $now;
      }
    }
    else
    {
      echo $item['itemname'] . '?  For nesting an egg?  Ridiculous!';
    }
  }
}

if($exp < $exp_needed)
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

        if($item['itemname'] == 'Feather' || $item['itemname'] == 'Fluff' || $item['itemname'] == 'Phoenix Down')
        {
          $items++;
          $count++;

          if($items == 1)
          {
            echo '<i>(' . $this_inventory['itemname'] . 's must be incubated with Fluff or Feathers.  Note that the quantity listed is the number available in your house, not the number you will use.  You will always only use 1 item at a time.)</i></p>' .
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
           '<p><input type="hidden" name="action" value="feed" /><input type="submit" value="Use" /></p></form><p>';
    }
    else
      echo 'There are no items here to incubate this ' . $this_inventory['itemname'] . ' with.';
  }
  else
  {
    echo 'This ' . $this_inventory['itemname'] . ' is warm... for the moment.</p><p><i>(You may use an item for incubation again in ' . Duration($last_feed_time + $time_between_feeding - $now) . '.';
    if($user['breeder'] == 'no')
      echo '  Residents with the Breeder\'s License can do this faster.';
    echo ')</i>';
  }
}
?>
