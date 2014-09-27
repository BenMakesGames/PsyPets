<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/petlib.php';

require_once 'libraries/db_messages.php';

$lose_level = true;

if($this_item['itemname'] == 'Forget')
  $stat = 'int';
else if($this_item['itemname'] == 'Atrophy')
  $stat = 'str';
else if($this_item['itemname'] == 'Stumble')
  $stat = 'dex';
else if($this_item['itemname'] == 'Cloud')
  $stat = 'wit';
else if($this_item['itemname'] == 'Blur')
  $stat = 'per';
else if($this_item['itemname'] == 'Waste')
  $stat = 'sta';

else if($this_item['itemname'] == 'Pacify')
  $stat = 'bra';
else if($this_item['itemname'] == 'Quit')
  $stat = 'athletics';
else if($this_item['itemname'] == 'Expose')
  $stat = 'stealth';
else if($this_item['itemname'] == 'Domesticate')
  $stat = 'sur';
else if($this_item['itemname'] == 'Urbanize')
  $stat = 'gathering';
else if($this_item['itemname'] == 'Aquaphobia')
  $stat = 'fishing';
else if($this_item['itemname'] == 'Collapse')
  $stat = 'mining';
else if($this_item['itemname'] == 'Hackney')
  $stat = 'cra';
else if($this_item['itemname'] == 'Smear')
  $stat = 'painting';
else if($this_item['itemname'] == 'Mold')
  $stat = 'carpentry';
else if($this_item['itemname'] == 'Dull')
  $stat = 'jeweling';
else if($this_item['itemname'] == 'Chip')
  $stat = 'sculpting';
else if($this_item['itemname'] == 'Static')
  $stat = 'eng';
else if($this_item['itemname'] == 'Entropy')
  $stat = 'mechanics';
else if($this_item['itemname'] == 'Dilute')
  $stat = 'chemistry';
else if($this_item['itemname'] == 'Rust')
  $stat = 'smi';
else if($this_item['itemname'] == 'Fray')
  $stat = 'tai';
else if($this_item['itemname'] == 'Paradox')
  $stat = 'binding';
else if($this_item['itemname'] == 'Crash')
  $stat = 'pil';

else
{
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

if($target_pet['user'] != $user['user'] || $target_pet['dead'] != 'no' || $target_pet['zombie'] == 'yes' || $target_pet[$stat] < 1 || $target_pet['location'] != 'home')
{
  echo '<p>Which pet will drink ', $this_inventory['itemname'], '?</p>';

  if($lose_level)
    echo '<p>Note: Pets that drink this potion will have to re-earn the option to reincarnate, should they have already earned it.</p>';
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
    if($userpets[$i]['ascend'] == 'yes')
      $extra = ' <img src="' . $SETTINGS['protocol'] . '://saffron.psypets.net/gfx/ascend.png" class="inline" width="16" height="16" alt="(pet can reincarnate)" />';
    else
      $extra = '';

    echo '<tr class="' . $rowclass . '">';

    if($userpets[$i]['dead'] != 'no' || $userpets[$i]['zombie'] == 'yes')
      echo '<td><input type="radio" disabled /></td><td>', pet_graphic($userpets[$i]), '</td><td class="dim">' . $userpets[$i]['petname'] . '<br /><i>Level ' . pet_level($userpets[$i]) . $extra . '</i></td><td class="failure">(is dead)</td>';
    else if($userpets[$i][$stat] < 1)
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

  if($lose_level)
  {
    $sets = array(
      '`' . $stat . '`=`' . $stat . '`-1',
      'ascend=\'no\''
    );

    foreach($ASCEND_STATS as $ascend_stat)
      $sets[] = '`' . $ascend_stat . '`=\'no\'';
  
    $database->FetchNone('
      UPDATE monster_pets
      SET ' . implode(',', $sets) . '
      WHERE idnum=' . $target_pet['idnum'] . '
      LIMIT 1
    ');
  }
  else
    $database->FetchNone("UPDATE monster_pets SET `$stat`=`$stat`-1 WHERE idnum=" . $target_pet['idnum'] . " LIMIT 1");

  echo '<p>', $target_pet['petname'], ' drinks the entire potion, then hiccups, releasing a small bubble that floats away.</p>';

  $message = $target_pet['petname'] . ' drank a strange potion, and lost a level of ' . ucfirst($PET_STAT_DESCRIPTIONS[$stat]) . '!';
  
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
