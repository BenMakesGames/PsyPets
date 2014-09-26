<?php
if($okay_to_be_here !== true)
  exit();

require_once "commons/petlib.php";

if(count($userpets) == 0)
{
  echo "<i>You have no pet to use this on.</i>\n";
}

if($_POST['petid'] > 0 && (int)$_POST['petid'] == $_POST['petid'])
  $target_pet = get_pet_byid($_POST['petid']);
else
  $target_pet = array();

if($target_pet['user'] != $user['user'] || $target_pet['level'] > 2 || $target_pet['energy'] < 1 || $target_pet['location'] != 'home')
{
?>
 <p>Which pet will you use the <?= $this_item['itemname'] ?> on?  (Only level-1 and 2 pets who are not exhausted may be chosen.)</p>
 <form action="itemaction.php?idnum=<?= $_GET['idnum'] ?>" method="post">
<?php
  $any_pets = false;

  for($i = 0; $i < count($userpets); ++$i)
  {
    if($userpets[$i]['level'] <= 2 && $userpets[$i]['energy'] > 0)
    {
      if(!$any_pets)
      {
        echo '<p><select name="petid">';
        $any_pets = true;
      }

      echo "   <option value=\"" . $userpets[$i]["idnum"] . "\">" . $userpets[$i]["petname"] . "</option>\n";
    }
  }

  if($any_pets)
    echo '</select>&nbsp;<input type="submit" name="submit" value="Whirr!" /></p>';
  else
    echo '<p class="failure">You do not have any level-1 or 2 pets!</p>';
?>
 </form>
<?php
}
else
{
  $stats = array('str', 'dex', 'sta', 'per', 'int', 'wit', 'bra', 'athletics', 'stealth', 'sur', 'cra', 'eng', 'smi', 'tai', 'leather', 'binding', 'pil', 'music', 'astronomy');

  $total = 0;

  foreach($stats as $stat)
  {
    $total += $target_pet[$stat];
    $target_pet[$stat] = 0;
  }

  echo '<p>The device comes to life, spinning up to top speed almost immediately before sending orange bolts of electricity to wrap around ' . $target_pet['petname'] . '\'s head.';

  $sets = array();

  if($target_pet['sleeping'] == 'yes')
  {
    echo ' (And you can bet that wakes ' . t_pronoun($target_pet['gender']) . ' up!)';
    $sets[] = 'sleeping=\'no\'';
  }

  $sets[] = 'energy=energy-2';

  echo '</p>';

  if($total >= 19)
  {
    $descriptions = array('Hairs stand on end.', 'Windows whine.', 'Lights flicker, wondering if they\'ll survive the ordeal.',
      'Outlets spark, almost ominously.', 'A couple loose moneys coins escape from your pocket onto the floor.',
      'The kitchen microwave turns itself on.', 'Your alarm clock comes to believe that it\'s 12:00, and starts blinking the information furiously.',
      'A car alarm outside goes off.', 'You briefly become aware of every single floating particle of dust in the house as they freeze in place, midair.');

    $dramas = array_rand($descriptions, 3);
    
    foreach($dramas as $drama)
      echo '<p>' . $descriptions[$drama] . '</p>';
  
    if(mt_rand(1, 6) == 1)
      $total--;

    while($total > 0)
    {
      $stat = $stats[array_rand($stats)];

      if($target_pet[$stat] > 0)
        $stat = $stats[array_rand($stats)];

      $target_pet[$stat]++;
      $total--;
    }
    
    foreach($stats as $stat)
      $sets[] = '`' . $stat . '`=' . $target_pet[$stat];

    $command = 'UPDATE monster_pets SET ' . implode(',', $sets) . ' WHERE idnum=' . $target_pet['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'scrambling pet #' . $target_pet['idnum']);
    
    echo '<p>And then it\'s over.  The ' . $this_item['itemname'] . ' spins down, and ' . $target_pet['petname'] . ' twitches once before moving on as if nothing happened.</p>';
  }
  else
    echo '<p>But before things can really get going, everything stops.  The orange bolts take to the air, deflected, and vanish.  The ' . $this_item['itemname'] . ' spins down.</p>' .
         '<p>Apparently ' . $target_pet['petname'] . ' is immune to the effects of the ' . $this_item['itemname'] . '...</p>';

  $AGAIN_WITH_SAME = true;
}
?>
