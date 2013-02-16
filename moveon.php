<?php
// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";
require_once 'commons/petlib.php';

$petid = (int)$_GET['petid'];

$deadpet = get_pet_byid($petid);

if($deadpet['user'] == $user['user'] && $deadpet['dead'] != 'no')
{
  $command = 'UPDATE monster_pets SET user=\'graveyard\' WHERE idnum=' . $petid . ' LIMIT 1';
  $database->FetchNone($command, 'deleting pet');

  $command = 'DELETE FROM psypets_pet_market WHERE petid=' . $petid . ' LIMIT 1';
  $database->FetchNone($command, 'deleting pet market listing, if one exists');

  // protected pets always get an urn
  if($deadpet['protected'] == 'no')
  {
    if(rand() % 2 == 0)
    {
      $where = 'home';

      $deadpet['movedon'] = true;

      if($deadpet['graphic'] == 'mushroom.gif')
      {
        $command = "INSERT INTO `monster_inventory` " .
                   "(`user`, `itemname`, `message`, `location`) " .
                   "VALUES " .
                   '(' . quote_smart($user['user']) . ", 'Mushroom', " . quote_smart($deadpet['petname'] . "'s remains") . ", 'home')";
      }
      else if($deadpet['graphic'] == 'broccoli.gif')
      {
        $command = 'INSERT INTO `monster_inventory` ' .
                   '(`user`, `itemname`, `message`, `location`) ' .
                   'VALUES ' .
                   '(' . quote_smart($user['user']) . ", 'Broccoli', " . quote_smart($deadpet['petname'] . "'s remains") . ", 'home')";
      }
      else if($deadpet["graphic"] == "chicken.png" || $deadpet["graphic"] == "chicken_red.png" || $deadpet["graphic"] == "chicken_blue.png" || $deadpet["graphic"] == "chickie.gif" || $deadpet["graphic"] == "rooster_not_ostrich.gif")
      {
        $command = 'INSERT INTO `monster_inventory` ' .
                   '(`user`, `itemname`, `message`, `location`) ' .
                   'VALUES ' .
                   '(' . quote_smart($user['user']) . ", 'Chicken', " . quote_smart($deadpet['petname'] . "'s remains") . ", 'home')";
      }
      else if($deadpet['graphic'] == 'ba-ha.gif')
      {
        $command = 'INSERT INTO `monster_inventory` ' .
                   '(`user`, `itemname`, `message`, `location`) ' .
                   'VALUES ' .
                   '(' . quote_smart($user['user']) . ", 'Fish', " . quote_smart($deadpet['petname'] . "'s remains") . ", 'home')";
      }
      else
      {
        $command = 'INSERT INTO `monster_inventory` ' .
                   '(`user`, `itemname`, `message`, `location`) ' .
                   'VALUES ' .
                   '(' . quote_smart($user['user']) . ", 'Urn', " . quote_smart($deadpet['petname'] . "'s ashes") . ", 'home')";
      }
    }
  }
  else
  {
    $command = 'INSERT INTO `monster_inventory` ' .
               '(`user`, `itemname`, `message`, `location`) ' .
               'VALUES ' .
               '(' . quote_smart($user['user']) . ", 'Urn', " . quote_smart($deadpet['petname'] . "'s ashes") . ", 'home')";
  }

  if(strlen($command) > 0)
  {
    $database->FetchNone($command, 'adding food item >_>');
  }

  if($deadpet['toolid'] > 0)
  {
    $command = 'UPDATE monster_inventory SET location=\'home\',changed=' . time() . ',user=' . quote_smart($user['user']) . ' WHERE idnum=' . $deadpet['toolid'] . ' LIMIT 1';
    $database->FetchNone($command, 'giving back the equipped item');
  }

  $ghost = (mt_rand(1, 512) == 1 ? 'yes' : 'no');

  $command = '
    INSERT INTO psypets_graveyard (`locid`, `ownerid`, `timestamp`, `tombstone`, `petname`, `petid`, `ghost`)
    VALUES (
      ' . $deadpet['locid'] . ',
      ' . $user['idnum'] . ',
      ' . time() . ',
      ' . ($deadpet['idnum'] % 4 + 1) . ',
      ' . quote_smart($deadpet['petname']) . ',
      ' . $petid . ',
      ' . quote_smart($ghost) . '
    )
  ';

  $database->FetchNone($command, 'adding graveyard entry');

  $id = $database->InsertID();

  $loc = 'editepitaph.php?id=' . $id;

  require_once 'commons/statlib.php';
  record_stat($user['idnum'], '"Move On"\'d a Pet', 1);

  header('Location: ./editepitaph.php?id=' . $id);
} // if the pet was indeed ours, and dead
else
  header('Location: ./myhouse.php');
?>
