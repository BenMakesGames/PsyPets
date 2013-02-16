<?php
require_once 'commons/cachelib.php';

$INVENTORY_TO_ADD = array();

function itemname_to_form_value($itemname)
{
  $conversions = array(
    ' ' => '__0__',
    '\'' => '__1__',
    '"' => '__2__',
    '.' => '__3__',
    '`' => '__4__',
    '#' => '__5__',
    '!' => '__6__',
    '?' => '__7__',
    '-' => '__8__',
    '/' => '__9__',
  );
  
  return str_replace(array_keys($conversions), array_values($conversions), $itemname);
}

function itemname_from_form_value($form_value)
{
  $conversions = array(
    ' ' => '__0__',
    '\'' => '__1__',
    '"' => '__2__',
    '.' => '__3__',
    '`' => '__4__',
    '#' => '__5__',
    '!' => '__6__',
    '?' => '__7__',
    '-' => '__8__',
    '/' => '__9__',
  );

  return str_replace(array_values($conversions), array_keys($conversions), $form_value);
}

function get_craft_byid($idnum)
{
  return _get_project_byid($idnum, 'craft');
}

function get_tailor_byid($idnum)
{
  return _get_project_byid($idnum, 'tailor');
}

function get_invention_byid($idnum)
{
  return _get_project_byid($idnum, 'invention');
}

function get_mechanics_byid($idnum)
{
  return _get_project_byid($idnum, 'mechanic');
}

function get_smith_byid($idnum)
{
  return _get_project_byid($idnum, 'smith');
}

function get_binding_byid($idnum)
{
  return _get_project_byid($idnum, 'binding');
}

function get_carpentry_byid($idnum)
{
  return fetch_single('SELECT * FROM `psypets_carpentry` WHERE idnum=' . $idnum . ' LIMIT 1');
}

function get_painting_byid($idnum)
{
  return _get_project_byid($idnum, 'painting');
}

function get_sculpture_byid($idnum)
{
  return _get_project_byid($idnum, 'sculpture');
}

function get_leatherworking_byid($idnum)
{
  return _get_project_byid($idnum, 'leatherwork');
}

function get_jewelry_byid($idnum)
{
  return fetch_single('SELECT * FROM `psypets_jewelry` WHERE idnum=' . $idnum . ' LIMIT 1');
}

function get_chemistry_byid($idnum)
{
  return fetch_single('SELECT * FROM `psypets_chemistry` WHERE idnum=' . $idnum . ' LIMIT 1');
}

function get_gardening_byid($idnum)
{
  return fetch_single('SELECT * FROM `psypets_gardening` WHERE idnum=' . $idnum . ' LIMIT 1');
}

function _get_project_byid($idnum, $type)
{
  return fetch_single('SELECT * FROM `psypets_' . $type . 's` WHERE idnum=' . $idnum . ' LIMIT 1');
}

function process_cached_inventory()
{
  global $INVENTORY_TO_ADD;

  if(count($INVENTORY_TO_ADD) == 0)
    return;

	$keys = false;

  foreach($INVENTORY_TO_ADD as $data)
  {
		if($keys === false)
			$keys = '(`' . implode('`, `', array_keys($data)) . '`)';

    // quote smart each value
    foreach($data as $key=>$value)
      $data[$key] = quote_smart($value);

    $newdata[] = '(' . implode(', ', $data) . ')';
  }

  $values = implode(', ', $newdata);

  $command = "INSERT INTO monster_inventory $keys VALUES $values";
  fetch_none($command, 'item library > process cached inventory');

  $INVENTORY_TO_ADD = array();
}

function delete_inventory_byname($user, $itemname, $quantity, $location)
{
  $GLOBALS['database']->FetchNone('DELETE FROM `monster_inventory` WHERE `user`=' . quote_smart($user) . ' AND `itemname`=' . quote_smart($itemname) . ' AND `location`=' . quote_smart($location) . ' LIMIT ' . ((int)$quantity));

  return $GLOBALS['database']->AffectedRows();
}

function delete_inventory_fromhome($user, $itemname, $quantity)
{
	$removed += remove_cached_add_inventory_by('itemname', $itemname, $quantity);
	$quantity -= $removed;

	if($quantity > 0)
	{
		fetch_none('DELETE FROM `monster_inventory` WHERE `user`=' . quote_smart($user) . ' AND `itemname`=' . quote_smart($itemname) . ' AND location LIKE ' . quote_smart('home%') . " AND location NOT LIKE 'home/$%' LIMIT " . ((int)$quantity));

		return $GLOBALS['database']->AffectedRows() + $removed;
	}
	else
		return $removed;
}

function delete_inventory_fromstorage($user, $itemname, $quantity)
{
  fetch_none('DELETE FROM `monster_inventory` WHERE `user`=' . quote_smart($user) . " AND `itemname`=" . quote_smart($itemname) . " AND location='storage' LIMIT " . ((int)$quantity));

  return $GLOBALS['database']->AffectedRows();
}

function add_inventory($user, $maker, $name, $message, $location)
{
  global $now;

  $q_user = quote_smart($user);
  $q_maker = quote_smart($maker);
  $q_itemname = quote_smart($name);
  $q_message = quote_smart($message);
  $q_location = quote_smart($location);

  $item = get_item_byname($name);

  $command = 'INSERT INTO `monster_inventory` (`user`, `creator`, `itemname`, `health`, `message`, `location`, `changed`) VALUES ' .
             '(' . $q_user . ', ' . $q_maker . ', ' . $q_itemname . ', ' . $item['durability'] . ', ' . $q_message . ', ' . $q_location . ', ' . $now . ')';
  fetch_none($command, 'itemlib.php/add_inventory()');

  return $GLOBALS['database']->InsertID();
}

function remove_cached_add_inventory_by($key, $value, $quantity = 1)
{
  global $INVENTORY_TO_ADD;

	$remove_keys = array();

	foreach($INVENTORY_TO_ADD as $i=>$item)
	{
		if($item[$key] === $value)
		{
			$remove_keys[] = $i;
			$quantity--;

			if($quantity == 0)
				break;
		}
	}
	
	if(count($remove_keys) > 0)
	{
		foreach($remove_keys as $key)
			unset($INVENTORY_TO_ADD[$key]);
	}
	
	return count($remove_keys);
}

function add_inventory_cached($user, $maker, $name, $message, $location)
{
  global $INVENTORY_TO_ADD;
  global $now;

  $item = get_item_byname($name);
  
  if($item === false)
    return false;

  $INVENTORY_TO_ADD[] = array('user' => $user, 'creator' => $maker, 'itemname' => $name, 'health' => $item['durability'], 'message' => $message, 'location' => $location, 'changed' => $now);

  return true;
}

function add_inventory_quantity($user, $maker, $name, $message, $location, $quantity)
{
  global $now;

	if($quantity == 0)
		return;
	
  $q_user = quote_smart($user);
  $q_maker = quote_smart($maker);
  $q_itemname = quote_smart($name);
  $q_message = quote_smart($message);
  $q_location = quote_smart($location);

  $item = get_item_byname($name);

  if($item === false)
  {
    echo "adding bulk inventory (1)<br />\n" .
         "There is no item named '$name'<br />\n" .
    exit();
  }

  $item_data = "($q_user, $q_maker, $q_itemname, " . $item['durability'] . ", $q_message, $q_location, $now)";


  $command = "INSERT INTO `monster_inventory` (`user`, `creator`, `itemname`, `health`, `message`, `location`, `changed`) VALUES $item_data";
  if($quantity > 1)
    $command .= str_repeat(', ' . $item_data, $quantity - 1);

  fetch_none($command, 'adding bulk inventory (2)');
}

function get_item_byname($name, $reload_from_db = false)
{
  $key = md5('item by name:' . $name);

  $item = cache_get($key);

  if(!is_array($item) || $reload_from_db === true)
  {
    $item = fetch_single('
      SELECT *
      FROM `monster_items`
      WHERE `itemname`=' . quote_smart($name) . '
      LIMIT 1
    ');
    
    cache_add($key, $item);
  }

  return $item;
}

function get_item_byid($id, $reload_from_db = false)
{
  $key = md5('item by id:' . $id);

  $item = cache_get($key);
  
  if($item === false || $reload_from_db === true)
  {
    $item = fetch_single('
      SELECT *
      FROM `monster_items`
      WHERE `idnum`=' . (int)$id . '
      LIMIT 1
    ');

    cache_add($key, $item);
  }
  
  return $item;
}

function delete_inventory_byid($id)
{
  $GLOBALS['database']->FetchNone('DELETE FROM monster_inventory WHERE idnum=' . quote_smart($id) . ' LIMIT 1');
  
  return $GLOBALS['database']->AffectedRows();
}

function get_inventory_byid($id)
{
  return $GLOBALS['database']->FetchSingle('SELECT * FROM `monster_inventory` WHERE `idnum`=' . (int)$id . ' LIMIT 1');
}

function get_houseinventory_byuser($username)
{
  return $GLOBALS['database']->FetchMultiple('SELECT * FROM `monster_inventory` WHERE `user`=' . quote_smart($username) . ' AND `location` LIKE \'home%\'');
}

function grow_time($details)
{
  if(substr($details['itemtype'], 0, 5) == "food/")
    $mult = 8;
  else
    $mult = 12;

  $time = (1 + ($details['weight'] / 10 + 1) * $mult + $details['bulk'] / 5 + ceil(($details['ediblefood'] + $details['ediblelove']) / 2)) * 60 * 60;

  return $time;
}

function get_houseinventory_byuser_forpets($username)
{
  return $GLOBALS['database']->FetchMultiple('SELECT * FROM monster_inventory WHERE user=' . quote_smart($username) . ' AND location LIKE \'home%\' AND location NOT LIKE \'home/$%\' ORDER BY itemname ASC');
}

function get_inventory_byuser($username, $loc)
{
  return $GLOBALS['database']->FetchMultiple("SELECT * FROM `monster_inventory` WHERE `user`=" . quote_smart($username) . " AND `location`=" . quote_smart($loc) . " ORDER BY `itemname` ASC");
}

function repair_cost($cur, $max, $value)
{
  $x = 1 - ($cur / $max);

  $y = pow((4 * $x - 2), 1/3) / 2.5 + 0.6;

  $y = max(0.1, $y);

  return ceil($y * $value);
}

function item_maker_display($creator, $link = false)
{
  if($creator == '')
    return '&ndash;';
  else if($creator{0} == 'u')
  {
    $maker_user = get_user_byid(substr($creator, 2), 'display');
    if($maker_user === false)
      return '<i class="dim">[departed]</i>';
    else if($link)
      return resident_link($maker_user['display']);
    else
      return $maker_user['display'];
  }
  else if($creator{0} == 'p')
  {
    $petid = substr($creator, 2);
  
    $maker_pet = get_pet_byid($petid, 'petname');
    if($maker_pet === false)
      return '<i class="dim">[departed]</i>';
    else if($link)
      return '<a href="/petprofile.php?petid=' . $petid . '">' . $maker_pet['petname'] . '</a>';
    else
      return $maker_pet['petname'];
  }
}
?>
