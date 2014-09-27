<?php
if($okay_to_be_here !== true)
  exit();

$data = (int)$this_inventory['data'];
$now = time();
$DAY = 60 * 60 * 24;

$berries = 0;

$days = floor(($now - $data) / $DAY);
$berries = min(3, $days);

if($data == 0)
{
  $data = $now;

  $command = "UPDATE monster_inventory SET data='$data' WHERE idnum=" . $_GET["idnum"] . " LIMIT 1";
  $database->FetchNone($command, 'updating timestamp');

  $berries = 0;
  
  echo '<p>Your Death by Cactus is beginning to grow!  (Scary...)</p>';
}
else if($berries > 0)
{
  $data = $now;

  $command = "UPDATE monster_inventory SET data='$data' WHERE idnum=" . $_GET["idnum"] . " LIMIT 1";
  $database->FetchNone($command, 'updating timestamp');
}

if($berries > 0)
{
  $itemnames = array();

  add_inventory_quantity($user['user'], 'u:' . $user['idnum'], 'Cactus Flower', 'Harvested from a ' . $this_inventory['itemname'], $this_inventory['location'], $berries);

  echo '<p>You carefully harvest ' . $berries . ' Cactus Flower' . ($berries != 1 ? 's' : '') . ' from the ' . $this_inventory['itemname'] . '.</p>';

}
else
  echo '<p>There are currently no Cactus Flowers to harvest.</p>';

echo '<hr />';

if($_GET['step'] == 2)
{
  $database->FetchNone("UPDATE monster_users SET graphic='unique/cactusflower.png' WHERE idnum=" . $user["idnum"] . " LIMIT 1");
  
  echo '<p class="success">Done!</p>';
}
else
{
  echo '
    <p><img src="//saffron.psypets.net/gfx/avatars/unique/cactusflower.png" align="right" />Death by Cactus can also change your avatar into that of a Cactus Flower... how about it?</p>
    <ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&amp;step=2">Looks pretty!  Why not!</a></li></ul>
    <p><i>(Don\'t worry: doing so doesn\'t consume your cactus, or its flowers, or do anything else weird.  It\'s just a normal avatar change.)</i></p>
  ';
}
?>
