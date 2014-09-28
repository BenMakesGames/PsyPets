<?php
if($okay_to_be_here !== true)
  exit();

$data = $this_inventory["data"];

if(strlen($data) == 0)
{
  $data = mt_rand(1, 2);

  $command = "UPDATE monster_inventory SET data='$data' WHERE idnum=" . $this_inventory['idnum'] . " LIMIT 1";
  $database->FetchNone($command, 'item: ' . $this_inventory['itemname']);
}
else
{
  $data--;

  if($data >= 0)
  {
    $command = "UPDATE monster_inventory SET data='$data' WHERE idnum=" . $this_inventory['idnum'] . " LIMIT 1";
    $database->FetchNone($command, 'item: ' . $this_inventory['itemname']);
  }
}

$AGAIN_WITH_SAME = true;

if($data <= 0)
{
  $message = 'You reach inside the bag, but nothing is there.';
  $AGAIN_WITH_SAME = false;
}
else
{
  $i = mt_rand(1, 10);
  $petid = false;

  $message = 'You reach inside the bag and pull out a little ball of Fluff.  ';
  if($i == 1) // summons a sootie
  {
    $message .= 'It unwraps, revealing itself as a small Black Bat.</p><p>It flutters around uselessly for a while before finally escaping through an open window.';
  }
  else if($i == 2)
  {
    $message .= 'It rolls around in your hand, then falls to the ground.</p><p>How cute!  It\'s a little pet Sooty!';

    $petid = create_random_pet($user['user']);

    $command = 'UPDATE monster_pets SET graphic=\'sooty.gif\' WHERE idnum=' . $petid . ' LIMIT 1';
    $database->FetchNone($command, 'item: ' . $this_inventory['itemname']);
  }
  else if($i == 3)
  {
    $message .= 'It unfurls, revealing itself as a Tiny Elephant!';

    $petid = create_random_pet($user['user']);

    $command = 'UPDATE monster_pets SET graphic=\'mtelephant.png\' WHERE idnum=' . $petid . ' LIMIT 1';
    $database->FetchNone($command, 'item: ' . $this_inventory['itemname']);
  }
  else if($i == 3)
  {
    $message .= 'It turns around and looks at you.</p><p>It\'s a ball of Cotton Candy!';

    $petid = create_random_pet($user['user']);

    $command = 'UPDATE monster_pets SET graphic=\'cottoncandy.png\' WHERE idnum=' . $petid . ' LIMIT 1';
    $database->FetchNone($command, 'item: ' . $this_inventory['itemname']);
  }
  else if($i == 4)
  {
    $message .= 'It shakes, sending Fluff everywhere, and revealing itself as a tiny pet Rock.';

    $petid = create_random_pet($user['user']);

    $command = 'UPDATE monster_pets SET graphic=\'rock.gif\' WHERE idnum=' . $petid . ' LIMIT 1';
    $database->FetchNone($command, 'item: ' . $this_inventory['itemname']);
    
    $a = mt_rand(1, 4) - 1;
    for($i = 0; $i < $a; ++$i)
      add_inventory($user['user'], 'p:' . $petid, 'Fluff', 'Shed from a baby pet rock.', $this_inventory['location']);
  }
  else if($i == 5)
  {
    $message .= 'It rolls onto the floor, revealing itself as a Fuzzy Eel, which squirms around the room a little before escaping through a hole in the wall.';
  }
  else if($i == 6)
  {
    $message .= 'It turns around, revealing itself as a Chick.';

    $petid = create_random_pet($user['user']);

    $command = 'UPDATE monster_pets SET graphic=\'chickie.gif\' WHERE idnum=' . $petid . ' LIMIT 1';
    $database->FetchNone($command, 'item: ' . $this_inventory['itemname']);
  }
  else if($i == 7)
  {
    $message .= 'A gust of wind from an ajar door steals it from you.';
  }
  else if($i == 8)
  {
    add_inventory($user['user'], '', 'Fluff', 'Pulled out of a ' . $this_inventory['itemname'], $this_inventory['location']);
  }
  else if($i == 9)
  {
    $message .= 'Two little ears poke out from the fluff.  Eeee!  It\'s a Tree Mouse!';

    $petid = create_random_pet($user['user']);

    $command = 'UPDATE monster_pets SET graphic=\'treemouse.png\' WHERE idnum=' . $petid . ' LIMIT 1';
    $database->FetchNone($command, 'item: ' . $this_inventory['itemname']);
  }
  else if($i == 10)
  {
    $message .= 'It props itself up on two legs, revealing itself as an Ambling Eye.';

    $petid = create_random_pet($user['user']);

    $command = 'UPDATE monster_pets SET graphic=\'eyeonlegs.png\' WHERE idnum=' . $petid . ' LIMIT 1';
    $database->FetchNone($command, 'item: ' . $this_inventory['itemname']);
  }
  
  if($petid !== false)
  {
    $level = mt_rand(1, 5);
    
    $initial_stats = array();
    $updates = array();

    for($i = 0; $i < $level; ++$i)
      $initial_stats[$PET_SKILLS[array_rand($PET_SKILLS)]]++;

    foreach($initial_stats as $stat=>$score)
      $updates[] = '`' . $stat . '`=' . $score;

    $database->FetchNone('UPDATE monster_pets SET ' . implode(',', $updates) . ' WHERE idnum=' . $petid . ' LIMIT 1');
  }
}

echo '<p>', $message, '</p>';
?>
