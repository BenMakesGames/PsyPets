<?php
if($okay_to_be_here !== true)
  exit();

if($this_inventory['itemname'] == 'Muffin Tree')
{
  $muffin = 'Blueberry Muffin';
  $num_hours = 24;
}
else
{
  $muffin = 'Different Kind of Muffin';
  $num_hours = 30;
}

$data = (int)$this_inventory['data'];
$hour = 60 * 60;

if($data == 0)
{
  $data = $now;

  $command = "UPDATE monster_inventory SET data='$data' WHERE idnum=" . $this_inventory['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'item: ' . $this_inventory['itemname']);
  
  echo '<p>You pat the dirt down around the tree, and sprinkle some water on it.  It\'s beginning to grow!</p>';
}

$age = ($now - $data) / $hour;

$oranges = round($age / $num_hours, 1);

if($_GET['action'] == 'shake' && $oranges >= 1)
{
  $data += (int)$oranges * $num_hours * $hour;

  $oranges = min(12, (int)$oranges);

  for($i = 0; $i < $oranges; ++$i)
    add_inventory($user['user'], '', $muffin, 'Recovered from a ' . $this_inventory['itemname'], $this_inventory['location']);

  if($oranges == 1)
    echo '<p>A ' . $muffin . ' falls to the ground';
  else
    echo '<p>' . $oranges . ' ' . $muffin . 's fall to the ground';

  $command = 'UPDATE monster_inventory SET data=' . $data . ' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'resetting muffin tree');

  echo '.  (The tree remains standing.)</p>';
  
  $oranges = 0;
}
else if($_GET['action'] == 'change' && $oranges < 1)
{
  $command = 'UPDATE monster_inventory SET itemname=\'Different Kind of Muffin Tree\' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'changing muffin tree type');
  
  header('Location: ./itemaction.php?idnum=' . $this_inventory['idnum']);
  exit();
}
else
{
  if($oranges == 0)
    echo '<p>There are currently no ' . $muffin . 's on this tree.</p>';
  else if($oranges == 1)
    echo '<p>There is a ' . $muffin . ' on this tree.</p>';
  else
  {
    if($oranges > 12)
      echo '<p>There are a dozen ' . $muffin . 's on this tree.  They\'re so crowded, there\'s no way any more can possibly grow...</p>';
    else if((int)$oranges == $oranges)
      echo '<p>There are ' . $oranges . ' ' . $muffin . 's on this tree.</p>';
    else
      echo '<p>There are ' . $oranges . ' ' . $muffin . 's on this tree.  Too bad you can\'t harvest a fraction of a ' . $muffin . ' &gt;_&gt;</p>';
  }

  if($oranges >= 1)
    echo '<ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=shake">Shake the tree!  Shake the tree!</a> (Doing so will harvest the "fruit".  The tree will <em>not</em> be destroyed.)</li></ul>';
}

$command = 'SELECT idnum,data FROM monster_inventory WHERE itemname=' . quote_smart($this_inventory['itemname']) . ' AND user=' . quote_smart($user['user']) . ' AND location=' . quote_smart($this_inventory['location']) . ' AND idnum!=' . $this_inventory['idnum'];
$others = $database->FetchMultiple($command, 'fetching other instances of the item');

if($this_inventory['itemname'] == 'Muffin Tree')
{
  if($oranges < 1)
    echo '<ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=change" onclick="return confirm(\'Doing so will cause the Muffin Tree to be permanently changed into a Different Kind of Muffin Tree.\n\nIs this really what you want to do?\');">Tell the Muffin Tree you are tired of Blueberry Muffins; ask it if it will make another kind.</a></li></ul>';
  else
    echo '<p><i>(You must harvest the Muffins from the tree before you can ask it to make a different kind of Muffin.)</i></p>';
}

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
    $oranges = min(12, round($age / $num_hours, 1));
    echo 'A ' . $this_inventory['itemname'] . ' with ' . $oranges . ' ' . $muffin . ($oranges != 1 ? 's' : '') . ' on it';
  }

  echo '</a></li>'; 
}
?>
</ul>
<?php
}
?>
