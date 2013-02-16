<?php
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/questlib.php';

$today = date('M j, Y');

if($today == 'Dec 1, 2008')
{
  if($user['idnum'] <= 34789)
  {
    $dec1_2008 = get_quest_value($user['idnum'], 'December 1st, 2008');
    if($dec1_2008 === false)
    {
      add_inventory($user['user'], '', 'December 1st, 2008', 'In celebration of a rare celestial event!', 'storage/incoming');
      flag_new_incoming_items($user['user']);
      add_quest_value($user['idnum'], 'December 1st, 2008', 1);
      header('Location: ./incoming.php');
    }
    else
      header('Location: ./incoming.php?msg=125');
  }
  else
    header('Location: ./incoming.php?msg=124');
}
else
  header('Location: ./404.php');
?>
