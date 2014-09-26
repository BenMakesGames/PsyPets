<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/questlib.php';

$data = (int)$this_inventory['data'];
$hour = 60 * 60;

if($data == 0 || $data > $now)
{
  $data = $now;

  $database->FetchNone('UPDATE monster_inventory SET data=' . $data . ' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1');
  
  echo '<p>You pat the dirt down around the tree, and sprinkle some water on it.  It\'s beginning to grow!</p>';
}

$age = ($now - $data) / $hour;

$oranges = round($age / 24, 1);

if($_GET['shake'] == 1 && $oranges >= 1)
{
  $oranges = min(5, (int)$oranges);

  delete_inventory_byid($this_inventory['idnum']);

  for($i = 0; $i < $oranges; ++$i)
    add_inventory($user['user'], '', 'Coconut', 'Recovered from a ' . $this_inventory['itemname'], $this_inventory['location']);

  add_inventory($user['user'], '', 'Log', 'Recovered from a ' . $this_inventory['itemname'], $this_inventory['location']);

  if($oranges == 1)
    echo '<p>A Coconut falls to the ground';
  else
    echo '<p>' . $oranges . ' Coconuts fall to the ground';

  echo ', and the tree with it, dead.  (You dutifully collect its Log.)</p>';

}
else
{
  echo '<p>This plant started growing ' . duration($now - $data, 2) . ' ago.</p>';

  if($oranges == 0)
    echo '<p>There are currently no Coconuts on this tree.</p>';
  else if($oranges == 1)
    echo '<p>There is one Coconut on this tree.</p>';
  else
  {
    if($oranges >= 5)
      echo '<p>There are 5 Coconuts on this tree; so many, it doesn\'t look like any more can possibly grow.</p>';
    else if((int)$oranges == $oranges)
      echo '<p>There are ' . $oranges . ' Coconuts on this tree.</p>';
    else
      echo '<p>There are ' . $oranges . ' Coconuts on this tree.  Too bad you can\'t harvest a fraction of a Coconut &gt;_&gt;</p>';
  }

  if($oranges >= 1)
    echo '<ul><li><a href="/itemaction.php?idnum=' . $this_inventory['idnum'] . '&shake=1">Shake the tree!  Shake the tree!</a> (Doing so will harvest the fruit, and a log, but also destroy the tree.)</li></ul>';
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
  echo '<li><a href="/itemaction.php?idnum=' . $other['idnum'] . '">';

  if((int)$other['data'] == 0)
    echo 'An untended ' . $this_inventory['itemname'] . ' which needs your attention';
  else
  {
    $age = ($now - (int)$other['data']) / $hour;
    $oranges = min(5, round($age / 24, 1));
    echo 'A ' . $this_inventory['itemname'] . ' with ' . $oranges . ' Coconut' . ($oranges != 1 ? 's' : '') . ' on it';
  }

  echo '</a></li>'; 
}
?>
</ul>
<?php
}
?>
