<?php
if($okay_to_be_here !== true)
  exit();

$data = (int)$this_inventory['data'];
$now = time();
$DAY = 60 * 60 * 12;

$berries = 0;

$days = floor(($now - $data) / $DAY);
$berries = min(4, $days);

if($data == 0)
{
  $data = $now;

  $command = "UPDATE monster_inventory SET data='$data' WHERE idnum=" . $_GET["idnum"] . " LIMIT 1";
  $database->FetchNone($command, 'updating timestamp');

  $berries = 0;
  
  echo '<p>You give the staff a tap to get it going.</p><p>Now just to wait...</p>';
}
else if($berries > 0)
{
  $data = $now;

  $command = "UPDATE monster_inventory SET data='$data' WHERE idnum=" . $_GET["idnum"] . " LIMIT 1";
  $database->FetchNone($command, 'updating timestamp');

  add_inventory_quantity($user['user'], 'u:' . $user['idnum'], 'Unflavored Snowball', 'Summoned by a ' . $this_inventory['itemname'], $this_inventory['location'], $berries);

  echo '<p>You twirl the staff dramatically before striking a pose - the staff outstretched - and summoning ' . $berries . ' Unflavored Snowball' . ($berries != 1 ? 's' : '') . '!  Fantastic!  Such flair!</p>';

}
else
  echo '<p>You twirl the staff dramatically before striking a pose - the staff outstretched - and summoning... a puff of cold air.</p><p>I guess it\'s not ready yet...</p>';
?>
