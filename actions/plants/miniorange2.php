<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/questlib.php';

$data = (int)$this_inventory['data'];
$hour = 60 * 60;

if($data == 0)
{
  $data = $now;

  $command = "UPDATE monster_inventory SET data='$data' WHERE idnum=" . $this_inventory['idnum'] . " LIMIT 1";
  $database->FetchNone($command, 'item: ' . $this_inventory['itemname']);
  
  echo '<p>You pat the dirt down around the tree, and sprinkle some water on it.  It\'s beginning to grow!</p>';
}

$age = ($now - $data) / $hour;

$oranges = round($age / 30, 1);

$orange_quest = get_quest_value($user['idnum'], 'oranges');

if($_GET['shake'] == 1 && $oranges >= 1)
{
  $oranges = min(12, (int)$oranges);

  delete_inventory_byid($this_inventory['idnum']);

  add_inventory_quantity($user['user'], '', 'Orange', 'Recovered from a ' . $this_inventory['itemname'], $this_inventory['location'], $oranges);

  add_inventory($user['user'], '', 'Log', 'Recovered from a ' . $this_inventory['itemname'], $this_inventory['location']);

  if($oranges == 1)
    echo '<p>An Orange falls to the ground';
  else
    echo '<p>' . $oranges . ' Oranges fall to the ground';

  echo ', and the tree with it, dead.  (You dutifully collect its Log.)</p>';

  if($orange_quest === false)
    add_quest_value($user['idnum'], 'oranges', $oranges);
  else
    update_quest_value($orange_quest['idnum'], $oranges + $orange_quest['value']);

  if($orange_quest['value'] + $oranges >= 100)
  {
    $badges = get_badges_byuserid($user['idnum']);
    if($badges['oranges'] == 'no')
    {
      set_badge($user['idnum'], 'oranges');
      echo '<p><i>(You received the Oranges!  ORANGES! badge.)</i></p>';
    }
  }
}
else
{
  echo '<p>This plant started growing ' . duration($now - $data, 2) . ' ago.</p>';

  if($oranges == 0)
    echo '<p>There are currently no Oranges on this tree.</p>';
  else if($oranges == 1)
    echo '<p>There is one Orange on this tree.</p>';
  else
  {
    if($oranges >= 12)
      echo '<p>There are 12 Oranges on this tree; so many, it doesn\'t look like any more can possibly grow.</p>';
    else if((int)$oranges == $oranges)
      echo '<p>There are ' . $oranges . ' Oranges on this tree.</p>';
    else
      echo '<p>There are ' . $oranges . ' Oranges on this tree.  Too bad you can\'t harvest a fraction of an Orange &gt;_&gt;</p>';
  }

  if($oranges >= 1)
    echo '<ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&shake=1">Shake the tree!  Shake the tree!</a> (Doing so will harvest the fruit, and a log, but also destroy the tree.)</li></ul>';
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
    $oranges = min(12, round($age / 30, 1));
    echo 'A ' . $this_inventory['itemname'] . ' with ' . $oranges . ' Orange' . ($oranges != 1 ? 's' : '') . ' on it';
  }

  echo '</a></li>'; 
}
?>
</ul>
<?php
}
?>
<p><i>(Oranges harvested so far: <?= (int)$orange_quest['value'] ?>.)</i></p>

