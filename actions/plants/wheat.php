<?php
if($okay_to_be_here !== true)
  exit();

$max_harvest = 7;
$hours_per_growth = 32;

$data = (int)$this_inventory['data'];
$hour = 60 * 60;

if($data == 0)
{
  $data = $now;

  $command = "UPDATE monster_inventory SET data='$data' WHERE idnum=" . $this_inventory['idnum'] . " LIMIT 1";
  $database->FetchNone($command, 'item: ' . $this_inventory['itemname']);
  
  echo '<p>You pat the dirt down inside the pot, and sprinkle some water in it.  The Wheat\'s beginning to grow!</p>';
}

$age = ($now - $data) / $hour;

$oranges = round($age / $hours_per_growth, 1);

if(($_GET['shake'] == 1 || $_GET['shake'] == 2) && $oranges >= 1)
{
  $oranges = min($max_harvest, (int)$oranges);

  delete_inventory_byid($this_inventory['idnum']);

  for($i = 0; $i < $oranges; ++$i)
    add_inventory($user['user'], '', 'Wheat', 'Recovered from a ' . $this_inventory['itemname'], $this_inventory['location']);

  if($oranges == 1)
    echo '<p>A Wheat falls to the ground.</p>';
  else
    echo '<p>' . $oranges . ' Wheat fall to the ground.</p>';
  
  if($_GET['shake'] == 2)
  {
    $command = 'UPDATE monster_users SET title=\'Peasant\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'updating resident\'s title');

    echo '<p><i>(Your title has been changed to "Peasant".)</i></p>';
  }
}
else
{
  echo '<p>This plant started growing ' . duration($now - $data, 2) . ' ago.</p>';

  if($oranges == 0)
    echo '<p>There is currently no Wheat in this pot.</p>';
  else if($oranges == 1)
    echo '<p>There is a Wheat in this pot.</p>';
  else
  {
    if($oranges >= $max_harvest)
      echo '<p>There is ' . $max_harvest . ' Wheat in this pot.  The stalks are all so crowded, there\'s no way any more can possibly grow...</p>';
    else if((int)$oranges == $oranges)
      echo '<p>There is ' . $oranges . ' Wheat in this pot.</p>';
    else
      echo '<p>There is ' . $oranges . ' Wheat in this pot.  Too bad you can\'t harvest a fraction of a Wheat &gt;_&gt;</p>';
  }

  if($oranges >= 1)
  {
    echo '
      <ul>
       <li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&shake=1">It\'s harvest time!</a> (Doing so will harvest the grain, and destroy the Potted Wheat.)</li>
       <li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&shake=2">It\'s harvest time!  And I should like to be called "Peasant" from here on out.</a> (Doing so will harvest the grain, change your title, and destroy the Potted Wheat.)</li>
      </ul>
    ';
  }
}

$command = 'SELECT idnum,data FROM monster_inventory WHERE itemname=' . quote_smart($this_inventory['itemname']) . ' AND user=' . quote_smart($user['user']) . ' AND location=' . quote_smart($this_inventory['location']) . ' AND idnum!=' . $this_inventory['idnum'];
$others = $database->FetchMultiple($command, 'fetching other instances of the item');

if(count($others) > 0)
{
?>
<h5>Other <?= $this_inventory['itemname'] ?>s In This Room</h5>
<ul>
<?php
foreach($others as $other)
{
  echo '<li><a href="itemaction.php?idnum=' . $other['idnum'] . '">';

  if((int)$other['data'] == 0)
    echo 'An untended ' . $this_inventory['itemname'] . ' which needs your attention';
  else
  {
    $age = ($now - (int)$other['data']) / $hour;
    $oranges = min($max_harvest, round($age / $hours_per_growth, 1));
    echo 'A ' . $this_inventory['itemname'] . ' with ' . $oranges . ' Wheat on it';
  }

  echo '</a></li>'; 
}
?>
</ul>
<?php
}
?>
