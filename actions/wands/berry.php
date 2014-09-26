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

  $command = "UPDATE monster_inventory SET data='$data' WHERE idnum=" . $_GET["idnum"] . ' LIMIT 1';
  $database->FetchNone($command, 'updating timestamp');

  $berries = 0;
}
else if($berries > 0)
{
  $data = $now;

  $command = "UPDATE monster_inventory SET data='$data' WHERE idnum=" . $_GET["idnum"] . ' LIMIT 1';
  $database->FetchNone($command, 'updating timestamp');
}

if($berries > 0)
{
  $itemnames = array();

  for($x = 0; $x < $berries; ++$x)
  {
    if(mt_rand(1, 7) == 1)
      $itemname = 'Goodberries';
    else if(mt_rand(1, 11) == 1)
      $itemname = 'Evilberries';
    else
      $itemname = (mt_rand(1, 2) == 1 ? 'Redsberries' : 'Blueberries');

    add_inventory($user['user'], 'u:' . $user['idnum'], $itemname, 'Summoned by a ' . $this_inventory['itemname'], $this_inventory['location']);

    $itemnames[] = $itemname;
  }

  echo '<p>You point the wand, out of which issues ' . implode(', ', $itemnames) . '.</p>';

}
else
  echo '<p>You point the wand, but it issues only a puff of smoke.</p>';
?>
