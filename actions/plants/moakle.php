<?php
if($okay_to_be_here !== true)
  exit();

$data = (int)$this_inventory['data'];
$hour = 60 * 60;

if($data == 0)
{
  $data = $now;

  $command = "UPDATE monster_inventory SET data='$data' WHERE idnum=" . $this_inventory['idnum'] . " LIMIT 1";
  $database->FetchNone($command, 'item: ' . $this_inventory['itemname']);
  
  echo '<p>You pat the dirt down around the bush, and sprinkle some water on it.  It\'s beginning to grow!</p>';
}

$age = ($now - $data) / $hour;

$fruit = min(325, round($age / 13.5, 1));

// 20 maple leaves
// 12 acorns
// 8 bird nests
// 1 log

if($_GET['shake'] == 1 && $fruit >= 1)
{
  $fruit = (int)$fruit;

  delete_inventory_byid($this_inventory['idnum']);

  $items = array();

  for($x = 0; $x < $fruit; ++$x)
  {
    $a = mt_rand(1, 100);
    if($a <= 50)
      $item = 'Maple Leaf';
    else if($a <= 80)
      $item = 'Acorn';
    else
      $item = 'Bird Nest';

    $items[$item]++;
    add_inventory($user['user'], '', $item, $user['display'] . ' harvested this from a ' . $this_inventory['itemname'], $this_inventory['location']);
  }

  add_inventory($user['user'], '', 'Log', $user['display'] . ' harvested this from a ' . $this_inventory['itemname'], $this_inventory['location']);

  echo '<p>It takes a lot of effort, but you shake the tree, knocking loose...</p><ul>';
  foreach($items as $itemname=>$qty)
    echo '<li>' . $qty . '&times; ' . $itemname . '</li>';
  echo '</ul><p>... until all that remains is a stump.  (It\'ll make a good Log.)</p>';
}
else
{
  echo '<p>This plant started growing ' . duration($now - $data, 2) . ' ago.</p>';

  if($fruit == 0)
    echo '<p>There is nothing on this tree.</p>';
  else
  {
    else if($fruit >= 325)
      echo '<p>There\'s so much stuff in this tree!  So much, it doesn\'t look like any more can fit!</p>';
    else if($fruit < 7)
    {
      echo '<p>There\'s some stuff in this tree, but probably not even a tenth as much as can fit.  You know, if you had to guess.</p>';
    }
    else
    {
      $estimate = floor($fruit / 325 * 100);
      echo '<p>There\'s a lot of stuff in this tree, but probably only about ' . $estimate . '% as much as can fit.  You know, if you had to guess.</p>';
    }
  }

  if($fruit >= 1)
    echo '<ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&shake=1">Shake the tree!  Shake the tree!</a> (Doing so will harvest whatever\'s inside, and a Log, but also destroy the tree.)</li></ul>';
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
    $fruit = min(325, round($age / 13.5, 1));

    $estimate = floor($fruit / 325 * 100);
    echo 'A ' . $this_inventory['itemname'] . ' at about ' . $estimate . ' capacity';
  }

  echo '</a></li>'; 
}
?>
</ul>
<?php
}
?>
