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
require_once 'commons/itemstats.php';

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
$tossed = array();

foreach($itemids as $id)
{
  $item = get_inventory_byid($id);

  if($item['user'] == $user['user'] && $item['location'] != 'pet' && $item['location'] != 'storage/outgoing')
  {
    $item_details = get_item_byname($item['itemname']);

    if($item_details === false)
      continue;

    if($item_details['cursed'] == 'yes' || $item_details['questitem'] == 'yes')
    {
      $failed_ids[] = $id;
    }
    else if($item_details['custom'] == 'yes' || $item_details['custom'] == 'monthly' || $item_details['custom'] == 'recurring' || $item_details['custom'] == 'limited')
    {
      $failed_ids[] = $id;
      $reveal_volcano = true;
    }
    else
    {
      $moved_ids[] = $id;
      $tossed[$item['itemname']]++;
    }
  }
  else
    $failed_ids[] = $id;
}

if(count($tossed) > 0)
{
  foreach($tossed as $item=>$quantity)
    record_item_disposal($item, 'tossed', $quantity);
}

if(count($moved_ids) > 0)
{
  $command = 'DELETE FROM monster_inventory WHERE idnum IN (' . implode(',', $moved_ids) . ') LIMIT ' . count($moved_ids);
  $database->FetchNone($command, 'deleting items');

  $new_bulk = recount_house_bulk($user, $house);
  $house['curbulk'] = $new_bulk;
    
  echo 'newbulk:' . render_house_bulk($house) . "\n";
}

if(count($moved_ids) > 0)
{
  echo 'message:<span class="success">' . count($moved_ids) . ' item' . (count($moved_ids) == 1 ? ' was' : 's were') . ' thrown away.';

  if(count($failed_ids) > 0)
    echo '  ' . count($failed_ids) . ' item' . (count($failed_ids) == 1 ? '' : 's') . ' could not be moved.';

  echo '</span>' . "\n" . 
       'success:' . implode(',', $moved_ids);
}

if(count($failed_ids) > 0)
{
  if(count($moved_ids) == 0)
    echo 'message:<span class="failure">The selected item' . (count($failed_ids) == 1 ? '' : 's') . ' could not be thrown away!</span>';

  echo "\n" . 'failure:' . implode(',', $failed_ids);
}

if($reveal_volcano === true)
{
  if($user['show_volcano'] == 'no')
  {
    $command = 'UPDATE monster_users SET show_volcano=\'yes\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'revealing volcano');
    
    echo "\n" . 'message:<span class="success">(<a href="volcano.php">The Volcano</a> has been revealed to you!  Find it in the Services menu.)</i>';
  }
}
?>
