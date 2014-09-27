<?php
if($okay_to_be_here !== true)
  exit();

if($user['breeder'] == 'yes')
  $time_between_feeding = 45 * 60;
else
  $time_between_feeding = 60 * 60;

$last_feed_time = (int)$this_inventory['data'];

if($last_feed_time === 0)
{
  $last_feed_time = $now;
  $command = 'UPDATE monster_inventory SET data=\'' . $now . '\' WHERE idnum=' . $this_inventory['idnum'];
  $database->FetchNone($command, 'updating last feed time');
}

if($_POST['action'] == 'feed' && $last_feed_time + $time_between_feeding <= $now)
{
  $itemid = (int)$_POST['itemid'];
  
  $item = get_inventory_byid($itemid);
  $details = get_item_byname($item['itemname']);

  if($item['user'] == $user['user'] && $item['location'] == $this_inventory['location']
    && $details['custom'] == 'no' && $details['cursed'] == 'no'
    && $details['ediblefood'] > 0)
  {
    echo 'The Hungry Chicken devours the ' . $item['itemname'] . ' in one bite, and grins.</p><p>';

    delete_inventory_byid($itemid);

    $command = 'UPDATE monster_inventory SET `data`=' . quote_smart($now) . $extra . ' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'updating exp/level');

    $last_feed_time = $now;
    
    $number = mt_rand(1, 100) + $details['ediblefood'];
    
    if($number <= 40)
      echo '<i>(Feeding the Hungry Chicken a bigger meal will increase the chances that it lays something.)</i>';
    else
    {
      $article = 'a';
    
      if($number <= 60)
      {
        $article = 'an';
        $itemname = 'Egg';
      }
      else if($number <= 80)
        $itemname = 'Speckled Egg';
      else if($number <= 100)
      {
        $article = 'an';
        $itemname = 'Eggplant';
      }
      else if($number <= 120)
        $itemname = 'Blue Egg';
      else if($number <= 130)
        $itemname = 'Blue-Dyed Egg';
      else if($number <= 140)
        $itemname = 'Yellow-Dyed Egg';
      else if($number <= 150)
        $itemname = 'Red-Dyed Egg';
      else if($number <= 160)
        $itemname = 'Purple-Dyed Egg';
      else if($number <= 170)
        $itemname = 'Green-Dyed Egg';
      else if($number <= 180)
      {
        $article = 'an';
        $itemname = 'Orange-Dyed Egg';
      }
      else if($number <= 190)
        $itemname = 'Black-Dyed Egg';
      else
        $itemname = 'Crystal Egg';
        
      echo 'Then it lays ' . $article . ' ' . $itemname . '!</p><p>';
      
      add_inventory($user['user'], '', $itemname, 'Laid by ' . $user['display'] . '\'s Hungry Chicken', $this_inventory['location']);
    }
  }
}

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
        && $details['ediblefood'] > 0)
      {
        $items++;
        $count++;

        if($items == 1)
        {
          echo '<i>(A Hungry Chicken can eat only food items.  Note that the quantity listed is the number available in your house, not the number you will feed.  You will always only give the Hungry Chicken 1 item to eat.)</i></p>' .
               '<form action="itemaction.php?idnum=' . $this_inventory['idnum'] . '" method="post">' .
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
    echo 'There are no items here to feed to the Hungry Chicken.';
}
else
{
  echo 'This Hungry Chicken is sated... for the moment. ' .
       '<i>(You may feed it in ' . Duration($last_feed_time + $time_between_feeding - $now, 2);

  if($user['breeder'] != 'yes')
    echo '; Residents with a Breeder\'s License may feed Hungry Chickens more often';

  echo '.)</i>';
}
?>
