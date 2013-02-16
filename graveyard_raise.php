<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/userlib.php';
require_once 'commons/gravelib.php';
require_once 'commons/questlib.php';
require_once 'commons/petlib.php';

$tombid = (int)$_GET['id'];

$tombstone = get_tombstone_byid($tombid);

if($tombstone === false || $tombstone['ownerid'] == $user['idnum'] || $tombstone['tombstone'] == 0)
{
  header('Location: /graveyard.php');
  exit();
}

$raise_quest = get_quest_value($user['idnum'], 'raise zombie');
if($raise_quest !== false)
  $raise_one = ($raise_quest['value'] > 0);
else
  $raise_one = false;

if($raise_one)
{
  update_quest_value($raise_quest['idnum'], 0);

  $petid = create_random_pet($user['user']);
  
  $str = mt_rand(2, 4);
  $sta = mt_rand(0, 2);
  $athletics = mt_rand(1, 3);
  $sur = mt_rand(0, 2);
  $bra = mt_rand(3, 5);
  
  $size = pet_size(array('str' => $str, 'sta' => $sta, 'athletics' => $athletics, 'sur' => $sur, 'bra' => $bra));
  
  $database->FetchNone('
    UPDATE monster_pets
    SET
      petname=' . quote_smart($tombstone['petname']) . ',
      gender=\'male\',prolific=\'no\',zombie=\'yes\',
      energy=12,food=-1,safety=10,love=10,esteem=10,size=' .  $size . ',
      `str`=' . $str . ',`dex`=0,`sta`=' . $sta . ',`per`=0,`int`=0,`wit`=0,
      `bra`=' . $bra . ',`athletics`=' . $athletics . ',`stealth`=0,`sur`=' . $sur . ',`cra`=0,`eng`=0,`smi`=0
    WHERE idnum=' . $petid . '
    LIMIT 1
  ');

  $database->FetchNone('UPDATE psypets_graveyard SET tombstone=0 WHERE idnum=' . $tombid . ' LIMIT 1');

  $badges = get_badges_byuserid($user['idnum']);
  if($badges['zombielord'] == 'no')
    set_badge($user['idnum'], 'zombielord');

  header('Location: /myhouse.php');
}
else
  header('Location: /graveyard.php');
