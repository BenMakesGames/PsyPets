<?php
if($challenge['step'] == 0)
  exit();

// SELECT AVG(level),(`int`+eng) AS disarm FROM monster_pets GROUP BY disarm ORDER BY disarm DESC

if($challenge['difficulty'] == 0)
{
  $require = 1;
  $trap = 'simple, and you don\'t think it';
}
else if($challenge['difficulty'] == 1)
{
  $require = 6;
  $trap = 'simple, but';
}
else if($challenge['difficulty'] == 2)
{
  $require = 10;
  $trap = 'somewhat complex, and';
}
else if($challenge['difficulty'] == 3)
{
  $require = 14;
  $trap = 'very complex, and';
}
else if($challenge['difficulty'] == 4)
{
  $require = 18;
  $trap = 'fiendish in its complexity, and';
}
else
  exit();

$command = 'SELECT idnum,petname,dead,changed,sleeping FROM monster_pets WHERE user=' . quote_smart($user['user']) . ' AND location=\'home\' AND ((`int`+`eng`>=' . $require . ' AND merit_steady_hands=\'no\') OR (`int`+`eng`>=' . ($require - 1) . ' AND merit_steady_hands=\'yes\'))';
$pets = fetch_multiple($command, 'fetching pets');
?>
<p>While traveling through a cave you encounter a trap.  It's <?= $trap ?> would be too dangerous to set off.  Disarming it would be the best approach, at any rate.</p>
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

  echo '</select> <input type="submit" value="Disarm" /></p>' .
       '</form>';
}
?>
