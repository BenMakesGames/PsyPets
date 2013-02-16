<?php
$require_petload = 'no';
$invisible = 'yes';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/houselib.php';

require_once 'commons/flavorlib.php';

$locid = $user['locid'];
$house = get_house_byuser($user['idnum'], $locid);
if($house === false)
{
  echo 'Error loading your house.  If this problem persists (especially if there\'s nothing about it in the City Hall), please report it to <a href="admincontact.php">an administrator</a>.';
  exit();
}

$houseloc = 'home' . (strlen($house['curroom']) > 0 ? '/' . $house['curroom'] : '');

$here = $_POST['from'];

$error_message = array();

if($here == 'groupbox')
{
  require_once 'commons/grouplib.php';

  $groupid = (int)$_POST['id'];
  $group = get_group_byid($groupid);

  if($group === false)
  {
    header('Location: /groupindex.php?msg=92');
    exit();
  }

  $ranks = get_group_ranks($groupid);
  $members = explode(',', $group['members']);

  $a_member = is_a_member($group, $user['idnum']);

  if($a_member)
  {
    $rankid = get_member_rank($group, $user['idnum']);
    $can_take_stuff = (rank_has_right($ranks, $rankid, 'boxtake') || $group['leaderid'] == $user['idnum']);
  }
  else
    $can_take_stuff = false;

  if(!$can_take_stuff)
  {
    header('Location: ./groupbox.php?id=' . $groupid);
    exit();
  }

  $group_user = 'group:' . $groupid;
}

$max_pets = max_active_pets($user, $house);

if(strlen($_POST['submit']) > 0)
{
  $itemids = array();

  foreach($_POST as $key=>$value)
  {
    if(is_numeric($key))
    {
      if($value == 'yes' || $value == 'on')
        $itemids[] = (int)$key;
    }
  }

  $no_basement = array();
  $no_basement2 = array();
  $item_names = array();

  foreach($itemids as $id)
  {
    $item = get_inventory_byid($id);
    if(($item['user'] == $user['user'] || $item['user'] == $group_user) && $item['location'] != 'seized' && $item['location'] != 'pet' && $item['location'] != 'storage/outgoing')
    {
      $item_details = get_item_byname($item['itemname']);

      if($item_details === false)
        continue;

      if($item_details['cursed'] == 'yes')
      {
        if($_POST['submit'] == 'Prepare' || $_POST['submit'] == 'Feed to' ||
          $_POST['submit'] == 'Throw Out' || $_POST['submit'] == 'Gamesell' ||
          $_POST['submit'] == 'Move to')
        {
          $error_message[] = '31:' . $item_details['itemname'];
        }
      }
      else if($item_details['questitem'] == 'yes')
      {
        if($_POST['submit'] == 'Throw Out' || $_POST['submit'] == 'Gamesell')
        {
          $error_message[] = '169:' . $item_details['itemname'];
        }
      }

      $items[] = $id;
      $item_names[$item_details['itemname']]++;
      if($item_details['nomarket'] == 'yes')
        $no_basement[] = $item_details['itemname'];
      else if($item['health'] < $item_details['durability'])
        $no_basement2[] = $item_details['itemname'];
    }
    else
    {
      $error_message[] = 28;
      break;
    }
  }

  if(count($error_message) > 0)
    ;
  else if($_POST['submit'] == 'Prepare')
  {
    if($_POST['recipe1'] == 'none')
    {
      if(count($items) == 0)
        $error_message[] = 1;
      else
      {
        // make sure we can make something out of these items to begin with...
        foreach($items as $id)
        {
          $item = get_inventory_byid($id);
          $consider_item = get_item_byname($item['itemname']);
        }
      }

      if(count($error_message) == 0)
      {
        // find all recipes that match the selected ingredients

        $command = 'SELECT * FROM monster_recipes WHERE machine_only=\'no\'';

        $first = true;
        foreach($items as $id)
        {
          $item = get_inventory_byid($id);

          $command .= ' AND ingredients LIKE ' . quote_smart('%' . $item['itemname'] . '%');
        }

        $recipes = $database->FetchMultiple($command, 'fetching recipes');

        foreach($recipes as $recipe)
        {
          $ingredients = explode(',', $recipe['ingredients']);
          $good_recipe = true;

          foreach($items as $id)
          {
            $item = get_inventory_byid($id);
            $key = array_search($item['itemname'], $ingredients);

            if(is_numeric($key))
              unset($ingredients[$key]);
            else
            {
              $good_recipe = false;
              break;
            }
          }

          if(count($ingredients) == 0 && $good_recipe == true)
          {
            $recipe_id = $recipe['idnum'];

            $makes = explode(',', $recipe['makes']);
            $made_list = array();

            // add the new things you made
            foreach($makes as $made_this)
            {
              if($make_this == 'Mushroom' && mt_rand(1, 4) == 10)
                $made_this = 'Poisonous Mushroom';
              else if($made_this == 'White Bread' && mt_rand(1, 10) == 10)
                $make_this = 'Wheat Bread';
              else if($made_this == 'Blueberry Wine' && mt_rand(1, 100) == 100)
                $make_this = 'Goodberry Wine';
              else if($made_this == 'Redsberry Wine' && mt_rand(1, 100) == 100)
                $make_this = 'Goodberry Wine';
              else
                $make_this = $made_this;

              add_inventory($user['user'], 'u:' . $user['idnum'], $make_this, $user['display'] . ' prepared this.', $houseloc);
              $made_list[$make_this]++;
            }

            require_once 'commons/kitchenlib.php';
            record_known_recipe($user['idnum'], $recipe_id);
            
            $makes_msgs = array();
            foreach($made_list as $itemname=>$quantity)
              $makes_msgs[] = $quantity . '&times; ' . $itemname;

            $error_message[] = '4:' . urlencode(implode(', ', $makes_msgs));
            break;
          }
          else
            $good_recipe = false;
        }

        if($good_recipe == false)
          $error_message[] = 52;
        else // a good recipe
        {
          foreach($items as $id)
            delete_inventory_byid($id);
        }
      } // cooking
    } // remembered recipe
    else
    {
      $recipenum = (int)$_POST['recipe1'];
      
      $recipe_details = $database->FetchSingle('SELECT * FROM psypets_known_recipes WHERE userid=' . $user['idnum'] . ' AND recipeid=' . $recipenum . ' LIMIT 1');
      
      if($recipe_details === false)
        $error_message[] = 1;
      else
      {
        $ingredients = $database->FetchSingle('SELECT * FROM monster_recipes WHERE idnum=' . $recipe_details['recipeid'] . ' LIMIT 1');

        $requires = array();
        $ingreident_list = explode(',', $ingredients['ingredients']);
        $okay = true;

        $max_quantity = (int)$_POST['quantity'];
        if($max_quantity < 1)
          $max_quantity = 1;

        foreach($ingreident_list as $item)
          $requires[$item]++;

        foreach($requires as $itemname=>$quantity)
        {
          $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=' . quote_smart($houseloc) . ' AND itemname=' . quote_smart($itemname) . ' LIMIT ' . $quantity;
          $data = $database->FetchSingle($command, 'checking house for ingredients');
          if($data['c'] < $quantity)
          {
            $okay = false;
            break;
          }
          else if((int)($data['c'] / $quantity) < $max_quantity)
            $max_quantity = (int)($data['c'] / $quantity);
        }

        if(!$okay)
          $error_message[] = 101;
        else
        {
          foreach($requires as $itemname=>$quantity)
            delete_inventory_byname($user['user'], $itemname, $quantity * $max_quantity, $houseloc);

          $makes = explode(',', $ingredients['makes']);
          $total_makes = array();
          
          for($x = 0; $x < $max_quantity; ++$x)
          {
            foreach($makes as $itemname)
            {
              if($itemname == 'Mushroom' && mt_rand(1, 4) == 10)
                $itemname = 'Poisonous Mushroom';
              else if($itemname == 'White Bread' && mt_rand(1, 10) == 10)
                $itemname = 'Wheat Bread';
              else if($itemname == 'Blueberry Wine' && mt_rand(1, 100) == 100)
                $itemname = 'Goodberry Wine';
              else if($itemname == 'Redsberry Wine' && mt_rand(1, 100) == 100)
                $itemname = 'Goodberry Wine';

              add_inventory($user['user'], 'u:' . $user['idnum'], $itemname, $user['display'] . ' prepared this.', $houseloc);
              $total_makes[$itemname]++;
            }
          }

          $makes_msgs = array();
          foreach($total_makes as $itemname=>$quantity)
            $makes_msgs[] = $quantity . '&times; ' . $itemname;

          $error_message[] = '4:' . urlencode(implode(', ', $makes_msgs));

          require_once 'commons/kitchenlib.php';
          record_known_recipe($user['idnum'], $recipenum, $max_quantity);

          if($max_quantity >= 50)
          {
            $command = 'UPDATE monster_users SET toasty=\'yes\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
            $database->FetchNone($command, 'toasty!');
          }
        } // ingredients are available
      } // recipe is indeed known
    } // preparing from a known recipe
  }
  else if($_POST['submit'] == 'Feed to')
  {
    require_once 'commons/petlib.php';

    if($_POST['pet1'] == 'multiple')
    {
      include 'commons/feedtomultiple.php';
      exit();
    }

    $pet = get_pet_byid((int)$_POST['pet1']);

    if($pet === false)
      $error_message[] = 109;
    else if($pet['user'] == $user['user'])
    {
      if(count($items) >= 1)
      {
        $error = false;
        
        if($pet['dead'] != 'no')
        {
          $error_message[] = 87;
          $error = true;
        }
        else if($pet['sleeping'] != 'no')
        {
          $error_message[] = '88:' . urlencode($pet['petname']);
          $error = true;
        }
        
        if($error == false)
        {
          foreach($items as $id)
          {
            $item = get_inventory_byid($id);
            $this_item = get_item_byname($item['itemname']);

            if($this_item['is_edible'] == 'no')
            {
              $error_message[] = '6:' . urlencode($item['itemname']);
              $error = true;
              break;
            }
          }
        } // no errors so far

        if($error == false)
        {
          $old_pet = $pet;

          $fed_ids = array();
          $remaining_count = count($items);

          foreach($items as $id)
          {
            $item = get_inventory_byid($id);
            $this_item = get_item_byname($item['itemname']);
            
            if($pet['food'] >= max_food($pet) && $this_item['ediblefood'] >= 0)
              break;
              
            $remaining_count--;

            $fed_ids[] = $id;
            $fed_foods[] = $item['itemname'];

            // apply food bonuses in reverse order.  ex: we don't want to let the pet gain love from eating if
            // the pet's food is <= 0.

            $made_of = array_merge(
              take_apart(',', $this_item['recycle_for']),
              take_apart(',', $this_item['additional_flavors'])
            );

            $dislikes = ($FLAVORS[$pet['dislikes_flavor']] == $item['itemname'] || in_array($FLAVORS[$pet['dislikes_flavor']], $made_of));
            $likes = ($FLAVORS[$pet['likes_flavor']] == $item['itemname'] || in_array($FLAVORS[$pet['likes_flavor']], $made_of));

            if($likes && $dislikes)
            {
              $error_message[] = '149:' . $pet['petname'] . ' has mixed feelings about ' . $item['itemname'] . '.';

              $likes = false;
              $dislikes = false;
            }

            if($dislikes)
              $error_message[] = '149:' . $pet['petname'] . ' doesn\'t care for ' . $item['itemname'] . '.';

            if($likes)
              $error_message[] = '150:' . $pet['petname'] . ' really likes ' . $item['itemname'] . '!';

            if($this_item['edibleesteem'] >= 0 && !$dislikes)
            {
              // items given to you by someone else are worth their 50% their love value, in addition to their esteem value
              if($item['creator']{0} == 'u' && $item['creator'] != ('u:' . $user['idnum']) && $item["esteembonus"] == 'yes')
                $esteem_value = $this_item['ediblelove'] / 2 + $this_item['edibleesteem'];
              else
                $esteem_value = $this_item['edibleesteem'];

              gain_esteem($pet, $esteem_value);
            }
            else if($this_item['edibleesteem'] < 0)
              lose_stat($pet, 'esteem', -$this_item['edibleesteem']);

            if($this_item['ediblelove'] > 0 && !$dislikes)
            {
              // if you give the pet something you made, the item grants 50% more love :)
              if($item['creator'] == 'u:' . $user['idnum'])
                $love_value = $this_item['ediblelove'] * 1.5;
              else
                $love_value = $this_item['ediblelove'];

              if($likes)
                gain_safety($pet, ceil($love_value / 3));

              gain_love($pet, $love_value);

              if($likes)
                gain_esteem($pet, ceil($love_value / 3));
            }
            else if($this_item['ediblelove'] < 0)
              lose_stat($pet, 'love', -$this_item['ediblelove']);

            if($this_item['ediblesafety'] > 0 && !$dislikes)
              gain_safety($pet, $this_item['ediblesafety'], true);
            // un-safe food? (hallucinogenic)
            else if($this_item['ediblesafety'] < 0)
              lose_stat($pet, 'safety', -$this_item['ediblesafety']);

            if($likes)
            {
              if($this_item['ediblefood'] >= 4)
                $this_item['ediblefood'] += 2;
              else if($this_item['ediblefood'] > 0)
                $this_item['ediblefood']++;
            }


            if($this_item['ediblehealing'] > 0)
            {
              if($pet['healing'] >= $this_item['ediblehealing'])
                $error_message[] = '164:' . $pet['petname'];
              else
              {
                $healing_amount = gain_healing($pet, $this_item['ediblehealing']);

                gain_love_exp($pet, ceil(sqrt($healing_amount)));

                if($healing_amount > 8)
                  $error_message[] = '165:' . $pet['petname'];
                else
                  $error_message[] = '166:' . $pet['petname'];
              }
            }

            if($this_item['ediblefood'] > 0)
              gain_food($pet, $this_item['ediblefood']);
            // if the food is bad...
            else if($this_item['ediblefood'] < 0)
              $pet['food'] += $this_item['ediblefood'];

            if($this_item['edibleenergy'] > 0)
              gain_energy($pet, $this_item['edibleenergy']);
            else if($this_item['edibleenergy'] < 0)
              $pet['energy'] += $this_item['edibleenergy'];

            if($this_item['ediblecaffeine'] > 0)
              gain_caffeine($pet, $this_item['ediblecaffeine']);

            if(!$dislikes)
            {
              $love_exp = 0;
              if(mt_rand(1, 10) < $this_item['ediblefood'] + $this_item['ediblelove'])
                $love_exp++;

              if(mt_rand(1, 10) < $this_item['ediblesafety'] + $this_item['edibleesteem'])
                $love_exp++;
              
              if($item['creator'] == 'u:' . $user['idnum'])
                $love_exp++;

              if($likes)
                $love_exp += mt_rand(2, 3);
              
              if($love_exp > 0)
                gain_love_exp($pet, $love_exp, 0, true);
            }

            if($item['itemname'] == 'Eggplant' && mt_rand(1, 1000) == 1 && $pet['eggplant'] == 'no')
            {
              $pet['eggplant'] = 'yes';
              $eggplant_message = true;
            }
          } // for each item

          if(count($fed_ids) > 0)
          {
            // delete the items we fed the pet
            foreach($fed_ids as $id)
              delete_inventory_byid($id);

            // save all the changes we made to the pet
            save_pet($pet, array('esteem', 'love', 'safety', 'food', 'energy', 'caffeinated', 'eggplant', 'healing', 'nasty_wound'));

            $extras['energy'] = $pet['energy'] - $old_pet['energy'];
            $extras['food'] = $pet['food'] - $old_pet['food'];
            $extras['safety'] = $pet['safety'] - $old_pet['safety'];
            $extras['love'] = $pet['love'] - $old_pet['love'];
            $extras['esteem'] = $pet['esteem'] - $old_pet['esteem'];

            add_logged_event($user['idnum'], $pet['idnum'], 0, 'realtime', false, 'Was fed: ' . implode(', ', $fed_foods), $extras);

            recount_house_bulk($user, $house);
          }

          if(count($fed_ids) > 0)
          {
            $error_message[] = '7:' . urlencode($pet['petname']);

            if($remaining_count > 0)
              $error_message[] = '153:' . urlencode($pet['petname']);
          
            if($eggplant_message === true)
              $error_message[] = '151:' . urlencode($pet['petname']);
          }
          else
            $error_message[] = '154:' . urlencode($pet['petname']);

        } // if we selected only food items
      }
      else
        $error_message[] = 8;
    }
    else
      $error_message[] = 9;
  }

  else if($_POST['submit'] == 'Move to')
  {
    $do_basement_move = false;
    $target = trim($_POST['move1']);

    if(count($items) > 0)
    {
      if($target == 'Storage')
      {
        $newloc = 'storage';
      }
      else if($target == 'Locked Storage')
      {
        $newloc = 'storage/locked';
      }
      else if($target == 'Home' || $target == 'Common')
      {
        $newloc = 'home';
        $newroom = '';
      }
      else if($target == 'My Store')
      {
        $nowloc = 'storage/mystore';
      }
      else if($target == 'Basement')
      {
        $addons = take_apart(',', $house['addons']);
        if(array_search('Basement', $addons) === false)
          $error_message[] = 81;
        else
        {
          $do_basement_move = true;
          if(count($no_basement) > 0)
            $error_message[] = '80:' . link_safe(implode(', ', array_unique($no_basement)));
          if(count($no_basement2) > 0)
            $error_message[] = '84:' . link_safe(implode(', ', $no_basement2));
        }
      }
      else if(strlen($house['rooms']) > 0)
      {
        $rooms = explode(',', $house['rooms']);
        $newroom = $target;
        if(array_search($target, $rooms) !== false)
          $newloc = 'home/' . $target;
        else
          $error_message[] = '49:' . $target;
      }
      else
        $error_message[] = 50;

      if(count($error_message) == 0)
      {
        if($do_basement_move)
        {
          if(count($items) + $house['curbasement'] > $house['maxbasement'])
          {
            $error_message[] = '10:basement';
          }
          else
          {
            require_once 'commons/basementlib.php';

            foreach($items as $id)
              delete_inventory_byid($id);

            $item_report = array();

            foreach($item_names as $itemname=>$quantity)
            {
              add_to_basement($user['idnum'], $user['locid'], $itemname, $quantity);
              $item_report[] = ($quantity != 1 ? ($quantity . 'x ') : '') . $itemname;
            }

            recalc_basement_bulk($user['idnum'], $user['locid']);

            if($here == 'groupbox')
            {
              $database->FetchNone('
                INSERT INTO psypets_groupboxlogs
                (timestamp, groupid, userid, type, details)
                VALUES
                (
                  ' . $now . ',
                  ' . $groupid . ',
                  ' . $user['idnum'] . ',
                  \'remove\',
                  ' . quote_smart(implode(', ', $item_report)) . '
                )
              ');

              require_once 'commons/statlib.php';
              record_stat($user['idnum'], 'Removed an Item from a Group Box', count($items));
            }
          }
        }
        else
        {
          if($here == 'groupbox')
          {
            $item_report = array();
            foreach($item_names as $name=>$amount)
              $item_report[] = ($amount != 1 ? ($amount . 'x ') : '') . $name;

            $database->FetchNone('
              INSERT INTO psypets_groupboxlogs
              (timestamp, groupid, userid, type, details)
              VALUES
              (
                ' . $now . ',
                ' . $groupid . ',
                ' . $user['idnum'] . ',
                \'remove\',
                ' . quote_smart(implode(', ', $item_report)) . '
              )
            ');

            require_once 'commons/statlib.php';
            record_stat($user['idnum'], 'Removed an Item from a Group Box', count($items));

            $command = 'UPDATE monster_inventory SET user=' . quote_smart($user['user']) . ',`location`=' . quote_smart($newloc) . ',changed=' . $now . ',forsale=0 WHERE idnum IN (' . implode(',', $items) . ') LIMIT ' . count($items);
          }
          else
            $command = 'UPDATE monster_inventory SET `location`=' . quote_smart($newloc) . ',changed=' . $now . ',forsale=0 WHERE idnum IN (' . implode(',', $items) . ') LIMIT ' . count($items);
        
          $database->FetchNone($command, 'moving items');
        }

        if(substr($newloc, 0, 4) != 'home' || $_POST['from'] != 'home')
          recount_house_bulk($user, $house);
      }
    }
    else
      $error_message[] = 8;
  }

  else if($_POST['submit'] == 'Throw Out')
  {
    if(count($items) > 0)
    {
      foreach($items as $id)
        delete_inventory_byid($id);

      recount_house_bulk($user, $house);
    }
    else
      $error_message[] = 8;
  }

  else if($_POST['submit'] == 'Gamesell')
  {
    if(count($items) > 0)
    {
      $profit = 0;
      $sold_something = false;

      $delete_ids = array();
      $refuse_ids = array();
      $grocery_ids = array();

      foreach($items as $index=>$id)
      {
        $item = get_inventory_byid($id);
        $this_item = get_item_byname($item['itemname']);

        if($this_item['custom'] != 'no' || $this_item['nosellback'] == 'yes')
        {
          unset($items[$index]);
          $error_message[] = '42:' . $item['itemname'];
        }
        else
        {
          $sold_something = true;

          $sellback = ceil($this_item['value'] * sellback_rate());
          $profit += $sellback;

          if(mt_rand(1, 2) == 1)
            $delete_ids[] = $id;
          else
					{
						if($this_item['is_grocery'] == 'yes')
							$grocery_ids[] = $id;
						else
							$refuse_ids[] = $id;
					}
        }
      }

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

      if($profit > 0)
      {
        $user['money'] += $profit;

        $command = 'UPDATE monster_users ' .
                   "SET money=money+$profit " .
                   'WHERE idnum=' . quote_smart($user['idnum']) . ' LIMIT 1';
        $database->FetchNone($command, 'adding moneys');

        if($homemade_sell)
          $error_message[] = "17:$profit";
        else
          $error_message[] = "16:$profit";

        // update total sellback count
        require_once 'commons/questlib.php';

        $sellback = get_quest_value($user['idnum'], 'total sellback');
        $sellback_value = (int)$sellback['value'];

        $sellback_value += $profit;

        if($sellback === false)
          add_quest_value($user['idnum'], 'total sellback', $sellback_value);
        else
          update_quest_value($sellback['idnum'], $sellback_value);

        // check for sellback-related badges
        $badges = get_badges_byuserid($user['idnum']);

        if($badges['gamesell'] == 'no' || $badges['gamesellmore'] == 'no')
        {
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
      }
      else if(!$sold_something && $homemade_sell)
        $error_message[] = 15;
    }
  }

  if(count($error_message) > 0)
    $msg = '?msg=' . link_safe(implode(',', $error_message));
  else
    $msg = '';
}

if($here == 'home')
  header('Location: ./myhouse.php' . $msg);
else if($here == 'storage')
  header('Location: ./storage.php' . $msg);
else if($here == 'locked storage')
  header('Location: ./storage_locked.php' . $msg);
else if($here == 'post')
  header('Location: ./post.php' . $msg);
else if($here == 'tree')
  header('Location: ./givingtree.php' . $msg);
else if($here == 'basement')
  header('Location: ./addon_basement.php' . $msg);
else if($here == 'incoming')
  header('Location: ./incoming.php' . $msg);
else if($here == 'mystore')
  header('Location: ./mystore.php' . $msg);
else if($here == 'groupbox')
  header('Location: ./groupbox.php' . (strlen($msg) > 0 ? ($msg . '&') : '?') . 'id=' . $groupid);
else
  header('Location: ./myhouse.php' . $msg);
?>
