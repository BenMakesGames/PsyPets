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
  
  echo '<p>You pat the dirt down around the stalk, and sprinkle some water on it.  It\'s beginning to grow!</p>';
}

$age = ($now - $data) / $hour;

$oranges = round($age / 32, 1);

if($_GET['shake'] == 1 && $oranges >= 1)
{
  $oranges = min(8, (int)$oranges);

  delete_inventory_byid($this_inventory['idnum']);

  for($i = 0; $i < $oranges; ++$i)
    add_inventory($user['user'], '', 'Corn', 'Recovered from a ' . $this_inventory['itemname'], $this_inventory['location']);

  if($oranges == 1)
    echo '<p>One Corn cob falls to the ground';
  else
    echo '<p>' . $oranges . ' Corn cobs fall to the ground';

  echo ', and the stalk, dead. </p>';
}
else
{
  echo '<p>This plant started growing ' . duration($now - $data, 2) . ' ago.</p>';

  if($oranges == 0)
    echo '<p>There are currently no Corn cobs on this stalk.</p>';
  else if($oranges == 1)
    echo '<p>There is one Corn cob on this stalk.</p>';
  else
  {
    if($oranges >= 8)
      echo '<p>There are 8 Corn cobs on this stalk; so many, it doesn\'t look like any more can possibly grow.</p>';
    else if((int)$oranges == $oranges)
      echo '<p>There are ' . $oranges . ' Corn cobs on this stalk.</p>';
    else
      echo '<p>There are ' . $oranges . ' Corn cobs on this stalk.  Too bad you can\'t harvest a fraction of a Corn cob &gt;_&gt;</p>';
  }

  if($oranges >= 1)
    echo '<ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&shake=1">Shake the stalk!  Shake the stalk!</a> (Doing so will harvest the fruit and destroy the stalk.)</li></ul>';
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
    $oranges = min(8, round($age / 32, 1));
    echo 'A ' . $this_inventory['itemname'] . ' with ' . $oranges . ' Corn cob' . ($oranges != 1 ? 's' : '') . ' on it';
  }

  echo '</a></li>'; 
}
?>
</ul>
<?php
}
?>

