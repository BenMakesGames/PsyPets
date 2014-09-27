<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/questlib.php';

$data = (int)$this_inventory['data'];
$hour = 60 * 60;

if($data == 0)
{
  $data = $now;

  $command = "UPDATE monster_inventory SET data='$data' WHERE idnum=" . $this_inventory['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'item: ' . $this_inventory['itemname']);
  
  echo '<p>You pat the dirt down around the bush, and sprinkle some water on it.  It\'s beginning to grow!</p>';
}

$age = ($now - $data) / $hour;

$oranges = min(18, round($age / 28, 1));

if($_GET['shake'] == 1 && $oranges >= 1)
{
  $oranges = (int)$oranges;

  delete_inventory_byid($this_inventory['idnum']);

  for($i = 0; $i < $oranges; ++$i)
    add_inventory($user['user'], '', 'Amethyst Rose', 'Recovered from an ' . $this_inventory['itemname'], $this_inventory['location']);

  add_inventory($user['user'], '', 'Wood', 'Recovered from an ' . $this_inventory['itemname'], $this_inventory['location']);

  if($oranges == 1)
    echo '<p>An Amethyst Rose falls to the ground';
  else
    echo '<p>' . $oranges . ' Amethyst Roses fall to the ground';

  echo ', and the bush with it, dead.  (You dutifully collect its Wood.)</p>';
}
else
{
  echo '<p>This plant started growing ' . duration($now - $data, 2) . ' ago.</p>';

  if($oranges == 0)
    echo '<p>There are currently no Amethyst Roses on this bush.</p>';
  else if($oranges == 1)
    echo '<p>There is one Amethyst Rose on this bush.</p>';
  else
  {
    if($oranges >= 18)
      echo '<p>There are 18 Amethyst Roses on this bush; so many, it doesn\'t look like there\'s room for any more to grow!</p>';
    else if((int)$oranges == $oranges)
      echo '<p>There are ' . $oranges . ' Amethyst Roses on this bush.</p>';
    else
      echo '<p>There are ' . $oranges . ' Amethyst Roses on this bush.  Too bad you can\'t harvest a fraction of an Amethyst Rose &gt;_&gt;</p>';
  }

  if($oranges >= 1)
    echo '<ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&shake=1">Shake the bush!  Shake the bush!</a> (Doing so will harvest the roses, and Wood, but also destroy the bush.)</li></ul>';
}

$command = 'SELECT idnum,data FROM monster_inventory WHERE itemname=' . quote_smart($this_inventory['itemname']) . ' AND user=' . quote_smart($user['user']) . ' AND location=' . quote_smart($this_inventory['location']) . ' AND idnum!=' . $this_inventory['idnum'];
$others = $database->FetchMultiple($command, 'fetching other instances of the item');

if(count($others) > 0)
{
?>
<h5>Other <?= $this_inventory['itemname'] ?>es In This Room</h5>
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
    $oranges = min(18, round($age / 28, 1));
    echo 'An ' . $this_inventory['itemname'] . ' with ' . $oranges . ' Amethyst Rose' . ($oranges != 1 ? 's' : '') . ' on it';
  }

  echo '</a></li>'; 
}
?>
</ul>
<?php
}
?>
