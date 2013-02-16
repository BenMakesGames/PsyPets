<?php
if($challenge['step'] == 0)
  exit();

if($challenge['difficulty'] == 0)
{
  $require = 1;
}
else if($challenge['difficulty'] == 1)
{
  $require = 4;
}
else if($challenge['difficulty'] == 2)
{
  $require = 8;
}
else if($challenge['difficulty'] == 3)
{
  $require = 12;
}
else if($challenge['difficulty'] == 4)
{
  $require = 16;
}
else
  exit();

$petid = (int)$_POST['pet'];

$command = 'SELECT * FROM monster_pets WHERE idnum=' . $petid . ' AND user=' . quote_smart($user['user']) . ' AND location=\'home\' AND `extraverted`+`open`>=' . $require . ' AND dead=\'no\' AND sleeping=\'no\' AND changed=\'no\' LIMIT 1';
$pet = fetch_single($command, 'fetching pet');

if($pet !== false)
{

  $message = '<p>' . $pet['petname'] . ' calms the little guy down, who returns to his grateful owner.</p>' .
             '<p>"Thank you so much!" the woman exclaims.</p>';
  $success = true;
}
?>
