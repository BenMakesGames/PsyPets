<?php
require_once 'commons/itemlib.php';
require_once 'commons/grammar.php';
require_once 'commons/utility.php';

$MATERIALS_LIST = array();

function addon_exists(&$house, $addon)
{
/*
  // BETA TESTING
  if($addon == 'Airship Mooring')
    return true;
*/
  $addons = take_apart(',', $house['addons']);

  return(array_search($addon, $addons) !== false);
}

function say_room_of_house($room)
{
  if($room == 'storage')
    return 'Storage';
  else if($room == 'storage/incoming')
    return 'Incoming';
  else if($room == 'storage/locked')
    return 'Locked Storage';
  if($room == 'home')
    return 'Common';
  else
  {
    $room = substr($room, 5);
    if($room{0} == '$')
      $room = substr($room, 1);

    return ucfirst($room);
  }
}

function save_house_status(&$house)
{
  $command = 'UPDATE monster_houses SET rats=' . quote_smart($house['rats']) . ' WHERE idnum=' . $house['idnum'] . ' LIMIT 1';
  fetch_none($command, 'save_house_status');
}

// 2 pets at 50; 100 pets at 20,000.  plus bonuses.
function max_active_pets(&$user, &$house)
{
  $bulk = min(100000, $house['maxbulk']);

  $max = floor($bulk * 0.00098) + 2;

  if($user['license'] == 'yes')
    $max += 2;

  if($user['breeder'] == 'yes')
    $max += 10;

  return $max;
}

function room_display(&$house)
{
  global $THIS_ROOM;

  $m_rooms = take_apart(',', $house['rooms']);
  $addons = take_apart(',', $house['addons']);

  $rooms[] = 'Common';

  foreach($m_rooms as $room)
    $rooms[] = $room;

  echo '<ul class="tabbed">';

  $i = 0;
  foreach($rooms as $room)
  {
    if($i > $house['max_rooms_shown'])
      break;
  
    
    $classes = array();
    if(substr($room, 0, 1) == '$')
      $classes[] = 'locked-room';
    if($room == $THIS_ROOM)
      $classes[] = 'activetab';
  
    echo ' <li class="' . implode(' ', $classes) . '"><nobr><a href="/houseaction.php?room=' . link_safe($room) . '">' . str_replace('$', '', $room) . '</a></nobr></li>';

    $i++;
  }

  $i = 0;
  foreach($addons as $room)
  {
    if($i >= $house['max_addons_shown'])
      break;
  
    $classes = array('addontab');
    if($room == $THIS_ROOM)
      $classes[] = 'activetab';
  
    echo ' <li class="' . implode(' ', $classes) . '" style="background-image: url(//' . $SETTINGS['static_domain'] . '/gfx/addons/' . urlize($room) . '.png);"><nobr><a href="/myhouse/addon/' . urlize($room) . '.php">' . $room . '</a></nobr></li>';

    $i++;
  }

  echo '<li style="border: 0; background-color: transparent;"><a href="/managerooms.php"><img src="/gfx/pencil_small.png" height="13" width="15" alt="(manage rooms)" style="vertical-align:text-bottom;" /></a></li>';

  echo '</ul>';
}

function get_home_improvement_byid($idnum)
{
  $command = 'SELECT * FROM psypets_homeimprovement WHERE idnum=' . $idnum . ' LIMIT 1';
  $improvement = fetch_single($command, 'get_home_improvement_byid');

  return $improvement;
}

function add_house($userid, $locid, $size)
{
  $command = "INSERT INTO monster_houses (`userid`, `maxbulk`, `rooms`, `wallpapers`) VALUES ($userid, $size, '\$Protected', 'none,none')";
  fetch_none($command, 'add_house');
}

function get_house_byid($idnum)
{
  $command = 'SELECT * FROM monster_houses WHERE idnum=' . (int)$idnum . ' LIMIT 1';
  $house = fetch_single($command, 'get_house_byid');

  return $house;
}

function storage_bulk($username, $locid = 0)
{
  $command = 'SELECT COUNT(a.itemname)*b.bulk AS total_bulk FROM monster_inventory AS a LEFT JOIN monster_items AS b ON a.itemname=b.itemname WHERE user=' . quote_smart($username) . ' AND location LIKE \'storage%\' GROUP BY(a.itemname)';
  $numbers = fetch_multiple($command, 'fetching bulk totals per item');

  $total_size = 0;
  
  foreach($numbers as $bulk)
    $total_size += $bulk['total_bulk'];

  return $total_size;
}

function get_projects_byloc($userid)
{
	return $GLOBALS['database']->FetchMultipleBy('
		SELECT *
		FROM monster_projects
		WHERE userid=' . (int)$userid . '
	', 'idnum');
}

function get_house_byuser($userid)
{
	return $GLOBALS['database']->FetchSingle('
		SELECT *
		FROM monster_houses
		WHERE userid=' . (int)$userid . '
		LIMIT 1
	');
}

function home_improvement($locid, $ownerid, $amount)
{
  $command = "UPDATE monster_houses SET maxbulk=maxbulk+$amount WHERE userid=$ownerid LIMIT 1";
  fetch_none($command, 'adding to house\'s max bulk');

  if($GLOBALS['database']->AffectedRows() == 0)
  {
    echo "home_improvement($locid, $ownerid, $amount)<br />\n" .
         "Error in <i>$command</i><br />\n" .
         "no rows were affected<br />\n";
    exit();
  }
}

function upgrade_house($idnum, $bulk)
{
  $command = "UPDATE monster_houses SET maxbulk='$bulk' WHERE idnum=$idnum LIMIT 1";
  fetch_none($command, 'upgrade_house');
}

function render_house_bulk(&$house)
{
  $effective_max_bulk = min(max_house_size(), $house['maxbulk']);

  if($effective_max_bulk < $house['maxbulk'])
    $house_note = '<a href="realestate.php">*</a>';
  else
    $house_note = '';

  return ($house['curbulk'] / 10) . '/' . ($effective_max_bulk / 10) . $house_note . '; ' . ceil($house['curbulk'] * 100 / $effective_max_bulk) . '% full';
}

function recount_house_bulk(&$user, &$house)
{
  $stats = get_housestats_byloc($user);
  $pets = get_pets_byuser($user['user'], 'home');

  foreach($pets as $pet)
    $stats['bulk'] += pet_size($pet);

  update_house_bulk($house['idnum'], $stats['bulk']);
  
  return $stats['bulk'];
}

function update_house_bulk($houseid, $bulk)
{
  $command = "UPDATE monster_houses SET curbulk='$bulk' WHERE idnum=$houseid LIMIT 1";
  fetch_none($command, 'update_house_bulk');
}

function get_housestats_byloc(&$user, $locid = 0)
{
  global $MATERIALS_LIST;

  $stats = array('bulk' => 0, 'hourlyfood' => 0, 'hourlysafety' => 0, 'hourlylove' => 0, 'hourlyesteem' => 0);
  $MATERIALS_LIST = array();
  $house_items = array();
  
  $these_items = $GLOBALS['database']->FetchMultiple('
    SELECT COUNT(idnum) AS qty,itemname,location
    FROM monster_inventory
    WHERE
      user=' . quote_smart($user['user']) . '
      AND location LIKE \'home%\'
    GROUP BY itemname,location
  ');

  foreach($these_items as $this_item)
  {
    if(substr($this_item['location'], 0, 6) != 'home/$')
      $MATERIALS_LIST[$this_item['itemname']] += $this_item['qty'];

    $house_items[$this_item['itemname']] += $this_item['qty'];
  }

  foreach($house_items as $itemname=>$quantity)
  {
    $item_details = get_item_byname($itemname, 'bulk');
    if($item_details !== false)
    {
      $stats['bulk']         += $quantity * $item_details['bulk'];
      //$stats['hourlyfood']   += $quantity * $item_details['hourlyfood'];
      //$stats['hourlysafety'] += $quantity * $item_details['hourlysafety'];
      //$stats['hourlylove']   += $quantity * $item_details['hourlylove'];
      //$stats['hourlyesteem'] += $quantity * $item_details['hourlyesteem'];
    }
  }

  return $stats;
}

function room_actions(&$user, &$house, &$rooms)
{
  $room_list['storage'] = 'Storage';

  if($user['license'] == 'yes')
  {
    $rooms_list['storage:locked'] = 'Locked Storage';
    $rooms_list['storage:mystore'] = 'My Store';
  }

  $room_list['home:0'] = 'Unsorted items';
  $rooms[] = 'Common';

  if(strlen($house['rooms']) > 0)
  {
    $m_rooms = explode(',', $house['rooms']);
    foreach($m_rooms as $room)
      $rooms[] = $room;
  }

  $addons = take_apart(',', $house['addons']);

  if(array_search('Library', $addons) !== false)
    $rooms[] = 'Library Add-on';
  if(array_search('Basement', $addons) !== false)
    $rooms[] = 'Basement';
?>
<div id="message_area"></div>
 <form action="/moveinventory2.php?confirm=1" method="post" name="homeaction" id="homeaction">
 <input type="hidden" name="from" value="home" />
 <input type="hidden" name="recipe1" value="none" />
<?php
  toolbar($rooms, $curroom, $userpets, 1);
  inventory_view($user, $house, $userpets, $inventory);

  echo toolbar($rooms, $curroom, $userpets, 2); // returns the context menu; this time we echo it

  echo '</form>';

}

function room_view(&$user, &$room, &$pets, &$inventory)
{
  if(count($inventory) == 0)
  {
    echo '<p>This room is empty!</p>';
    return;
  }
}

function house_view(&$user, &$house, &$userpets, &$inventory)
{
  if(count($inventory) == 0)
  {
    echo '<p>There are no items here.</p>';
    return;
  }

  $rooms[] = 'Storage';

  if($user['license'] == 'yes')
  {
    $rooms[] = 'Locked Storage';
    $rooms[] = 'My Store';
  }

  $rooms[] = 'Common';

  if(strlen($house['rooms']) > 0)
  {
    $m_rooms = explode(',', $house['rooms']);
    foreach($m_rooms as $room)
      $rooms[] = $room;
  }

  $addons = take_apart(',', $house['addons']);

  if(array_search('Library', $addons) !== false)
    $rooms[] = 'Library Add-on';
  if(array_search('Basement', $addons) !== false)
    $rooms[] = 'Basement';

  if(strlen($house['curroom']) > 0)
  {
    $curroom = $house['curroom'];
    $curlocation = 'home/' . $curroom;
  }
  else
  {
    $curroom = 'Common';
    $curlocation = 'home';
  }
?>
<div id="message_area"></div>
 <form action="moveinventory2.php?confirm=1" method="post" name="homeaction" id="homeaction">
 <input type="hidden" name="from" value="home" />
 <input type="hidden" name="recipe1" value="none" />
<?php
  toolbar($rooms, $curroom, $userpets, 1);
  inventory_view($user, $house, $userpets, $inventory);

  echo toolbar($rooms, $curroom, $userpets, 2); // returns the context menu; this time we echo it

  echo '</form>';
}

function toolbar(&$rooms, &$curroom, &$userpets, $num)
{
  $hide_buttons = ($curroom{0} == '$');

  if($num == 1)
    $o_num = 2;
  else
    $o_num = 1;

  $context_menu = '<ul id="inventorymenu" class="jeegoocontext">';

  echo '<table class="nomargin"><tr>';

  if(!$hide_buttons)
  {
    $context_menu .= '<li>Feed to<ul>';
?>
    <td>
     <input type="submit" name="submit" value="Feed to" class="button_feedto" id="feedto<?= $num ?>" />&nbsp;<select id="pet<?= $num ?>" name="pet<?= $num ?>" style="<?= $style ?>" onchange="document.getElementById('pet<?= $o_num ?>').selectedIndex = this.selectedIndex">
<?php
    if(count($userpets) > 1)
    {
      echo '<option value="multiple">(multiple...)</option>';
      $context_menu .= '<li><a href="#" onclick="context_feed(\'multiple\'); return false;">multiple...</a></li>';
    }

    $pet_count = 0;

    foreach($userpets as $pet)
    {
      if($pet['dead'] == 'no' && $pet['sleeping'] == 'no')
      {
        echo '<option value="' . $pet['idnum'] . '">' . htmlspecialchars($pet['petname']) . '</option>';
        $context_menu .= '<li><a href="#" onclick="context_feed(' . $pet['idnum'] . '); return false;">' . htmlspecialchars($pet['petname']) . '</a></li>';
      }
    }

    echo '</select></td>';
    
    $context_menu .= '</ul></li>';
  }
  
  $context_menu .= '<li>Move to<ul>';
?>
    <td>
     <input type="button" value="Move to" onclick="move_items('move')" id="moveto<?= $num ?>" />&nbsp;<select id="move<?= $num ?>" name="move<?= $num ?>" style="width:100px;" onchange="document.getElementById('move<?= $o_num ?>').selectedIndex = this.selectedIndex">
<?php
  foreach($rooms as $room)
  {
    if($room != $curroom)
    {
      echo '<option value="' . $room . '">' . ($room{0} == '$' ? substr($room, 1) : $room) . '</option>';
      $context_menu .= '<li><a href="#" onclick="context_move(\'' . $room . '\'); return false;">' . $room . '</li>';
    }
  }

  echo '</select></td>';
  
  $context_menu .= '</ul></li><li><a href="#" onclick="context_prepare(); return false;">Prepare</a></li>';
?>
<td>
 <input type="submit" name="submit" value="Prepare" class="button_prepare" id="prepare<?= $num ?>" />&nbsp;(<a href="#" onclick="openpreparewindow(); return false;">show favorite recipes</a>)
</td>
<?php
  if(!$hide_buttons)
  {
    $context_menu .= '<li><a href="#" onclick="context_gamesell(); return false;">Gamesell</a></li>';
    $context_menu .= '<li><a href="#" onclick="context_throwout(); return false;">Throw Out</a></li>';
?>
<td><input type="button" value="Gamesell" onclick="sell_items()" class="button_gamesell" id="gamesell<?= $num ?>" /></td>
<td><input type="button" value="Throw Out" onclick="trash_items()" class="button_throwout" id="throwout<?= $num ?>" /></td>
<?php
  }
  
  $context_menu .= '</ul>';

  echo '</tr></table>';
  
  return $context_menu;
}

function inventory_view(&$user, &$house, &$userpets, &$inventory)
{
  global $now;

  echo '<ul class="filter">';

  if($house['sort'] == 'bulk')
    echo '<li>Size/Weight&nbsp;<a href="houseaction.php?sortby=idnum">&#9650;</a></li>';
  else
    echo '<li>Size/Weight&nbsp;<a href="houseaction.php?sortby=bulk">&#9651;</a></li>';

  if($house['sort'] == 'itemname')
    echo '<li>Name&nbsp;<a href="houseaction.php?sortby=idnum">&#9660;</a></li>';
  else
    echo '<li>Name&nbsp;<a href="houseaction.php?sortby=itemname">&#9661;</a></li>';

  if($house['sort'] == 'itemtype')
    echo '<li>Type&nbsp;<a href="houseaction.php?sortby=idnum">&#9660;</a></li>';
  else
    echo '<li>Type&nbsp;<a href="houseaction.php?sortby=itemtype">&#9661;</a></li>';

  if($house['sort'] == 'ediblefood')
    echo '<li>Meal&nbsp;Size&nbsp;<a href="houseaction.php?sortby=idnum">&#9650;</a></li>';
  else
    echo '<li>Meal&nbsp;Size&nbsp;<a href="houseaction.php?sortby=ediblefood">&#9651;</a></li>';

  if($house['sort'] == 'message')
    echo '<li>Comment&nbsp;<a href="houseaction.php?sortby=idnum">&#9660;</a></li>';
  else
    echo '<li>Comment&nbsp;<a href="houseaction.php?sortby=message">&#9661;</a></li>';

  echo '</ul><div id="roominventory">';

  if($house['view'] == 'icons')
    render_inventory_xhtml_3($inventory);
  else if($house['view'] == 'details')
    render_inventory_xhtml_3_list($inventory);

  echo '</div>';
}

function house_value($size)
{
  return floor((($size - 50) * 3) / 2 + 50) * 10;
}

function house_is_full($myuser, $mypets)
{
  return(house_bulk($myuser, $mypets) >= $myuser['homesize']);
}
?>
