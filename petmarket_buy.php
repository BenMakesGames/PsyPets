<?php
$whereat = 'petmarket';
$wiki = 'Pet_Market';

require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';

if($NO_PVP)
{
  header('Location: /lostdata.php');
  exit();
}

if($user['breeder'] != 'yes')
{
  header('Location: /breederslicense.php?dialog=2');
  exit();
}

$listid = (int)$_GET['id'];

$command = 'SELECT * FROM psypets_pet_market WHERE idnum=' . $listid . ' AND expiration>' . $now . ' LIMIT 1';
$listing = $database->FetchSingle($command, 'fetching pet market listing');

if($listing === false)
{
  header('Location: /petmarket.php?msg=105');
  exit();
}

if($listing['ownerid'] == $user['idnum'])
{
  header('Location: /petmarket.php');
  exit();
}

if($listing['price'] > $user['money'])
{
  header('Location: /petmarket.php?msg=22');
  exit();
}

$pet = get_pet_byid($listing['petid']);
$owner = get_user_byid($listing['ownerid'], 'idnum,user,display');

if($owner === false || $pet === false || $pet['user'] != $owner['user'])
{
  header('Location: /petmarket.php?msg=106');
  exit();
}

$command = 'DELETE FROM psypets_pet_market WHERE idnum=' . $listid . ' LIMIT 1';
$database->FetchNone($command, 'removing listing');

take_money($user, $listing['price'], 'Bought ' . $pet['petname'] . ' (pet) from {r ' . $owner['display'] . '}');
give_money($owner, $listing['price'], 'Sold ' . $pet['petname'] . ' (pet) to {r ' . $user['display'] . '}');

flag_madesale($owner['idnum']);

if(pet_level($pet) > 5)
  $extra = ',original=\'no\'';
else
  $extra = '';

if($pet['toolid'] > 0)
{
  $command = 'UPDATE monster_inventory SET location=\'home\',user=' . quote_smart($owner['user']) . ',changed=' . $now . ' WHERE idnum=' . $pet['toolid'] . ' LIMIT 1';
  $database->FetchNone($command, 'unequipping pet (tool)');
}

if($pet['keyid'] > 0)
{
  $command = 'UPDATE monster_inventory SET location=\'home\',user=' . quote_smart($owner['user']) . ',changed=' . $now . ' WHERE idnum=' . $pet['keyid'] . ' LIMIT 1';
  $database->FetchNone($command, 'unequipping pet (key)');
}

$command = 'UPDATE monster_pets SET user=' . quote_smart($user['user']) . $extra . ',location=\'home\',toolid=0,keyid=0,orderid=0 WHERE idnum=' . $listing['petid'] . ' LIMIT 1';
$database->FetchNone($command, 'trasferring pet');

require_once 'commons/statlib.php';
record_stat($user['idnum'], 'Bought a Pet from the Pet Market', 1);
record_stat($owner['idnum'], 'Sold a Pet at the Pet Market', 1);

header('Location: /myhouse.php');
?>
