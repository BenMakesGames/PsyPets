<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/hungryvalue.php';

$time_between_feeding = (int)$action_info[2] * 60 * 60;

$wall_options = array(
   1 => 'walls/bar1.png',
   2 => 'walls/bar2.png',
   3 => 'walls/bar3.png',
   4 => 'walls/bar8.png',
   5 => 'walls/bar0.png',
   6 => 'walls/bar9.png',
   7 => 'walls/bar5.png',
   8 => 'walls/bar4.png',
   9 => 'walls/bar7.png',
  10 => 'walls/bar6.png',
);

if($this_inventory['itemname'] == 'Sated Tapestry')
  $max_level = 10;
else //ex: "Hungry Tapestry (level 4)"
  $max_level = substr($this_inventory['itemname'], 23, strlen($this_inventory['itemname']) - 24);

$data = explode(';', $this_inventory['data']);

$exp = (int)$data[0];
$last_feed_time = (int)$data[1];
$exp_needed = pow(2, $max_level);

$upgraded = false;

if($_POST['action'] == 'feed' && $max_level < 10 && $last_feed_time + $time_between_feeding <= $now)
{
  $itemid = (int)$_POST['itemid'];
  
  $item = get_inventory_byid($itemid);
  $details = get_item_byname($item['itemname']);

  if($item['user'] == $user['user'] && $item['location'] == $this_inventory['location']
    && $details['custom'] == 'no' && $details['cursed'] == 'no'
    && substr($details['itemtype'], 0, 5) != 'food/' && substr($details['itemtype'], 0, 6) != 'plant/')
  {
    delete_inventory_byid($itemid);

    $food_value = tapestry_food_value($item['itemname']);

    $extra = '';

    if($food_value > 0)
    {
      echo '<p>The Hungry Tapestry devours the ' . $item['itemname'] . ' in one bite, and grins.</p>';

      $exp += $food_value;
      $new_level = $max_level;
      while($exp >= $exp_needed && $new_level < 10)
      {
        $new_level++;
        $exp -= $exp_needed;
        $exp_needed = pow(2, $new_level);
      }

      if($new_level > $max_level)
      {
        $AGAIN_WITH_SAME = true;

        $upgraded = true;

        if($new_level == 10)
        {
          echo '<p><i>(The Hungry Tapestry has been sated!)</i></p>';

          $extra = ',itemname=' . quote_smart('Sated Tapestry');
        }
        else
        {
          echo '<p><i>(The Hungry Tapestry has increased in level!)</i></p>';

          $extra = ',itemname=' . quote_smart('Hungry Tapestry (level ' . $new_level . ')');
        }
      }
    }
    else
    {
      echo '<p>The Hungry Tapestry devours the ' . $item['itemname'] . ' in one bite, coughs a little, and spits up some Rubble.  It apparently didn\'t like the meal you gave it.</p>';
      add_inventory($user['user'], '', 'Rubble', 'Coughed up by a Hungry Tapestry', $this_inventory['location']);
    }

    $command = 'UPDATE monster_inventory SET `data`=' . quote_smart($exp . ';' . $now) . $extra . ' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'updating exp/level');

    $last_feed_time = $now;
  }
}

if($max_level == 10 && $_GET['action'] == 'farewell')
{
  delete_inventory_byid($this_inventory['idnum']);
  add_inventory($user['user'], '', 'Potion Ticket', 'Given to ' . $user['display'] . ' by a Sated Tapestry', $this_inventory['location']);
  $upgraded = true;
  
  echo '
    <h5>The Sated Tapestry Leaves to Start a Life of its Own</h5>
    <p>"Thanks again for everything, ' . $user['display'] . '!  Maybe we\'ll meet again someday!"</p>
    <p><i>(You received a Potion Ticket!  Trade it with Thaddeus, the Alchemist, for one of his rare potions.)</i></p>
  ';
}

if(!$upgraded)
{
  if($max_level < 10)
  {
    echo '<h5>' . $exp . ' / ' . $exp_needed . ' Experience Points</h5>';

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
              if($max_level > 0)
                echo '<ul><li><a href="#useitem">Hang Tapestry</a></li></ul>';

              echo '<p><i>(A Hungry Tapestry can eat a variety of items, however not all are good for them.  Note that the quantity listed is the number available in your house, not the number you will feed.  You will always only give the Hungry Tapestry 1 item to eat.)</i></p>' .
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
        echo '<p>There are no items here to feed to the Hungry Tapestry.</p>';
    }
    else
      echo '<p>This Hungry Tapestry is sated... for the moment.</p>';
  }
  else // $max_level >= 10 (doesn't get above 10, though)
  {
    echo '
      <h5>The Sated Tapestry Speaks!</h5>
      <p>"Thanks for taking care of me all this time!  I feel like I should be getting a move on - start a life of my own - but before that, I\'d like to give you something as way of thanks: a Potion Ticket.  I insist!"</p>
      <ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=farewell">Accept the gift and say farewell</a></li></ul>
    ';
  }

  if($max_level > 0)
  {
    $select = (int)$_GET['option'];

    echo '<h5 id="useitem">Hang Tapestry</h5>';

    if($_GET['step'] == 2 && $select <= $max_level && array_key_exists($select, $wall_options))
    {
      $command = 'UPDATE monster_users SET profile_wall=\'' . $wall_options[$_GET['option']] . '\',profile_wall_repeat=\'horizontal\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'applying neat-o bar');

      echo '<p><i>(Your profile background has been changed!)</i></p>';
    }
    else
    {
      echo '<p>What style will you hang in your profile? (Click on the graphic you want to use; this will replace your current profile background)</p>';

      foreach($wall_options as $id=>$graphic)
      {
        if($id > $max_level)
          break;

        echo '<p><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&step=2&option=' . $id . '"><img src="gfx/' . $graphic . '" border="0" /></a></p>';
      }
    }
  }
}
?>
