<?php
$require_petload = 'no';
$invisible = 'yes';
$AJAX = true;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/inventory.php';
require_once 'commons/houselib.php';
require_once 'commons/userlib.php';
require_once 'commons/itemstats.php';
require_once 'commons/sellermarketlib.php';

if(!array_key_exists('ids', $_POST))
{
  echo 'message:No items were selected...';
  exit();
}

$itemids = explode(',', $_POST['ids']);

$house = get_house_byuser($user['idnum']);
if($house === false)
{
  echo 'message:Error loading your house.  If this problem persists (especially if there\'s nothing about it in the City Hall), please report it to <a href="admincontact.php">an administrator</a>.' . "\n" .
       'failure:' . implode(',', $itemids);
  exit();
}

$failed_ids = array();
$moved_ids = array();
$soldback = array();

$profit = 0;

$delete_ids = array();
$refuse_ids = array();
$grocery_ids = array();

$NO_BIDS_REMAINING = array();

foreach($itemids as $id)
{
  $item = get_inventory_byid($id, 'idnum,user,location,itemname,health');

  if($item['user'] == $user['user'] && $item['location'] != 'seized' && $item['location'] != 'pet' && $item['location'] != 'storage/outgoing')
  {
    $item_details = get_item_byname($item['itemname']);

    if($item_details === false)
      continue;

    if($item_details['cursed'] == 'yes' || $item_details['nosellback'] == 'yes' || $item_details['quest_item'] == 'yes')
      $failed_ids[] = $id;
    else
    {
      $moved_ids[] = $id;

      $sellback = ceil($item_details['value'] * sellback_rate());

      if($item['health'] < $item_details['durability'] || $NO_BIDS_REMAINING[$item['itemname']])
        $sold_to_sellers_market = false;
      else
      {
        $sold_to_sellers_market = sell_to_sellers_market($item, $sellback);

        if($sold_to_sellers_market === false)
          $NO_BIDS_REMAINING[$item['itemname']] = true;
      }
      
      if($sold_to_sellers_market !== false)
      {
        $profit += $sold_to_sellers_market;
        $sold[$id]++;
        $sold_value[$id] += $sellback;
        
        $delete_ids[] = $id;
      }
      else
      {
        $profit += $sellback;

        $sold[$id]++;
        $sold_value[$id] += $sellback;

        $soldback[$item['itemname']]++;

        if(mt_rand(1, 2) == 1)
          $delete_ids[] = $id;
        else
        {
          if($item_details['is_grocery'] == 'yes')
            $grocery_ids[] = $id;
          else
            $refuse_ids[] = $id;
        }
      }
    }
  }
  else
    $failed_ids[] = $id;
}

if(count($soldback) > 0)
{
  foreach($soldback as $item=>$quantity)
    record_item_disposal($item, 'sold', $quantity);
}

$moved_ids_count = count($moved_ids);
$failed_ids_count = count($failed_ids);

if($moved_ids_count > 0)
{
  if(count($delete_ids) > 0)
  {
    $command = 'DELETE FROM monster_inventory WHERE idnum IN (' . implode(',', $delete_ids) . ') LIMIT ' . count($delete_ids);
    $database->FetchNone($command, 'deleting items');
  }

  if(count($refuse_ids) > 0)
  {
    $command = 'UPDATE monster_inventory SET user=\'ihobbs\',message2=' . quote_smart('Gamesold by ' . $user['display'] . '.') . ',changed=' . $now . ' WHERE idnum IN (' . implode(',', $refuse_ids) . ') LIMIT ' . count($refuse_ids);
    $database->FetchNone($command, 'recovering items for refuse store');
  }

  if(count($grocery_ids) > 0)
  {
    $command = 'UPDATE monster_inventory SET user=\'grocerystore\',message2=' . quote_smart('Gamesold by ' . $user['display'] . '.') . ',changed=' . $now . ' WHERE idnum IN (' . implode(',', $grocery_ids) . ') LIMIT ' . count($grocery_ids);
    $database->FetchNone($command, 'recovering items for refuse store');
  }

  recount_house_bulk($user, $house);
}

if($profit > 0)
{
  give_money($user, $profit, 'Game-sold ' . count($sold) . ' item' . (count($sold) == 1 ? '' : 's'));

  require_once 'commons/questlib.php';

  $sellback = get_quest_value($user['idnum'], 'total sellback');
  $sellback_value = (int)$sellback['value'];

  $sellback_value += $profit;

  if($sellback === false)
    add_quest_value($user['idnum'], 'total sellback', $sellback_value);
  else
    update_quest_value($sellback['idnum'], $sellback_value);

  $badges = get_badges_byuserid($user['idnum']);

  if($badges['gamesell'] == 'no' && $sellback_value >= 1000)
  {
    set_badge($user['idnum'], 'gamesell');
    $body = 'Just wanted to let you know that you\'re doing well.  Selling cheap items back to the game adds up.  You\'ve made 1,000{m} by selling stuff back!<br /><br />' .
            'Keep up the good work!<br /><br />' .
            '{i}(You earned the Bourgeois badge!){/}';
    psymail_user($user['user'], 'lpawlak', 'You game-sold over 1,000 moneys worth of items!', $body);
  }

  if($badges['gamesellmore'] == 'no' && $sellback_value >= 1000000)
  {
    set_badge($user['idnum'], 'gamesellmore');
    $body = 'You\'ve probably bought and sold so much stuff you wouldn\'t believe that you\'ve sold items totalling 1,000,000{m} in value!<br /><br />' .
            'It\'s true!  You\'ve come a long way since you first got here.<br /><br />' .
            'Anyway, see you around.<br /><br />' .
            '{i}(You earned the Profiteer badge!){/}';
    psymail_user($user['user'], 'lpawlak', 'You game-sold over 1,000,000 moneys worth of items!', $body);
  }
}

if($moved_ids_count > 0)
{
  echo 'message:<span class="success">' . $moved_ids_count . ' item' . ($moved_ids_count == 1 ? ' was' : 's were') . ' sold, for ' . $profit . '<span class="money">m</span>.';

  if($failed_ids_count > 0)
    echo '  ' . $failed_ids_count . ' item' . ($failed_ids_count == 1 ? '' : 's') . ' could not be sold.';

  echo '</span>' . "\n" . 
       'domoney:' . ($user['money'] + $profit) . "\n" .
       'success:' . implode(',', $moved_ids);
}

if($failed_ids_count > 0)
{
  if($moved_ids_count == 0)
    echo 'message:<span class="failure">The selected item' . ($failed_ids_count == 1 ? '' : 's') . ' could not be sold.</span>';

  echo "\n" . 'failure:' . implode(',', $failed_ids);
}
?>
