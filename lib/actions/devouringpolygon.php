<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/hungryvalue.php';

$SATED_LEVEL = 6;

$time_between_feeding = 2 * 60 * 60;

switch($this_inventory['itemname'])
{
  case 'Sated Infinitygon':    $max_level = $SATED_LEVEL; break;
  case 'Hungry Septagon':      $max_level = 5; break;
  case 'Hungry Hexagon':       $max_level = 4; break;
  case 'Hungry Pentagon':      $max_level = 3; break;
  case 'Hungry Quadrilateral': $max_level = 2; break;
  case 'Hungry Triangle':      $max_level = 1; break;
  default:                     die('Terrible error!');
}

$next_shape_list = array(
  'Hungry Triangle' => 'Hungry Quadrilateral',
  'Hungry Quadrilateral' => 'Hungry Pentagon',
  'Hungry Pentagon' => 'Hungry Hexagon',
  'Hungry Hexagon' => 'Hungry Septagon',
  'Hungry Septagon' => 'Sated Infinitygon',
  'Sated Infinitygon' => 'Hungry Triangle',
);

$data = explode(';', $this_inventory['data']);

$exp = (int)$data[0];
$last_feed_time = (int)$data[1];

if($max_level == $SATED_LEVEL)
  $exp_needed = 1;
else
  $exp_needed = $max_level * ($max_level + 1) * 5 + 10;

$upgraded = false;

if($_POST['action'] == 'feed' && ($last_feed_time + $time_between_feeding <= $now))
{
  $itemid = (int)$_POST['itemid'];
  
  $item = get_inventory_byid($itemid);
  $details = get_item_byname($item['itemname']);

  if($item['user'] == $user['user'] && $item['location'] == $this_inventory['location']
    && $details['custom'] == 'no' && $details['cursed'] == 'no'
    && substr($details['itemtype'], 0, 5) != 'food/' && substr($details['itemtype'], 0, 6) != 'plant/')
  {
    delete_inventory_byid($itemid);

    $food_value = polygon_food_value($item['itemname']);

    $extra = '';

    if($food_value > 0)
    {
      echo 'The ' . $this_inventory['itemname'] . ' devours the ' . $item['itemname'] . ' in one bite, and grins.';

      $exp += $food_value;
      $new_level = $max_level;
      while($exp >= $exp_needed)
      {
        $new_level++;
        
        if($new_level > $SATED_LEVEL)
        {
          $exp = 0;
          $exp_needed = 1 * 2 + 8;
          break;
        }
        else if($new_level == $SATED_LEVEL)
        {
          $exp = 0;
          $exp_needed = 1;
        }
        else
        {
          $exp -= $exp_needed;
          $exp_needed = $max_level * ($max_level + 1) * 2 + 8;
        }
      }

      if($new_level > $max_level)
      {
        if($new_level > $SATED_LEVEL)
        {
          echo '</p><p><i>(The ' . $this_inventory['itemname'] . ' has eaten too much!  It explodes, leaving a ' . $next_shape_list[$this_inventory['itemname']] . ' in its place...)</i>';
          echo '</p><p>Iridescent points and lines flit and flicker around your house, to the delight of your pets.';
          
          $command = 'UPDATE monster_pets SET `energy`=12+`sta`*2+`athletics`+`str`,`food`=12+(`sta`+`sur`)*2,`safety`=24,`love`=48+`extraverted`*2,`esteem`=48+`conscientious`*2,`sleeping`=\'no\' WHERE user=' . quote_smart($user['user']) . ' AND location=\'home\' AND dead=\'no\' AND zombie=\'no\'';
          $database->FetchNone($command, 'making awesome every pet');
        }
        else
          echo '</p><p><i>(The ' . $this_inventory['itemname'] . ' has become a ' . $next_shape_list[$this_inventory['itemname']] . '!)</i>';
        
        $AGAIN_WITH_SAME = true;

        $upgraded = true;

        $extra = ',itemname=' . quote_smart($next_shape_list[$this_inventory['itemname']]);
      }
      else
        echo '</p><p>';
    }
    else
    {
      echo 'The ' . $this_inventory['itemname'] . ' devours the ' . $item['itemname'] . ' in one bite, coughs a little, and spits up some Rubble.  It apparently didn\'t like the meal you gave it.</p><p>';
      add_inventory($user['user'], '', 'Rubble', 'Coughed up by a ' . $this_inventory['itemname'], $this_inventory['location']);
    }

    $command = 'UPDATE monster_inventory SET `data`=' . quote_smart($exp . ';' . $now) . $extra . ' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'updating exp/level');

    $last_feed_time = $now;
  }
}

if(!$upgraded)
{
  echo '<b>' . $exp . ' / ' . $exp_needed . ' experience points</b></p><p>';

  if($max_level == $SATED_LEVEL)
    echo 'The ' . $this_inventory['itemname'] . ' has already been sated.  Feeding it again might cause something unexpected to happen...</p><p>';

  if($last_feed_time + $time_between_feeding <= $now || $max_level == $SATED_LEVEL)
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
            echo '<i>(You can feed a ' . $this_inventory['itemname'] . ' a variety of items, however not all are good for it.  Note that the quantity listed is the number available in your house, not the number you will feed.  You will always only give the ' . $this_inventory['itemname'] . ' 1 item to eat.)</i></p>' .
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
      echo 'There are no items here to feed to the ' . $this_inventory['itemname'] . '.';
  }
  else
    echo 'This ' . $this_inventory['itemname'] . ' is sated... for the moment.';
}
?>
