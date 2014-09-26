<?php
if($okay_to_be_here !== true)
  exit();

$data = $this_inventory['data'];
$now = time();
$DAY = 60 * 60 * 16;

$berries = 0;

if(strlen($data) == 0)
{
  $data = $now + $DAY;

  $database->FetchNone('UPDATE monster_inventory SET data=\'' . $data . '\' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1');
  
  echo '<p>You give the wand a good shake to get the magical processes started.  Now just to wait...</p>';
}
else if($now > $data)
{
  $days = floor(($now - $data) / $DAY) + 1;

  $berries = min(3, $days);

  $data = $now + $DAY;

	$database->FetchNone("UPDATE monster_inventory SET data='$data' WHERE idnum=" . $_GET["idnum"] . " LIMIT 1");

  if($berries > 0)
  {
    $itemnames = array();

    for($x = 0; $x < $berries; ++$x)
    {
      $a = mt_rand(1, 100);
      if($a <= 65)
        $itemname = 'Pasta';
      else if($a <= 70)
        $itemname = 'Lasagna'; // 19 hours
      else if($a <= 80)
        $itemname = 'Macaroni and Cheese'; // 22 hours
      else if($a <= 89)
        $itemname = 'Pasta Salad'; // 24 hours
      else if($a <= 95)
        $itemname = 'Chicken Noodle Soup'; // 30 hours
      else
        $itemname = 'Beef Stroganon'; // 32 hours

      add_inventory($user['user'], 'u:' . $user['idnum'], $itemname, 'Summoned by a ' . $this_inventory['itemname'], $this_inventory['location']);

      $itemnames[] = $itemname;
    }

    echo '<p>You point the wand, out of which issues ' . implode(', ', $itemnames) . '.</p>';

  }
  else
    echo '<p>You point the wand, but it issues only a puff of smoke.  (Apparently it\'s not ready just yet...)</p>';
}
else
  echo '<p>You point the wand, but it issues only a puff of smoke.  (You might have to wait a few hours.  It\'d probably be best to check back tomorrow.)</p>';
?>
