<?php
if($challenge['step'] == 0)
  exit();

// SELECT AVG(level),(str+ath) AS jump FROM monster_pets GROUP BY jump ORDER BY jump DESC

if($challenge['difficulty'] == 0)
{
  $require = 1;
  $obstacle = 'burbling brook';
}
else if($challenge['difficulty'] == 1)
{
  $require = 6;
  $obstacle = 'narrow stream';
}
else if($challenge['difficulty'] == 2)
{
  $require = 10;
  $obstacle = 'stream';
}
else if($challenge['difficulty'] == 3)
{
  $require = 14;
  $obstacle = 'river';
}
else if($challenge['difficulty'] == 4)
{
  $require = 18;
  $obstacle = 'wide river';
}
else
  exit();

$command = 'SELECT idnum,petname,dead,changed,sleeping FROM monster_pets WHERE user=' . quote_smart($user['user']) . ' AND location=\'home\' AND `str`+`athletics`>=' . $require;
$pets = fetch_multiple($command, 'fetching pets');
?>
<p>While navigating through some woods, a Rogue Broccoli ambushes you, steals your map and compass, and jumps over a nearby <?= $obstacle ?> in order to escape.  The Rogue Broccoli will be quickly out of sight if the pet you send after it is not able to likewise clear the <?= $obstacle ?>...</p>
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

  echo '</select> <input type="submit" value="Give Chase" /></p>' .
       '</form>';
}
?>
