<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/doevent.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/itemlib.php';
require_once 'commons/parklib.php';
require_once 'commons/economylib.php';
require_once 'commons/houselib.php';

if($user['show_park'] != 'yes')
{
  header('Location: /n404/');
  exit();
}

if($user['license'] != 'yes')
{
  header('Location: /park.php');
  exit();
}

$command = 'SELECT idnum FROM monster_events WHERE host=' . quote_smart($user['user']) . ' AND finished=\'no\' LIMIT 3';
$existing_events = $database->FetchMultiple($command, 'fetching existing park event');

if(count($existing_events) >= 3)
{
  header('Location: /park.php?msg=162');
  exit();
}

$my_house = get_house_byuser($user['idnum']);

$my_addons = take_apart(',', $my_house['addons']);

$can_host_swimming_events = in_array('Indoor Swimming Pool', $my_addons);

if($user['event_type'] == 'swim' && !$can_host_swimming_events)
  $_POST['submit'] = 'Cancel';

$event_cost = 5; //value_with_inflation(10);

$errored = false;

if($user['event_step'] < 1)
{
  header('Location: /hostevent1.php');
  exit();
}
else if($_POST['submit'] == 'Finish')
{
  $tropies = 0;

  $event_prizes = '';
  $prizes = array();

  if($user['event_size'] * $event_cost > $user['money'])
  {
    $prize_error .= "It seems as though you can no longer afford to rent the park for an event of this size (the fee would be " . ($user["event_size"] * $event_cost) . '<span class="money">m</span>).  You may visit the bank to withdraw money.  Your progress here will be saved automatically.<br />';
    $errored = true;
  }

  if($user['event_type'] == 'hunt')
  {
    $prize_count = 0;
    foreach($_POST as $key=>$value)
    {
      if(is_numeric($key))
      {
        if($value == 'on' || $value == 'yes')
        {
          $prize_count++;
          $this_item = get_inventory_byid((int)$key);
          $item_details = get_item_byname($this_item['itemname']);

          if($item_details['noexchange'] == 'yes' || $item_details['cursed'] == 'yes')
          {
            $prize_error = 'Can not host an event with ' . $item_details['itemname'] . ' for a prize.';
            $errored = true;
            break;
          }
          else if($this_item['user'] != $user['user'])
          {
            $prize_error = 'The ' . $this_item['itemname'] . ' selected does not belong to you.  The most likely explanation is that you had the item up for sale in the Flea Market, and it was purchased while you were selecting prizes.';
            $errored = true;
            break;
          }

          if(substr($item_details['itemtype'], 0, 12) == 'craft/trophy')
            $trophies++;
          
          $prizes[] = $key;

          if(strlen($event_prizes) > 0)
            $event_prizes .= ',';
          $event_prizes .= $this_item['idnum'];

          $prize_descript .= $this_item['itemname'] . '<br />';
        }
        else
        {
          $prize_error = 'Problem with form.  Ask an administrator about this.';
          $errored = true;
          break;
        }
      }
    }

    if($prize_count == 0)
    {
      $prize_error = 'Scavenger hunts must provide <em>at least</em> one prize.';
      $errored = true;
    }
    else if($prize_count > $user['event_size'])
    {
      $prize_error = 'You may not give out more prizes than there are participants.';
      $errored = true;
    }

  }
  else if($user['event_type'] == 'ctf' || $user['event_type'] == 'picturades' || $user['event_type'] == 'tow')
  {
    foreach($_POST as $key=>$value)
    {
      if($key !== 'submit')
      {
        if($value == 1 || $value == 2)
        {
          $prizes[$value] = str_replace('_', ' ', $key);
        }
        else if($value != '')
        {
          $prize_error = 'Enter a 1 for the winning-team prize, 2 for the losing-team.';
          $errored = true;
          break;
        }
      }
    }

    if($errored == false)
    {
      $prize_ids = array();
      $prize_count = array();
 
      asort($prizes);
 
      for($i = 1; $i < count($prizes) + 1; $i++)
      {
        if(strlen($prizes[$i]) == 0)
        {
          $prize_error = 'You may not give the losing team a prize unless the winning team also gets a prize.';
          $errored = true;
          break;
        }
 
        $items = $database->FetchMultiple('
          SELECT a.*,b.itemtype
          FROM
            monster_inventory AS a
            LEFT JOIN monster_items AS b
              ON a.itemname=b.itemname
          WHERE
            a.itemname=' . quote_smart($prizes[$i]) . ' AND
            a.user=' . quote_smart($user['user']) . ' AND
            a.location=\'storage\' AND
            b.noexchange=\'no\' AND
            b.cursed=\'no\'
          LIMIT ' . ((int)$user['event_size'] / 2)
				);
 
        foreach($items as $my_item)
        {
          $prize_count[$my_item['itemname']]++;
          $prize_ids[] = $my_item['idnum'];
          $prize_descript .= $my_item['itemname'] . '<br />';

          if(substr($my_item['itemtype'], 0, 12) == 'craft/trophy')
            $trophies++;
        }
 
        if($prize_count[$prizes[$i]] < $user['event_size'] / 2)
        {
          $prize_error = 'You do not have enough ' . $prizes[$i] . ' items.';
          $errored = true;
          break;
        }
 
        if(strlen($event_prizes) > 0)
          $event_prizes .= ",";
        $event_prizes .= $prizes[$i];
      }
      
      $prizes = $prize_ids;
      
      $event_prizes = "";
 
      foreach($prizes as $prize)
      {
        if(strlen($event_prizes) > 0)
          $event_prizes .= ",";
        $event_prizes .= $prize;
      }
    }
  }
  else if($user['event_type'] == 'race' || $user['event_type'] == 'archery'
    || $user['event_type'] == 'jump' || $user['event_type'] == 'strategy'
    || $user['event_type'] == 'brawl' || $user['event_type'] == 'roborena'
    || $user['event_type'] == 'ddr'
    || $user['event_type'] == 'crafts' || $user['event_type'] == 'cookoff'
    || $user['event_type'] == 'swim'
    || $user['event_type'] == 'fashion'
    || $user['event_type'] == 'fishing'
    || $user['event_type'] == 'mining')
  {
    foreach($_POST as $key=>$value)
    {
      if($key > 0)
      {
        if($value > 0)
        {
          if($prizes[$value] > 0)
          {
            $prize_error = 'You can\'t have two prizes for one placement.  (Don\'t use the same number twice.)';
            $errored = true;
          }
          else
          {
            $prize_count++;
            $prizes[$value] = $key;
          }
        }
        else if($value != "")
        {
          $prize_error = "You must give positive numbers for placements.";
          $errored = true;
          break;
        }
      }
    }

    if($prize_count > $user["event_size"])
    {
      $prize_error = "You may not give out more prizes than there are participants.";
      $errored = true;
    }

    if($errored == false)
    {
      asort($prizes);

      for($i = 1; $i < count($prizes) + 1; $i++)
      {

        if($prizes[$i] <= 0)
        {
          $prize_error = 'You may not skip places.  Ex: you cannot hand out a prize to 1st, 2nd and 4th place unless you will also give a prize to 3rd place.';
          $errored = true;
          break;
        }

        $my_item = get_inventory_byid($prizes[$i]);

        if($my_item['user'] != $user['user'])
        {
          $prize_error = 'User mismatch (you don\'t seem to own one of the items you selected).  Really weird, unless you\'re trying to break things.  Ask ' . $SETTINGS['author_resident_name'] . ' about this.';
          $errored = true;
          break;
        }
        
        $item_details = get_item_byname($my_item['itemname']);
        if($item_details['noexchange'] == 'yes' || $item_details['cursed'] == 'yes')
        {
          $prize_error = 'You may not host a park event with ' . $my_item['itemname'] . ' as a prize.';
          $errored = true;
          break;
        }

        if(substr($item_details['itemtype'], 0, 12) == 'craft/trophy')
          $trophies++;

        if(strlen($event_prizes) > 0)
          $event_prizes .= ',';
        $event_prizes .= $my_item['idnum'];

        $prize_descript .= $my_item['itemname'] . '<br />';
      }
    }
  }

  if($errored == false)
  {
    $event_name       = $user['event_name'];
    $event_descript   = $user['event_descript'];
    $event_prereport  = $user['event_prereport'];
    $event_postreport = $user['event_postreport'];

    $event_descript   = break_long_lines($event_descript);
    $event_prereport  = break_long_lines($event_prereport);
    $event_postreport = break_long_lines($event_postreport);

    $event_name       = nl2br($event_name);
    $event_descript   = nl2br($event_descript);
    $event_prereport  = nl2br($event_prereport);
    $event_postreport = nl2br($event_postreport);

    $database->FetchNone('
      INSERT INTO `monster_events`
      (
        `name`, `descript`,
        `fee`,
        `prizes`, `prizedescript`,
        `trophies`,
        `minlevel`, `maxlevel`,
        `minparticipant`,
        `event`,
        `prereport`, `postreport`,
        `timestamp`,
        `host`,
        `graphic`
      )
      VALUES
      (
        ' . quote_smart($event_name) . ', ' . quote_smart($event_descript) . ',
        ' . (int)$user['event_fee'] . ',
        ' . quote_smart($event_prizes) . ', ' . quote_smart($prize_descript) . ',
        ' . (int)$trophies . ',
        ' . (int)$user['event_minlevel'] . ', ' . (int)$user['event_maxlevel'] . ',
        ' . (int)$user['event_size'] . ',
        ' . quote_smart($user['event_type']) . ',
        ' . quote_smart($event_prereport) . ', ' . quote_smart($event_postreport) . ',
        ' . $now . ',
        ' . quote_smart($user['user']) . ',
        ' . quote_smart($user['event_graphic']) . '
      )
    ');

    $event_id = $database->InsertID();
    
    foreach($prizes as $prize)
    {
      $database->FetchNone('
        UPDATE monster_inventory
        SET
          location=\'storage/outgoing\',
          forsale=0
        WHERE idnum=' . (int)$prize . '
        LIMIT 1
      ');
    }

    $database->FetchNone('
      UPDATE monster_users
      SET
        event_step=3,
        money=money-' . ((int)$user['event_size'] * $event_cost) . '
      WHERE `user`=' . quote_smart($user['user']) . '
      LIMIT 1
    ');

    $user['money'] -= $user['event_size'] * $event_cost;

    add_transaction($user['user'], $now, 'Host Event Fee', -($user['event_size'] * $event_cost));

    $user['event_step'] = 0;
    $database->FetchNone('
      UPDATE monster_users
      SET event_step=0
      WHERE user=' . quote_smart($user['user']) . '
      LIMIT 1
    ');

    header('Location: /eventdetails.php?idnum=' . $event_id);
    exit();
  }
}
else if($_POST['submit'] == '< Back')
{
  header('Location: ./hostevent1.php');
  exit();
}
else if($_POST['submit'] == 'Cancel')
{
  $command = 'UPDATE monster_users ' .
             'SET event_step=0 ' .
             'WHERE `user`=' . quote_smart($user['user']) . ' LIMIT 1';
  $database->FetchNone($command, 'canceling park event');

  header('Location: /park.php');
  exit();
}

$type_name = $EVENT_TYPES;

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Park &gt; Host Event (step 2 of 2)</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="park.php">The Park</a> &gt; Host Event (step 2 of 2)</h4>
<?php
echo '
  <ul class="tabbed">
   <li><a href="/park.php">Browse Events</a></li>
   <li class="activetab"><a href="/hostevent1.php">Host a new event</a></li>
   <li><a href="/park_exchange.php">Exchanges</a></li>
  </ul>
';
?>
     <table>
      <tr>
       <td valign="top"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/events/<?= $user['event_graphic'] ?>"></td>
       <td>
        <?= $user['event_name'] ?><br />
        <?= $user['event_size'] ?> pet <?= $type_name[$user['event_type']] ?> for levels <?= $user['event_minlevel'] ?> to <?= $user['event_maxlevel'] ?><br />
        <?= $user['event_fee'] ?><span class="money">m</span> entry fee
       </td>
      </tr>
     </table>
     <h4>Select Prizes</h4>
     <form method="post" name="hostevent">
<?php
$items = $database->FetchMultiple(
	'SELECT * ' .
  'FROM monster_inventory ' .
  'WHERE `user`=' . quote_smart($user['user']) . ' AND `location`=\'storage\' ' .
  'ORDER BY itemname ASC'
);
  
echo '<p>You can only select prizes from storage.  If you need to move items in to storage, you can leave this page to do so - your progress here has been saved.  You can resume hosting the event from the main Park page.</p>';

// hunt-type events
if($user['event_type'] == 'hunt')
{
?>
        <p>Check off the prizes you wish to be available.  You must select at least one.</p>
        <p>Click "Finish" only when you are sure that all the information is correct.</p>
        <p><input type="submit" name="submit" value="&lt; Back" /> <input type="submit" name="submit" value="Finish" /> <input type="submit" name="submit" value="Cancel" /></p>
        <table>
         <tr class="titlerow">
          <th></th>
          <th></th>
          <th>Item</th>
          <th>Maker</th>
          <th>Comment</th>
         </tr>
<?php
  $rowclass = begin_row_class();

  foreach($items as $my_item)
  {
    $item_detail = get_item_byname($my_item['itemname']);

    if($item_detail['cursed'] == 'yes' || $item_detail['noexchange'] == 'yes')
      continue;

    $maker = item_maker_display($my_item['creator']);
?>
         <tr class="<?= $rowclass ?>">
          <td><input name="<?= $my_item['idnum'] ?>" type="checkbox" /></td>
          <td class="centered"><?= item_display_extra($item_detail) ?></td>
          <td><?= $my_item['itemname'] ?></td>
          <td><?= $maker ?></td>
          <td><?= format_text($my_item['message']) . '<br />' . format_text($my_item['message2']) ?></td>
         </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
  
  echo '</table>';
}
// team-type events
else if($user['event_type'] == 'ctf' || $user['event_type'] == 'picturades' || $user['event_type'] == 'tow')
{
  $item_count = array();
  $prizes_ok = false;

  foreach($items as $my_item)
  {
    $item_count[$my_item['itemname']]++;

    if($item_count[$my_item['itemname']] >= $user['event_size'] / 2)
      $prizes_ok = true;
  }

  echo "        <p>Only items you have enough of for an entire team will be listed below.</p>\n";

  if($prizes_ok == true)
  {
?>
        <p>If there will be any prizes, enter "1" near the items that will be awarded to the winning team, and "2" for the items awarded to the losing team.</p>
        <p>Click "Finish" only when you are sure that all the information is correct.</p>
        <p><input type="submit" name="submit" value="&lt; Back" /> <input type="submit" name="submit" value="Finish" /> <input type="submit" name="submit" value="Cancel" /></p>
        <table>
         <tr class="titlerow">
          <th></th>
          <th></th>
          <th>Item</th>
         </tr>
<?php
    $rowclass = begin_row_class();

    foreach($item_count as $itemname=>$amount)
    {
      if($amount >= $user['event_size'] / 2)
      {
        $item_detail = get_item_byname($itemname);

        if($item_detail['noexchange'] == 'yes' || $item_detail['cursed'] == "yes")
          continue;
?>
         <tr class="<?= $rowclass ?>">
          <td><input name="<?= str_replace(' ', '_', $itemname) ?>" maxlength="1" size="1" style="width:2em;" /></td>
          <td class="centered"><?= item_display_extra($item_detail) ?></td>
          <td><?= $item_detail['itemname'] ?></td>
         </tr>
<?php
        $rowclass = alt_row_class($rowclass);
      }
    } // for each item you have enough of
    
    echo '</table>';
  } // if there were any items you have enough of
}
// placement-type events
else if($user['event_type'] == 'race' || $user['event_type'] == 'archery'
  || $user['event_type'] == 'jump' || $user['event_type'] == 'strategy'
  || $user['event_type'] == 'brawl' || $user['event_type'] == 'roborena'
  || $user['event_type'] == 'ddr'
  || $user['event_type'] == 'crafts' || $user['event_type'] == 'cookoff'
  || $user['event_type'] == 'swim'
  || $user['event_type'] == 'fashion'
  || $user['event_type'] == 'fishing'
  || $user['event_type'] == 'mining')
{
?>
        <p>If there will be any prizes, enter "1" near the item that will be awarded to 1st place, "2" for the 2nd place prize, etc.</p>
        <p>Click "Finish" only when you are sure that all the information is correct.</p>
        <p><input type="submit" name="submit" value="&lt; Back" /> <input type="submit" name="submit" value="Finish" /> <input type="submit" name="submit" value="Cancel" /></p>
        <table>
         <tr class="titlerow">
          <th></th>
          <th></th>
          <th>Item</th>
          <th>Maker</th>
          <th>Comment</th>
         </tr>
<?php
  $rowclass = begin_row_class();
  foreach($items as $my_item)
  {
    $item_detail = get_item_byname($my_item['itemname']);

    if($item_detail['noexchange'] == 'yes' || $item_detail['cursed'] == 'yes')
      continue;

    $maker = item_maker_display($my_item['creator']);
?>
         <tr class="<?= $rowclass ?>">
          <td><input name="<?= $my_item['idnum'] ?>" maxlength="2" size="2" style="width:2em;" /></td>
          <td class="centered"><?= item_display_extra($item_detail) ?></td>
          <td><?= $item_detail['itemname'] ?></td>
          <td><?= $maker ?></td>
          <td><?= format_text($my_item['message']) . '<br />' . format_text($my_item['message2']) ?></td>
         </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }

  echo '</table>';
}
?>
        <p>Click "Finish" only when you are sure that all the information is correct.</p>
        <p><input type="submit" name="submit" value="&lt; Back" /> <input type="submit" name="submit" value="Finish" /> <input type="submit" name="submit" value="Cancel" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
