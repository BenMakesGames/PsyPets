<?php
if($challenge['step'] == 0)
  exit();

// SELECT AVG(level),(dex+ste) AS sneaky FROM monster_pets GROUP BY sneaky ORDER BY sneaky DESC

if($challenge['difficulty'] == 0)
{
  $require = 1;
  $monster = 'A squirrel';
}
else if($challenge['difficulty'] == 1)
{
  $require = 6;
  $monster = 'A triskaidekaphobic baker';
}
else if($challenge['difficulty'] == 2)
{
  $require = 10;
  $monster = 'A goth';
}
else if($challenge['difficulty'] == 3)
{
  $require = 14;
  $monster = 'A ManBearpig';
}
else if($challenge['difficulty'] == 4)
{
  $require = 18;
  $monster = 'A lost viking';
}
else
  exit();

$command = 'SELECT idnum,petname,dead,changed,sleeping FROM monster_pets WHERE user=' . quote_smart($user['user']) . ' AND location=\'home\' AND `dex`+`stealth`>=' . $require;
$pets = fetch_multiple($command, 'fetching pets');
?>
<p><?= $monster ?> sentinel guards the area.  If it becomes aware of you, it will certainly alert the others.  The only safe way past it will be to sneak...</p>
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

  echo '</select> <input type="submit" value="Sneak" /></p>' .
       '</form>';
}
?>
