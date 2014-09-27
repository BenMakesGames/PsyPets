<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/questlib.php';
require_once 'commons/dreidellib.php';

$twelve_days = get_quest_value($user['idnum'], '12 days of christmas');

if($twelve_days === false)
{
  add_quest_value($user['idnum'], '12 days of christmas', 0);
  $twelve_days = get_quest_value($user['idnum'], '12 days of christmas');
  
  if($twelve_days === false)
    die('horrible error: unable to start the 12 days of christmas quest; a database error?  this should only happen if PsyPets is down entirely');
}

list($data_day, $data_month) = explode('-', $this_inventory['data']);

if($data_day == $now_day && $data_month == $now_month)
{
  echo '<p>The Partridge stands alert, as if waiting for something...</p>';
}
else if($twelve_days['value'] != ($now_month . $now_day) && (($now_month == 12 && $now_day >= 25) || ($now_month == 1 && $now_day <= 6)))
{
  update_quest_value($twelve_days['idnum'], ($now_month . $now_day));

  $command = 'UPDATE monster_inventory SET data=\'' . $now_day . '-' . $now_month . '\' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating item song');

  if($now_month == 12 && $now_day >= 25)
    $day_of_christmas = $now_day - 24;
  else if($now_month == 1 && $now_day <= 5)
    $day_of_christmas = $now_day + 7;

  switch($day_of_christmas)
  {
    case 1:
      $words = 'A Partridge In a Pear Tree';
      break;
    case 2:
      $words = 'Two Turtle Doves';
      add_inventory_quantity($user['user'], '', 'Turtle Dove', '<em>Someone</em> gave this to you...', $this_inventory['location'], 2);
      break;
    case 3:
      $words = 'Three french hens';

      $hen_ids[] = create_random_pet($user['user']);
      $hen_ids[] = create_random_pet($user['user']);
      $hen_ids[] = create_random_pet($user['user']);

      $command = 'UPDATE monster_pets SET graphic=\'chicken_red.png\',gender=\'female\' WHERE idnum IN (' . implode(',', $hen_ids) . ') LIMIT 3';
      $database->FetchNone($command, 'frenching hens');

      break;
    case 4:
      $words = 'Four calling birds';
      add_inventory($user['user'], '', 'The Writ of Chaos: The Forging of Chaos', '<em>Someone</em> gave this to you...', $this_inventory['location']);
      add_inventory($user['user'], '', 'The Writ of Chaos: The Semblances on Earth', '<em>Someone</em> gave this to you...', $this_inventory['location']);
      add_inventory($user['user'], '', 'The Writ of Chaos: The Whole of Creation', '<em>Someone</em> gave this to you...', $this_inventory['location']);
      add_inventory($user['user'], '', 'The Writ of Chaos: The Wills of Change and Consistency in Humans', '<em>Someone</em> gave this to you...', $this_inventory['location']);
      break;
    case 5:
      $words = 'Five golden rings';
      add_inventory_quantity($user['user'], '', 'Gold Ring', '<em>Someone</em> gave this to you...', $this_inventory['location'], 5);
      break;
    case 6:
      $words = 'Six geese a laying';
      add_inventory_quantity($user['user'], '', 'Egg', '<em>Someone</em> gave this to you...', $this_inventory['location'], 6);
      break;
    case 7:
      $words = 'Seven swans a swimming';
      add_inventory_quantity($user['user'], '', 'Swan Boat Blueprint', '<em>Someone</em> gave this to you...', $this_inventory['location'], 7);
      break;
    case 8:
      $words = 'Eight maids a-milking';
      add_inventory_quantity($user['user'], '', 'Raw Milk', '<em>Someone</em> gave this to you...', $this_inventory['location'], 8);
      break;
    case 9:
      $words = 'Nine ladies dancing';
      add_inventory_quantity($user['user'], '', 'Feather Boa', '<em>Someone</em> gave this to you...', $this_inventory['location'], 9);
      break;
    case 10:
      $words = 'Ten lords a-leaping';
      add_inventory_quantity($user['user'], '', 'Staff', '<em>Someone</em> gave this to you...', $this_inventory['location'], 10);
      break;
    case 11:
      $words = 'Eleven pipers piping';
      add_inventory_quantity($user['user'], '', 'Bagpipes', '<em>Someone</em> gave this to you...', $this_inventory['location'], 11);
      break;
    case 12:
      $words = 'Twelve drummers drumming';
      add_inventory_quantity($user['user'], '', 'Puniu', '<em>Someone</em> gave this to you...', $this_inventory['location'], 12);
      break;
    default:
      die('Logical error!  Today is not a day of Christmas!');
  }
  
  echo '<p><i>The Partridge sings...</i></p><p><img src="gfx/partridge.png" /> "' . $words . '!"</p>';
}
else
{
  $command = 'UPDATE monster_inventory SET data=\'' . $now_day . '-' . $now_month . '\' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating item song');

  echo '<p>While the Partridge isn\'t paying attention, you sneak up and grab a pear...</p>';

  add_inventory($user['user'], '', 'Bartlett Pear', $user['display'] . ' harvested this from a Pear Tree', $this_inventory['location']);
}
?>