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

$fruit = min(18, round($age / 34, 1));

if($_GET['shake'] == 1 && $fruit >= 1)
{
  $fruit = (int)$fruit;

  delete_inventory_byid($this_inventory['idnum']);

  $apples = 0;
  $berries = 0;

  for($x = 0; $x < $fruit; ++$x)
  {
    if(mt_rand(1, 2) == 1)
    {
      add_inventory($user['user'], '', 'Redsberries', 'Harvested from an Appleberry Bush', $this_inventory['location']);
      $berries++;
    }
    else
    {
      add_inventory($user['user'], '', 'Delicious', 'Harvested from an Appleberry Bush', $this_inventory['location']);
      $apples++;
    }
  }

  if($berries > 0)
  {
    echo '<p>' . $berries . ' Redsberries ';
    if($apples > 0)
      echo 'and ';
  }

  if($apples > 0)
    echo $apples . ' Delicious' . ($apples != 1 ? 'es' : '') . ' ';

  echo 'fall from the ' . $this_inventory['itemname'] . ', which itself falls over, dead.  (You dutifully collect Wood from its remains.)</p>';

  add_inventory($user['user'], '', 'Wood', 'Harvested from an Appleberry Bush', $this_inventory['location']);
}
else
{
  echo '<p>This plant started growing ' . duration($now - $data, 2) . ' ago.</p>';

  if($fruit == 0)
    echo '<p>There is no Fruit on this bush.</p>';
  else if($fruit == 1)
    echo '<p>There is one piece of Fruit on this bush, maybe more!</p>';
  else
  {
    if($fruit < 1)
      echo '<p>There are ' . $fruit . ' pieces of fruit on this bush.  Too bad you can\'t harvest fractions of fruit &gt;_&gt;</p>';
    else if($fruit >= 18)
      echo '<p>There are 18 pieces of fruit on this bush; so many, it doesn\'t look like any more are going to be able to grow.</p>';
    else if((int)$fruit == $fruit)
      echo '<p>There are ' . $fruit . ' pieces of fruit on this bush!</p>';
    else
      echo '<p>There are ' . $fruit . ' pieces of fruit on this bush!  Too bad you can\'t harvest fractions of fruit &gt;_&gt;</p>';
  }

  if($fruit >= 1)
    echo '<ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&shake=1">Shake the bush!  Shake the bush!</a> (Doing so will harvest the fruit, and Wood, but also destroy the bush.)</li></ul>';
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
    $fruit = min(18, round($age / 34, 1));
    echo 'An ' . $this_inventory['itemname'] . ' with ' . $fruit . ' piece' . ($fruit != 1 ? 's' : '') . ' of fruit on it';
  }

  echo '</a></li>'; 
}
?>
</ul>
<?php
}
?>
