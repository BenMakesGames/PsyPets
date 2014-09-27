<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/petlib.php';

require_once 'libraries/db_messages.php';

switch($this_item['itemname'])
{
  case 'Introvert':
    $stat = 'extraverted';
    $modifier = '-1';
    break;

  case 'Extrovert':
    $stat = 'extraverted';
    $modifier = '+1';
    break;

  case 'Laze':
    $stat = 'conscientious';
    $modifier = '-1';
    break;

  case 'Discipline':
    $stat = 'conscientious';
    $modifier = '+1';
    break;

  case 'Rebel':
    $stat = 'independent';
    $modifier = '+1';
    break;

  case 'Rely':
    $stat = 'independent';
    $modifier = '-1';
    break;

  case 'Experiment':
    $stat = 'open';
    $modifier = '+1';
    break;

  case 'Conform':
    $stat = 'open';
    $modifier = '-1';
    break;

  case 'Play':
    $stat = 'playful';
    $modifier = '+1';
    break;

  case 'Work':
    $stat = 'playful';
    $modifier = '-1';
    break;

  
  default:
    echo "This potion is an error! >_>  (Please report to That Guy Ben!)\n";
    exit();
}

if(count($userpets) == 0)
{
  echo "<i>You have no pet to give this to.</i>\n";
  exit();
}
else
{
  $alive = false;

  foreach($userpets as $pet)
  {
    if($pet['dead'] == 'no' && $pet['zombie'] == 'no')
    {
      $alive = true;
      break;
    }
  }

  if(!$alive)
  {
    echo "<i>You have no pet to give this to.</i>\n";
    exit();
  }
}

$petid = (int)$_POST['petid'];

if($petid > 0)
  $target_pet = get_pet_byid($petid);
else
  $target_pet = array();

if(
  $target_pet['user'] != $user['user'] ||
  $target_pet['dead'] != 'no' ||
  $target_pet['zombie'] == 'yes' ||
  ($target_pet[$stat] <= 0 && $modifier == '-1') ||
  ($target_pet[$stat] >= 10 && $modifier == '+1') ||
  $target_pet['location'] != 'home'
)
{
  echo '<p>Which pet will drink ', $this_inventory['itemname'], '?</p>';
?>
<form method="post">
<table>
 <thead>
  <tr><th></th><th></th><th colspan="2">Pet</th></tr>
 </thead>
 <tbody>
<?php
  $rowclass = begin_row_class();

  for($i = 0; $i < count($userpets); ++$i)
  {
    echo '<tr class="' . $rowclass . '">';

    if($userpets[$i]['dead'] != 'no' || $userpets[$i]['zombie'] == 'yes')
      echo '<td><input type="radio" disabled /></td><td>', pet_graphic($userpets[$i]), '</td><td class="dim">' . $userpets[$i]['petname'] . '<br /><i>Level ' . pet_level($userpets[$i]) . $extra . '</i></td><td class="failure">(is dead)</td>';
    else if(($userpets[$i][$stat] <= 0 && $modifier == '-1') || ($userpets[$i][$stat] >= 10 && $modifier == '+1'))
      echo '<td><input type="radio" disabled /></td><td>', pet_graphic($userpets[$i]), '</td><td class="dim">' . $userpets[$i]['petname'] . '<br /><i>Level ' . pet_level($userpets[$i]) . $extra . '</i></td><td class="failure">(stat is already at the minimum level)</td>';
    else
      echo '<td><input type="radio" name="petid" value="' . $userpets[$i]['idnum'] . '" /></td><td>', pet_graphic($userpets[$i]), '</td><td>' . $userpets[$i]['petname'] . '<br /><i>Level ' . pet_level($userpets[$i]) . $extra . '</i></td><td></td>';

    echo '</tr>';
    
    $rowclass = alt_row_class($rowclass);
  }
?>
 </tbody>
</table>
<p><input type="submit" name="submit" value="Give" /></p>
</form>
<?php
}
else
{
  delete_inventory_byid($this_inventory['idnum']);

  $database->FetchNone("UPDATE monster_pets SET `$stat`=`$stat`$modifier WHERE idnum=" . $target_pet['idnum'] . " LIMIT 1");

  echo '<p>', $target_pet['petname'], ' drinks it all in one gulp!</p>';

  $message = $target_pet['petname'] . ' drank ' . $this_inventory['itemname'] . '!';
  
  $database->FetchNone('
    INSERT INTO psypets_pet_level_logs
    (timestamp, petid, answer)
    VALUES
    (
      ' . time() . ',
      ' . $target_pet['idnum'] . ',
      ' . quote_smart($message) . '
    )
  ');
  add_logged_event($user['idnum'], $target_pet['idnum'], 0, 'realtime', false, $message);
  add_db_message($user['idnum'], FLASH_MESSAGE_PET_PROGRESS, $message);
}
?>
