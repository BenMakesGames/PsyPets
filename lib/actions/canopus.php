<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/questlib.php';

$been_there_done_that = get_quest_value($user['idnum'], 'Used a Canopus');

if($_GET['action'] == 'pour')
{
  $AGAIN_WITH_ANOTHER = true;
  $RECOUNT_INVENTORY = true;

  $command = 'UPDATE monster_inventory SET health=0,itemname=\'Gold\' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'melted into Gold');
  
  $juices = array(
    'Apple Juice',
    'Coconut Juice',
    'Limeade',
    'Orange Juice',
    'Pamplemousse Juice',
    'Pomegranate Juice',
    'Prickly Green Juice',
  );

  $juice = $juices[array_rand($juices)];

  if($been_there_done_that === false)
  {
    $cups = 2;

    echo '
      <p>You turn the cup completely upside down.  At first nothing happens, but as you tilt your head to take a look underneath, the contents - ', $juice, ', apparently - suddenly gush out!</p>
      <p>There\'s somehow much more than one glass could possibly hold, but you fortunately have a few at hand.</p>
      <p>After filling your ', numeric_place($cups), ' glass, the ', $this_inventory['itemname'], ' turns to a liquid gold, and pours itself through you fingers and onto the table where it solidifies into a plain lump of Gold.</p>
    ';
  
    add_quest_value($user['idnum'], 'Used a Canopus', 1);
  }
  else
  {
    $cups = mt_rand(2, 5);
    
    echo '
      <p>This time you\'re ready!</p>
      <p>You turn the cup upside down, and, after about a second, ', $juice, ' starts to pour out.</p>
      <p>You manage to collect ', $cups, ' glasses before the ', $this_inventory['itemname'], ' pours itself onto the table, and into a lump of Gold.</p>
    ';
  }
  
  add_inventory_quantity($user['user'], 'u:' . $user['idnum'], $juice, 'Poured out of a ' . $this_inventory['itemname'], $this_inventory['location'], $cups);
}
else
{
  if($been_there_done_that === false)
  {
?>
  <p>You try to take a sip, but the liquid inside seems to have no respect for gravity!</p>
  <p>Strange!</p>
  <ul>
   <li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&action=pour">Turn the cup upside-down (over a glass, or something, of course).</a></li>
  </ul>
<?php
  }
  else
  {
?>
  <p>The cup does not give up its contents so easily...</p>
  <ul>
   <li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&action=pour">Get a few glasses ready, and turn the cup upside-down.</a></li>
  </ul>
<?php
  }
}
?>
