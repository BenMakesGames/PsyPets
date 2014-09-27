<?php
require_once 'commons/init.php';

$require_petload = 'no';

require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/houselib.php';
require_once 'commons/petblurb.php';

$petid = (int)$_GET['petid'];
$numpets = (int)$_GET['numpets'];

$pet = get_pet_byid($petid);

if($pet['user'] != $user['user'])
  die('There is no such pet.');
else
{
  $loveoptions = array();

  if($now - $pet['last_love'] > 30 * 60 || $pet['last_love_by'] != $user['idnum'])
  {
    require_once 'commons/adventurelib.php';
    require_once 'commons/love.php';
    require_once 'commons/inventory.php';

    if(strlen($house['curroom']) > 0)
      $room = 'home/' . $house['curroom'];
    else
      $room = 'home';

    $inventory = get_room_inventory($user['user'], $room, false, false, false, false);

    $loveoptions = love_options($inventory);

    if(addon_exists($house, 'Refreshing Spring'))
      $loveoptions[-4] = 'Drink from Refreshing Spring';

    if(addon_exists($house, 'Lake'))
    {
      if($now_month == 12 || $now_month == 1)
        $loveoptions[-5] = 'Ice Skate on Lake';
      else
        $loveoptions[-5] = 'Play in Lake';
    }

    $adventure = get_adventure($user['idnum']);
    if($adventure !== false && $adventure['progress'] < $adventure['difficulty'])
      $loveoptions[-1000] = 'Adventure!';
  }

  pet_blurb($user, $house, 0, $numpets, $pet, $loveoptions);
}
?>