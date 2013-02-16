<?php
if($challenge['step'] == 0)
  exit();

if($challenge['difficulty'] == 0)
{
  $require = 1;
  $monster = 'A Gelatinous Cube rolled over my poor pet, covering him with goo!';
}
else if($challenge['difficulty'] == 1)
{
  $require = 4;
  $monster = 'An Earth Golem stomped through the area, startling my poor pet!';
}
else if($challenge['difficulty'] == 2)
{
  $require = 8;
  $monster = 'A Wizened Ent tore through the area, scaring my poor pet!';
}
else if($challenge['difficulty'] == 3)
{
  $require = 12;
  $monster = 'A Robot Monkey careened through the area, sending my pet mad with fright!';
}
else if($challenge['difficulty'] == 4)
{
  $require = 16;
  $monster = 'An Abandondero rampaged through the area, terrifying my poor pet out of his mind!';
}
else
  exit();

$command = 'SELECT idnum,petname,dead,changed,sleeping FROM monster_pets WHERE user=' . quote_smart($user['user']) . ' AND location=\'home\' AND `extraverted`+`open`>=' . $require;
$pets = fetch_multiple($command, 'fetching pets');
?>
<p>You encounter a worried-looking woman...</p>
<p>"Oh, something awful has happened!  <?= $monster ?>  Do you think you could help me calm him down?"</p>
<p><i>(Only pets that are able to perform this feat successfully are listed here.  Dead, sleeping, or were-form pets cannot participate, but will still be listed.)</i></p>
<?php
if(count($pets) > 0)
{
  echo '<form action="?action=go" method="post">' .
       '<p><select name="pet">';

  foreach($pets as $pet)
  {
    if($pet['sleeping'] == 'yes' || $pet['changed'] == 'yes' || $pet['dead'] != 'no')
      $disabled = ' disabled';
    else
      $disabled = '';

    echo '<option value="' . $pet['idnum'] . '"' . $disabled . '>' . $pet['petname'] . '</option>';
  }

  echo '</select> <input type="submit" value="Assist" /></p>' .
       '</form>';
}
?>
