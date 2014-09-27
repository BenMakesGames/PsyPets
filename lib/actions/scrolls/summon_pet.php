<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/petlib.php';
  
$AGAIN_WITH_ANOTHER = true;

delete_inventory_byid($_GET['idnum']);

$a = mt_rand(1, 7);
$m = mt_rand(1, 20);
  
if($_GET['testvalue'] > 0)
{
  $m = 20;
  $a = $_GET['testvalue'];
}

$destination = $this_inventory['location'];

if(substr($destination, 0, 8) == 'storage/')
  $destination = 'storage';

if($m == 20)
{
  if($a == 1 || $a == 2 || $a == 3)
  {
    add_inventory($user["user"], '', 'Familiar', $user['display'] . ' summoned this', $destination);
    echo '<p>The room fills with a brilliant sunlight, which eventually fades to reveal a smiling Familiar!</p>';

    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Summoned a Familiar', 1);
  }
  else if($a == 4 || $a == 5)
  {
    add_inventory($user["user"], '', 'Demon', $user['display'] . ' summoned this', $destination);
    echo '<p>A hideous cackling precedes the sudden appearance of a Demon!</p>';

    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Summoned a Demon', 1);
  }
  else if($a == 6 || $a == 7)
  {
    $g = mt_rand(1, 2);
    echo '<p>A wizard appears, looking very confused, but even more so, very miffed.</p>' .
         '<p>"I was in the middle of a spell, you twit!  Well, I have a spell for you!  Amethyst Rose, Fire Spice, Fire Spice!! ... Oh, I mean: Lilac, Pumpkin Spice, Lilac!!"</p>' .
         '<p>Having said this, ' . ($g == 1 ? 'he' : 'she') . ' twirls ' . ($g == 1 ? 'his' : 'her') . ' wand in your general direction, and vanishes.  (And you\'re pretty sure you overheard ' . ($g == 1 ? 'him' : 'her') . ' muttering "damn, I always get those two confused," on ' . ($g == 1 ? 'his' : 'her') . ' way out.)</p>' .
         '<p><i>(A wizard has turned you into a whale!)</i></p>';

    $command = 'UPDATE monster_users SET is_a_whale=\'yes\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'itemaction.php');

    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Got Turned into a Whale', 1);
  }
}
else
{
  $petid = create_random_pet($user['user']);
  echo '<p>A small pet steps around from behind you, smiles, bows, and wanders off into the house.</p>';

  $level = mt_rand(1, 5);
  
  $initial_stats = array();
  $updates = array();

  for($i = 0; $i < $level; ++$i)
    $initial_stats[$PET_SKILLS[array_rand($PET_SKILLS)]]++;

  foreach($initial_stats as $stat=>$score)
    $updates[] = '`' . $stat . '`=' . $score;

  $database->FetchNone('UPDATE monster_pets SET ' . implode(',', $updates) . ' WHERE idnum=' . $petid . ' LIMIT 1');
  
  require_once 'commons/statlib.php';
  record_stat($user['idnum'], 'Summoned a Pet', 1);
}
?>
