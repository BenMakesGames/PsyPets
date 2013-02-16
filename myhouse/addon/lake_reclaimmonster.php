<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$whereat = 'home';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/houselib.php';
require_once 'commons/lakelib.php';
require_once 'commons/utility.php';
require_once 'commons/petlib.php';

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

if($lake['monster'] != 'no')
{
  $monster = get_pet_byid($lake['monster']);
  
  if(strstr($monster['graphic'], '/') !== false)
  {
    $command = 'UPDATE monster_pets SET user=' . quote_smart($user['user']) . ' WHERE idnum=' . $lake['monster'] . ' LIMIT 1';
    fetch_none($command, 'reclaiming pet');
    
    $command = 'UPDATE psypets_lakes SET monster=\'no\' WHERE userid=' . $user['idnum'] . ' LIMIT 1';
    fetch_none($command, 'freeing monster from lake');
  }
}

header('Location: /myhouse/addon/lake.php');
?>