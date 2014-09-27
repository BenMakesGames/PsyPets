<?php
if($okay_to_be_here !== true)
  exit();

$command = 'SELECT * FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Broom\' AND location LIKE \'home%\' AND location NOT LIKE \'home/protected\' ORDER BY health ASC LIMIT 1';
$broom = $database->FetchSingle($command, 'fetching broom');

if($broom !== false)
{
  if($broom['health'] <= 5)
  {
    delete_inventory_byid($broom['idnum']);
    add_inventory($user['user'], '', 'Ruins', 'This Broom was broken while sweeping.', $broom['location']);

    $broken = true;
  }
  else
  {
    $command = 'UPDATE monster_inventory SET health=health-5,changed=' . $now . ' WHERE idnum=' . $broom['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'item: ' . $this_inventory['itemname']);

    $broken = false;
  }

  $data = $this_inventory["data"];
  $now = time();
  $day = 60 * 60 * 24;

  $coconut = false;
  $dies = false;

  if(strlen($data) == 0)
  {
    $data = $now + $day;

    $command = "UPDATE monster_inventory SET data='$data' WHERE idnum=" . $this_inventory['idnum'] . " LIMIT 1";
    $database->FetchNone($command, 'item: ' . $this_inventory['itemname']);
  }
  else if($now > $data)
  {
    $data = $now + rand($day * 2, $day * 4);
    $fluff = true;

    $command = "UPDATE monster_inventory SET data='$data' WHERE idnum=" . $this_inventory['idnum'] . " LIMIT 1";
    $database->FetchNone($command, 'item: ' . $this_inventory['itemname']);
  }

  if($fluff)
  {
    if(rand() % 200 == 0)
    {
      add_inventory($user['user'], '', "World Map #4", 'Swept out from under a ' . $this_inventory['itemname'], $this_inventory["location"]);
      echo '<p>You sweep under the ' . $this_inventory["itemname"] . ", revealing a crumpled up piece of paper.  It seems to be some kind of map!";
    }
    else
    {
      add_inventory($user['user'], '', 'Fluff', 'Swept out from under a ' . $this_inventory["itemname"], $this_inventory["location"]);
      echo '<p>You sweep under the ' . $this_inventory['itemname'] . ', revealing some accumulated Fluff.';
    }
  }
  else
    echo '<p>You sweep under the ' . $this_inventory['itemname'] . ', but find nothing.  Maybe you should try again another day.';

  if($broken)
  {
    echo '  Oh, and the broom you were using breaks in the process.  Lame!';
    
    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Brooms Broken While Sweeping', 1);
  }

  echo '</p>';
}
else
  echo '<p>You don\'t have a broom to sweep with, unfortunately.</p>';
?>
