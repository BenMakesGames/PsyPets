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
  
  echo '<p>You pat the dirt down, and sprinkle some water on it.  They\'re beginning to grow!</p>';
}

$age = ($now - $data) / $hour;

$oranges = round($age / 16, 1);

if($_GET['shake'] == 1 && $oranges >= 1)
{
  $oranges = min(9, (int)$oranges);

  delete_inventory_byid($this_inventory['idnum']);

  for($i = 0; $i < $oranges; ++$i)
    add_inventory($user['user'], '', 'Sugar Beet', 'Recovered from a ' . $this_inventory['itemname'], $this_inventory['location']);

  if($oranges == 1)
    echo '<p>A Sugar Beet pops up out of the ground';
  else
    echo '<p>' . $oranges . ' Sugar Beets pop up out of the ground';

  echo '.</p>';
}
else
{
  if($oranges == 0)
    echo '<p>There are currently no Sugar Beets.</p>';
  else if($oranges == 1)
    echo '<p>There is one Sugar Beet.</p>';
  else
  {
    if($oranges >= 9)
      echo '<p>There are 9 Sugar Beets; so many, it doesn\'t look like any more can possibly grow.</p>';
    else if((int)$oranges == $oranges)
      echo '<p>There are ' . $oranges . ' Sugar Beets.</p>';
    else
      echo '<p>There are ' . $oranges . ' Sugar Beets.  Too bad you can\'t harvest a fraction of a Sugar Beet &gt;_&gt;</p>';
  }

  if($oranges >= 1)
    echo '<ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&shake=1">Shake them!  Shake them!</a> (Doing so will harvest the beets.)</li></ul>';
}

$command = 'SELECT idnum,data FROM monster_inventory WHERE itemname=' . quote_smart($this_inventory['itemname']) . ' AND user=' . quote_smart($user['user']) . ' AND location=' . quote_smart($this_inventory['location']) . ' AND idnum!=' . $this_inventory['idnum'];
$others = $database->FetchMultiple($command, 'fetching other instances of the item');

if(count($others) > 0)
{
?>
<h5>Other <?= $this_inventory['itemname'] ?> In This Room</h5>
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
    $oranges = min(9, round($age / 16, 1));
    echo 'A ' . $this_inventory['itemname'] . ' with ' . $oranges . ' Sugar Beet' . ($oranges != 1 ? 's' : '') . ' in it';
  }

  echo '</a></li>'; 
}
?>
</ul>
<?php
}
?>
