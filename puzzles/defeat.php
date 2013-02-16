<?php
if($challenge['step'] == 0)
  exit();

// SELECT AVG(level),(str+bra) AS pwn FROM monster_pets GROUP BY pwn ORDER BY pwn DESC

if($challenge['difficulty'] == 0)
{
  $require = 1;
  $monster = 'An intimidating gecko';
}
else if($challenge['difficulty'] == 1)
{
  $require = 6;
  $monster = 'A tiny dragon';
}
else if($challenge['difficulty'] == 2)
{
  $require = 10;
  $monster = 'A small dragon';
}
else if($challenge['difficulty'] == 3)
{
  $require = 14;
  $monster = 'A dragon';
}
else if($challenge['difficulty'] == 4)
{
  $require = 18;
  $monster = 'A big dragon';
}
else
  exit();

$command = 'SELECT idnum,petname,dead,changed,sleeping FROM monster_pets WHERE user=' . quote_smart($user['user']) . ' AND location=\'home\' AND ((`str`+`bra`>=' . $require . ' AND merit_tough_hide=\'no\') OR (`str`+`bra`>=' . ($require - 1) . ' AND merit_tough_hide=\'yes\'))';
$pets = fetch_multiple($command, 'fetching pets');
?>
<p><?= $monster ?> blocks your path and refuses to stand down.  It seems a head-on fight is the only option...</p>
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

  echo '</select> <input type="submit" value="Fight" /></p>' .
       '</form>';
}
?>
