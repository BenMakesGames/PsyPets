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
  
  echo '<p>You watch as an explosion begins to take place, and the four forces are begin to separate!  It\'s a <strong>horrendous space kablooie!</strong></p>';
  echo '<p>(If that\'s not exciting to you, go take an astronomy class!)</p>';
}

$age = ($now - $data) / $hour;

$oranges = floor($age / 20);

if($_GET['shake'] == 1 && $oranges >= 1)
{
  $oranges = min(10, (int)$oranges);

  delete_inventory_byid($this_inventory['idnum']);

  $possible_objects = array('Spiral Galaxy', 'Elliptical Galaxy');

  for($i = 0; $i < $oranges; ++$i)
  {
    $item = $possible_objects[array_rand($possible_objects)];
    $item_list[$item]++;
    add_inventory($user['user'], '', $item, 'Recovered from a ' . $this_inventory['itemname'], $this_inventory['location']);
  }
  
  ksort($item_list);
  
  echo '<p>The universe is torn apart! (Agh!  Oh, God!  Help-- oh, oh, wait: it\'s not <em>our</em> universe, just some <em>other</em> universe.  Phew!)</p><p>Inside, you find:</p><ul>';
  
  foreach($item_list as $item=>$quantity)
    echo '<li>' . $quantity . '&times; ' . $item . '</li>';
  
  echo '</ul>';
  
  if(mt_rand(1, 10) == 1)
  {
    echo '<p>Amidst the wreckage, you also spot... a Spaceship Totem?  Weird!</p>';
    add_inventory($user['user'], '', 'Spaceship Totem', 'Found near a ' . $this_inventory['itemname'], $this_inventory['location']);
  }
  else
    echo '<p>Neat-o!</p>';
}
else
{
  echo '<p>This universe started forming ' . duration($now - $data, 2) . '-- er, <strong>billions of years ago!</strong></p>';

  if($oranges == 0)
    echo '<p>This universe is still mostly composed of plasma.  Useless, <em>useless</em> plasma!</p>';
  else if($oranges < 2)
    echo '<p>This universe contains merely a single Galaxy!</p>';
  else
  {
    if($oranges >= 10)
      echo '<p>This universe contains 10 Galaxies!  You don\'t think there\'s room for any more, however.</p>';
    else
      echo '<p>This universe contains ' . (int)$oranges . ' Galaxies, and there\'s still room for more!</p>';
  }

  if($oranges >= 1)
    echo '<ul><li><a href="?idnum=' . $this_inventory['idnum'] . '&shake=1">Shake it!  Shake it!</a> (Doing so will rip the universe apart, releasing the galaxies inside!  Dramatic!)</li></ul>';
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
    echo 'A singularity which needs your attention';
  else
  {
    $age = ($now - (int)$other['data']) / $hour;
    $oranges = min(10, floor($age / 30));
    echo 'A ' . $this_inventory['itemname'] . ' containing ' . (int)$oranges . ' Galaxies';
    if($oranges >= 10)
      echo ' (the greatest number possible)';
  }

  echo '</a></li>'; 
}
?>
</ul>
<?php
}
?>
