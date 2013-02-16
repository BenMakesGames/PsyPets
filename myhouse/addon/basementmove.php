<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$whereat = 'home';
$wiki = 'Basement';
$THIS_ROOM = 'Basement';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/checkpet.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/basementlib.php';
require_once 'commons/utility.php';

if(!addon_exists($house, 'Basement'))
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
        $itemid = substr($key, 2);
        $details = get_item_byid($itemid);

        $item = get_basement_item_byname($user['idnum'], $user['locid'], $details['itemname']);

        if($item === false)
        {
          $error_message[] = 28;
          $items = array();
          break;
        }
        else if($item['quantity'] < $quantity)
        {
          $error_message[] = '83:' . $details['itemname'];
          $items = array();
          break;
        }
        
        $items[$details['itemname']] = $quantity;
        $total_size += $details['bulk'];
        $total_quantity += $quantity;
      }
    }
  }

  if(count($items) > 0)
  {
    $target = trim($_POST['room']);

    if($target == 'Storage')
    {
      $newloc = 'storage';
      $that_room = 'storage';
    }
    else if($target == 'My Store')
    {
      $newloc = 'storage/mystore';
      $that_room = 'my store';
    }
    else if($target == 'Home' || $target == 'Common')
    {
      $newloc = 'home';
      $that_room = 'the common room';
    }
    else if($target == 'Protected')
    {
      $newloc = 'home/protected';
      $that_room = 'the protected room';
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
      foreach($items as $itemname=>$quantity)
      {
        remove_from_basement($user['idnum'], $user['locid'], $itemname, $quantity);
        add_inventory_quantity($user['user'], '', $itemname, '', $newloc, $quantity);
      }

      clean_up_basement($user['idnum']);
      recalc_basement_bulk($user['idnum'], $user['locid']);

      $error_message[] = '82:' . $total_quantity . ' items to ' . $that_room;
    }
  }
}

header('Location: /myhouse/addon/basement.php?page=' . $_GET['page'] . '&msg=' . implode(',', $error_message));
?>
