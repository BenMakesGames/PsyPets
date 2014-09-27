<?php
require_once 'commons/init.php';

$whereat = 'home';
$wiki = 'Lake';
$THIS_ROOM = 'Lake';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/houselib.php';
require_once 'commons/lakelib.php';

if(!addon_exists($house, 'Lake'))
{
  header('Location: /myhouse.php');
  exit();
}

$lake = get_lake_byuser($user['idnum']);
if($lake === false)
{
  header('Location: /myhouse/addon/lake.php');
  exit();
}

$boats = take_apart(',', $lake['boats']);
$num_boats = count($boats);

$boatid = (int)$_GET['i'];

if($boatid < 1 || $boatid > $num_boats)
{
  header('Location: /myhouse/addon/lake.php');
  exit();
}
else
  $boat_index = $boatid - 1;

$boat_name = $boats[$boat_index];
unset($boats[$boat_index]);

$command = 'UPDATE psypets_lakes SET boats=' . quote_smart(implode(',', $boats)) . ' WHERE userid=' . $user['idnum'] . ' LIMIT 1';
fetch_none($command, 'removing boat');

add_inventory($user['user'], '', $boat_name, '', 'home');

header('Location: /myhouse/addon/lake.php');
exit();
?>