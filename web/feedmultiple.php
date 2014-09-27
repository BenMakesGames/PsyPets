<?php
$require_petload = 'no';
$invisible = 'yes';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/formatting.php';
require_once 'commons/houselib.php';
require_once 'commons/petlib.php';

require_once 'commons/flavorlib.php';

$house = get_house_byuser($user['idnum']);
if($house === false)
{
  echo 'Error loading your house.  If this problem persists (especially if there\'s nothing about it in the City Hall), please report it to <a href="admincontact.php">an administrator</a>.';
  exit();
}

$houseloc = 'home' . (strlen($house['curroom']) > 0 ? '/' . $house['curroom'] : '');

$here = $_POST['from'];

$error_message = array();

$itemids = array();
$pets_by_id = array();
$old_pet = array();
$skipped_by_pet = array();

foreach($_POST as $key=>$value)
{
  if($key{0} == 'i')
  {
    $itemid = (int)substr($key, 1);
    $item = get_inventory_byid($itemid);
    
    if($item === false)
      continue;
    
    if($item['user'] != $user['user'] || $item['location'] == 'seized' || $item['location'] == 'pet')
    {
      $error_message[] = 28;
      continue;
    }
    
    $details = get_item_byname($item['itemname']);
    
    if($details === false || $details['cursed'] == 'yes')
    {
      $error_message[] = '31:' . $item_details['itemname'];
      continue;
    }
    else if($details['is_edible'] == 'no')
    {
      $error_message[] = '6:' . $item['itemname'];
      continue;
    }
    
    $petid = (int)$value;
    
    if($petid == 0)
      continue;
      
    if($pets_by_id[$petid]['idnum'] != $petid)
    {
      $pets_by_id[$petid] = get_pet_byid($petid);
      $old_pet[$petid] = $pets_by_id[$petid];
    }
    
    if($pets_by_id[$petid]['user'] != $user['user'])
    {
      $error_message[] = 9;
      continue;
    }
    else if($pets_by_id[$petid]['dead'] != 'no')
    {
      $error_message[] = 87;
      continue;
    }
    else if($pets_by_id[$petid]['sleeping'] == 'yes')
    {
      $error_message[] = '88:' . urlencode($pets_by_id[$petid]['petname']);
      continue;
    }
    else if($pets_by_id[$petid]['food'] >= max_food($pets_by_id[$petid]) && $details['ediblefood'] >= 0)
    {
      $skipped_by_pet[$petid]++;
      continue;
    }

    $made_of = array_merge(
      take_apart(',', $details['recycle_for']),
      take_apart(',', $details['additional_flavors'])
    );

    $dislikes = ($FLAVORS[$pets_by_id[$petid]['dislikes_flavor']] == $item['itemname'] || in_array($FLAVORS[$pets_by_id[$petid]['dislikes_flavor']], $made_of));
    $likes = ($FLAVORS[$pets_by_id[$petid]['likes_flavor']] == $item['itemname'] || in_array($FLAVORS[$pets_by_id[$petid]['likes_flavor']], $made_of));

    if($likes && $dislikes)
    {
      $error_message[] = '149:' . $pets_by_id[$petid]['petname'] . ' has mixed feelings about ' . $item['itemname'] . '.';

      $likes = false;
      $dislikes = false;
    }

    if($dislikes)
      $error_message[] = '149:' . $pets_by_id[$petid]['petname'] . ' doesn\'t care for ' . $item['itemname'] . '.';

    if($likes)
      $error_message[] = '150:' . $pets_by_id[$petid]['petname'] . ' really likes ' . $item['itemname'] . '!';

    $fed_foods[$petid][] = $item['itemname'];

    if($details['edibleesteem'] >= 0 && !$dislikes)
    {
      // items given to you by someone else are worth their 50% their love value, in addition to their esteem value
      if($item['creator']{0} == 'u' && $item['creator'] != ('u:' . $user['idnum']) && $item["esteembonus"] == 'yes')
        $esteem_value = $details['ediblelove'] / 2 + $details['edibleesteem'];
      else
        $esteem_value = $details['edibleesteem'];

      gain_esteem($pets_by_id[$petid], $esteem_value);
    }
    else if($details['edibleesteem'] < 0)
      lose_stat($pets_by_id[$petid], 'esteem', -$details['edibleesteem']);

    if($details['ediblelove'] > 0 && !$dislikes)
    {
      // if you give the pet something you made, the item grants 50% more love :)
      if($item['creator'] == 'u:' . $user['idnum'])
        $love_value = $details['ediblelove'] * 1.5;
      else
        $love_value = $details['ediblelove'];

      if($likes)
        gain_safety($pets_by_id[$petid], ceil($love_value / 3));

      gain_love($pets_by_id[$petid], $love_value);
      
      if($likes)
        gain_esteem($pets_by_id[$petid], ceil($love_value / 3));
    }
    else if($details['ediblelove'] < 0)
      lose_stat($pets_by_id[$petid], 'love', -$details['ediblelove']);

    if($details['ediblesafety'] > 0 && !$dislikes)
      gain_safety($pets_by_id[$petid], $details['ediblesafety'], true);
    // un-safe food? (hallucinogenic)
    else if($details['ediblesafety'] < 0)
      lose_stat($pets_by_id[$petid], 'safety', -$details['ediblesafety']);

    if($likes)
    {
      if($details['ediblefood'] >= 4)
        $details['ediblefood'] += 2;
      else if($details['ediblefood'] > 0)
        $details['ediblefood']++;
    }

    if($details['ediblehealing'] > 0)
    {
      if($pets_by_id[$petid]['healing'] >= $details['ediblehealing'])
        $error_message[] = '164:' . $pets_by_id[$petid]['petname'];
      else
      {
        $healing_amount = gain_healing($pets_by_id[$petid], $details['ediblehealing']);

        gain_love_exp($pets_by_id[$petid], ceil(sqrt($healing_amount)));

        if($healing_amount > 8)
        {
          $error_message[] = '165:' . $pets_by_id[$petid]['petname'];
        }
        else
          $error_message[] = '166:' . $pets_by_id[$petid]['petname'];
      }
    }
    
    if($details['ediblefood'] > 0)
      gain_food($pets_by_id[$petid], $details['ediblefood']);
    // if the food is bad...
    else if($details['ediblefood'] < 0)
      $pets_by_id[$petid]['food'] += $details['ediblefood'];

    if($details['edibleenergy'] > 0)
      gain_energy($pets_by_id[$petid], $details['edibleenergy']);
    else if($details['edibleenergy'] < 0)
      $pets_by_id[$petid]['energy'] += $details['edibleenergy'];

    if($details['ediblecaffeine'] > 0)
      gain_caffeine($pets_by_id[$petid], $details['ediblecaffeine']);

    if(!$dislikes)
    {
      $love_exp = 0;
      if(mt_rand(1, 10) < $details['ediblefood'] + $details['ediblelove'])
        $love_exp++;

      if(mt_rand(1, 10) < $details['ediblesafety'] + $details['edibleesteem'])
        $love_exp++;

      if($item['creator'] == 'u:' . $user['idnum'])
        $love_exp++;
        
      if($likes)
        $love_exp += mt_rand(2, 3);
        
      if($love_exp > 0)
        gain_love_exp($pets_by_id[$petid], $love_exp);
    }

    $error_message[] = '7:' . urlencode($pets_by_id[$petid]['petname']);

    if($item['itemname'] == 'Eggplant' && mt_rand(1, 1000) == 1 && $pets_by_id[$petid]['eggplant'] == 'no')
    {
      $pets_by_id[$petid]['eggplant'] = 'yes';
      $error_message[] = '151:' . urlencode($pets_by_id[$petid]['petname']);
    }

    delete_inventory_byid($item['idnum']);
  } // for each item
}

foreach($skipped_by_pet as $petid=>$count)
  $error_message[] = '153:' . urlencode($pets_by_id[$petid]['petname']);

foreach($pets_by_id as $petid=>$pet)
{
  if($pet['user'] == $user['user'])
  {
    save_pet($pet, array('esteem', 'love', 'safety', 'food', 'energy', 'caffeinated', 'eggplant', 'love_exp', 'healing', 'nasty_wound'));

    $extras['energy'] = $pet['energy'] - $old_pet[$petid]['energy'];
    $extras['food'] = $pet['food'] - $old_pet[$petid]['food'];
    $extras['safety'] = $pet['safety'] - $old_pet[$petid]['safety'];
    $extras['love'] = $pet['love'] - $old_pet[$petid]['love'];
    $extras['esteem'] = $pet['esteem'] - $old_pet[$petid]['esteem'];

    add_logged_event($user['idnum'], $petid, 0, 'realtime', false, 'Was fed: ' . implode(', ', $fed_foods[$petid]), $extras);
  }
}

header('Location: /myhouse.php?msg=' . implode(',', $error_message));
?>
