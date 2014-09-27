<?php
if($challenge['step'] == 0)
  exit();

// SELECT AVG(level),(str+eng) AS repair FROM monster_pets GROUP BY repair ORDER BY repair DESC

if($challenge['difficulty'] == 0)
{
  $require = 1;
  $extra_note = ' - some nearby branches could be used to repair it in no time!';
}
else if($challenge['difficulty'] == 1)
{
  $require = 6;
  $extra_note = '.';
}
else if($challenge['difficulty'] == 2)
{
  $require = 10;
  $extra_note = ', but the river rushing below will not make the task any easier.';
}
else if($challenge['difficulty'] == 3)
{
  $require = 14;
  $extra_note = ', but the river rushing below coupled with high winds will make it a challenge, to say the least.';
}
else if($challenge['difficulty'] == 4)
{
  $require = 18;
  $extra_note = ', but the river rushing below, high winds, and aggressive wildlife will make it an exceedingly difficult task.';
}
else
  exit();

$command = 'SELECT idnum,petname,dead,changed,sleeping FROM monster_pets WHERE user=' . quote_smart($user['user']) . ' AND location=\'home\' AND `str`+`eng`>=' . $require;
$pets = fetch_multiple($command, 'fetching pets');
?>
<p>A ravine blocks your path, and the rickety plank bridge that once spanned it is now broken, each half hanging limply on either side.  Repairing it looks like the only way<?= $extra_note ?></p>
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

  echo '</select> <input type="submit" value="Repair" /></p>' .
       '</form>';
}
?>
