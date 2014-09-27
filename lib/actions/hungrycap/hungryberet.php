<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/hungryvalue.php';

$time_between_feeding = 60 * 60;

$data = explode(';', $this_inventory['data']);

$exp = (int)$data[0];
$last_feed_time = (int)$data[1];
$transformed = false;

// CUSTOMIZE THIS
// --------------
$exp_needed = 40;
$upgrade_options = array('Sated Balmoral', 'Sated Caubeen');
// ==============

if($_POST['action'] == 'feed' && $last_feed_time + $time_between_feeding <= $now && $exp < $exp_needed)
{
  $itemid = (int)$_POST['itemid'];

  $item = get_inventory_byid($itemid);
  $details = get_item_byname($item['itemname']);

  if($item['user'] == $user['user'] && $item['location'] == $this_inventory['location']
    && $details['custom'] == 'no' && $details['cursed'] == 'no'
    && substr($details['itemtype'], 0, 5) != 'food/' && substr($details['itemtype'], 0, 6) != 'plant/')
  {
    delete_inventory_byid($itemid);

    $food_value = shovel_food_value($item['itemname']);

    $extra = '';

    if($food_value > 0)
    {
      echo '<p>The ' . $this_inventory['itemname'] . ' devours the ' . $item['itemname'] . ' in one bite, and grins.</p>';

      $exp += $food_value;
    }
    else
    {
      echo 'The ' . $this_inventory['itemname'] . ' devours the ' . $item['itemname'] . ' in one bite, coughs a little, and spits up some Rubble.  It apparently didn\'t like the meal you gave it.';
      add_inventory($user['user'], '', 'Rubble', 'Coughed up by a ' . $this_inventory['itemname'], $this_inventory['location']);
    }

    $command = 'UPDATE monster_inventory SET `data`=' . quote_smart($exp . ';' . $now) . ' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'updating exp and feed time');

    $last_feed_time = $now;
  }
}
else if($_GET['action'] == 'transform' && $exp >= $exp_needed && array_key_exists($_GET['transform'], $upgrade_options))
{
  $transformed = true;
  
  $new_itemname = $upgrade_options[$_GET['transform']];
  
  $exp -= $exp_needed;
  
  $command = 'UPDATE monster_inventory SET `data`=' . quote_smart($exp . ';' . $last_feed_time) . ',itemname=' . quote_smart($new_itemname) . ' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updaging exp, and transforming!');
}

if($transformed)
{
  echo '<p>The ' . $this_inventory['itemname'] . ' has transformed into a ' . $new_itemname . '!</p>';
  $AGAIN_WITH_ANOTHER = true;
}
else
{
  echo '<p><b>' . $exp . ' / ' . $exp_needed . ' experience points</b></p>';

  if($exp >= $exp_needed)
  {
    // level it up

    echo '<p>The ' . $this_inventory['itemname'] . ' is ready to transform!</p>',
         '<p>What should it transform in to?</p><ul>';

    foreach($upgrade_options as $id=>$itemname)
    {
      echo '<li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&amp;action=transform&amp;transform=' . $id . '">' . $itemname . '</a></li>';
    }

    echo '</ul>';
  }
  else
  {
    // feed it!

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
              echo '<p><i>(A ' . $this_inventory['itemname'] . ' can eat a variety of items, however not all are good for them.  Note that the quantity listed is the number available in this room, not the number you will feed.  You will always only give the ' . $this_inventory['itemname'] . ' one item to eat.)</i></p>' .
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
             '</table>' .
             '<p><input type="hidden" name="action" value="feed" /><input type="submit" value="Feed" /></p></form>';
      }
      else
        echo '<p>There are no items here to feed to the ' . $this_inventory['itemname'] . '.</p>';
    }
    else
      echo '<p>This ' . $this_inventory['itemname'] . ' is sated... for the moment.</p>';
  }
}
?>
