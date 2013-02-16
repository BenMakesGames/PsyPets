<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$whereat = 'home';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/houselib.php';
require_once 'commons/utility.php';
require_once 'commons/librarylib.php';

if(!addon_exists($house, 'Library'))
{
  header('Location: /myhouse.php');
  exit();
}

if($_POST['submit'] == 'Move to')
{
/*
  if($user['user'] == 'telkoth')
  {
    print_r($_POST);
    die();
  }
*/
  $items = array();
  $total_size = 0;
  $total_quantity = 0;

  foreach($_POST as $key=>$value)
  {
    if(substr($key, 0, 2) == 'i_')
    {
      $quantity = (int)$value;
      if($quantity > 0)
      {
        $itemid = (int)substr($key, 2);
        $item = get_library_book($user['idnum'], $itemid);

        if($item === false)
        {
          $error_message[] = 28;
          $items = array();
          break;
        }
        else if($item['quantity'] < $quantity)
        {
          $error_message[] = '83:' . $item['itemname'];
          $items = array();
          break;
        }

        $details = get_item_byname($item['itemname']);

        $items[$item['itemname']] = array('remove' => $quantity, 'total' => $item['quantity'], 'itemid' => $itemid);
        $total_size += $details['bulk'];
        $total_quantity += $quantity;
      }
    }
  }

  if(count($items) > 0)
  {
    $target = trim($_POST['room']);
    $check_size = true;

    if($target == 'Storage')
    {
      $newloc = 'storage';
      $check_size = false;
      $that_room = 'storage';
    }
    else if($target == 'My Store')
    {
      $newloc = 'storage/mystore';
      $check_size = false;
      $that_room = 'my store';
    }
    else if($target == 'Home' || $target == 'Common')
    {
      $newloc = 'home';
      $that_room = 'the common room';
    }
    else if(strlen($house['rooms']) > 0)
    {
      $rooms = explode(',', $house['rooms']);
      if(array_search($target, $rooms) !== false)
        $newloc = 'home/' . $target;
      else
        $error_message[] = '49:' . $room_name;

      if($target{0} == '$')
        $that_room = 'the ' . substr($target, 1) . ' room';
      else
        $that_room = 'the ' . $target . ' room';
    }
    else
      $error_message[] = 50;

    if(count($error_message) == 0)
    {
      if($check_size)
      {
        if($total_size + $house['curbulk'] > $house['maxbulk'])
          $error_message[] = '10:house';
      }
      
      if(count($error_message) == 0)
      {
        foreach($items as $itemname=>$item)
        {
          if($item['remove'] == $item['total'])
            remove_all_from_library($user['idnum'], $item['itemid']);
          else
            remove_from_library($user['idnum'], $item['itemid'], $item['remove']);

          add_inventory_quantity($user['user'], '', $itemname, '', $newloc, $item['remove']);
        }

        $error_message[] = '82:' . $total_quantity . ' items to ' . $that_room;
      }
    }
  }
}

header('Location: /myhouse/addon/library.php?page=' . (int)$_GET['page'] . '&msg=' . implode(',', $error_message));
?>
