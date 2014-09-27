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
  
  echo '<p>You watch as clouds of Hydrogen swirl and collapse.  Stellar nurseries are starting to flare up!</p>';
}

$age = ($now - $data) / $hour;

$oranges = floor($age / 30);

if($_GET['shake'] == 1 && $oranges >= 1)
{
  $oranges = min(8, (int)$oranges);

  delete_inventory_byid($this_inventory['idnum']);

  $possible_objects = array('Shooting Star', 'Dark Matter', 'Hydrogen', 'Supernova', 'Gold Star Stickers');
  
  for($i = 0; $i < $oranges; ++$i)
  {
    $item = $possible_objects[array_rand($possible_objects)];
    $item_list[$item]++;
    add_inventory($user['user'], '', $item, 'Recovered from a ' . $this_inventory['itemname'], $this_inventory['location']);
  }
  
  ksort($item_list);
  
  echo '<p>The ' . $this_inventory['itemname'] . ' is torn apart, releasing...</p><ul>';
  
  foreach($item_list as $item=>$quantity)
    echo '<li>' . $quantity . '&times; ' . $item . '</li>';
  
  echo '</ul>';
}
else
{
  echo '<p>This galaxy started forming stars ' . duration($now - $data, 2) . ' ago.</p>';

  if($oranges == 0)
    echo '<p>There\'s currently nothing interesting in this galaxy.  (Well, there is that Black Hole in the center, but you get the impression it\'s best left alone.)</p>';
  else if($oranges < 3)
    echo '<p>Small interstellar clouds are taking shape, but there\'s really not much going on in this galaxy just yet.</p>';
  else
  {
    if($oranges >= 10)
      echo '<p>This galaxy is swirling with activity!  (You doubt it could swirl more vigorously!)</p>';
    else
      echo '<p>This galaxy is swirling with activity!  But you bet it could swirl a bit more, yet.</p>';
  }

  if($oranges >= 1)
    echo '<ul><li><a href="?idnum=' . $this_inventory['idnum'] . '&shake=1">Shake it!  Shake it!</a> (Doing so will rip the galaxy apart, releasing whatever\'s inside.)</li></ul>';
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
  echo '<li><a href="?idnum=' . $other['idnum'] . '">';

  if((int)$other['data'] == 0)
    echo 'A stagnant ' . $this_inventory['itemname'] . ' which needs your attention';
  else
  {
    $age = ($now - (int)$other['data']) / $hour;
    $oranges = min(8, floor($age / 30));
    echo 'A ' . $this_inventory['itemname'] . ' ';
    if($oranges == 0)
      echo 'with nothing going on';
    else if($oranges < 3)
      echo 'with little activity';
    else if($oranges < 10)
      echo 'swirling with activity';
    else
      echo 'swirling with activity!  it couldn\'t swirl more!';
  }

  echo '</a></li>'; 
}
?>
</ul>
<?php
}
?>
