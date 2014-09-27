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

$command = 'SELECT * FROM monster_pets WHERE idnum=' . $petid . ' AND user=' . quote_smart($user['user']) . ' AND location=\'home\' AND ((`str`+`bra`>=' . $require . ' AND merit_tough_hide=\'no\') OR (`str`+`bra`>=' . ($require - 1) . ' AND merit_tough_hide=\'yes\')) AND dead=\'no\' AND sleeping=\'no\' AND changed=\'no\' LIMIT 1';
$pet = fetch_single($command, 'fetching pet');

if($pet !== false)
{
  train_pet($pet, 'str', $require * 10, 0, false, true);
  train_pet($pet, 'bra', $require * 10, 0, false, true);

  save_pet($pet, array('str_count', 'bra_count'));

  $message = '<p>' . $pet['petname'] . ' defeats the foe soundly, leaving you free to continue your journey.</p>';
  $success = true;
}
?>
