<?php
if($challenge['step'] == 0)
  exit();

if($challenge['difficulty'] == 0)
{
  $require = 1;
}
else if($challenge['difficulty'] == 1)
{
  $require = 6;
}
else if($challenge['difficulty'] == 2)
{
  $require = 10;
}
else if($challenge['difficulty'] == 3)
{
  $require = 14;
}
else if($challenge['difficulty'] == 4)
{
  $require = 18;
}
else
  exit();

$petid = (int)$_POST['pet'];

$command = 'SELECT * FROM monster_pets WHERE idnum=' . $petid . ' AND user=' . quote_smart($user['user']) . ' AND location=\'home\' AND `str`+`athletics`>=' . $require . ' AND dead=\'no\' AND sleeping=\'no\' AND changed=\'no\' LIMIT 1';
$pet = fetch_single($command, 'fetching pet');

if($pet !== false)
{
  train_pet($pet, 'str', $require * 10, 0, false, true);
  train_pet($pet, 'athletics', $require * 10, 0, false, true);

  save_pet($pet, array('str_count', 'athletics_count'));

  $message = '<p>' . $pet['petname'] . ' leaps across with such skill that the Rogue Broccoli drops the stolen goods, distracting ' . t_pronoun($pet['gender']) . ' and making its getaway.  You got your stuff back, anyway; that\'s the important part.</p>';
  $success = true;
}
?>
