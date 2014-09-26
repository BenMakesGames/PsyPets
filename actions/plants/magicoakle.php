<?php
if($okay_to_be_here !== true)
  exit();

$data = (int)$this_inventory['data'];
$hour = 60 * 60;

if($data == 0)
{
  $data = $now;

  $database->FetchNone('
    UPDATE monster_inventory
    SET data=' . $data . '
    WHERE idnum=' . $this_inventory['idnum'] . '
    LIMIT 1
  ');
  
  echo '<p>You pat the dirt down around the tree, and sprinkle some water on it.  It\'s beginning to grow!</p>';
}

$age = ($now - $data) / $hour;

$fruit = min(140, round($age / 5.25, 1));

$fruit_items['Acorn'] = floor($fruit / 7);
$fruit_items['Oakle Leaf'] = floor($fruit / 14);
$fruit_items['Zephrous'] = floor($fruit / 14);
$fruit_items['Aquite'] = floor($fruit / 14);
$fruit_items['Gossamer'] = floor($fruit / 20);
$fruit_items['Log'] = floor($fruit / 80) + 1;
$fruit_items['Amber'] = floor($fruit / 60);

$total_fruit = 0;

foreach($fruit_items as $itemname=>$quantity)
  $total_fruit += $quantity;

$total_fruit--; // get rid of the free log

if($_GET['shake'] == 1)
{
  delete_inventory_byid($this_inventory['idnum']);

  echo '<p>Out of the tree falls:</p><ul>';
  
  foreach($fruit_items as $itemname=>$quantity)
  {
    if($quantity > 0)
    {
      add_inventory_quantity($user['user'], '', $itemname, 'Harvested from a Potted Magic Oakle', $this_inventory['location'], $quantity);
      echo '<li>' . $quantity . '&times; ' . $itemname . '</li>';
    }
  }
  
  echo '</ul>';
}
else
{
  echo '<p>This tree started growing ' . duration($now - $data, 2) . ' ago.</p>';

  echo '<p>It\'s hard to tell what all\'s in there, but besides a Log, you think you\'ll probably get ' . $total_fruit . ' other thing' . ($total_fruit == 1 ? '' : 's') . '.';

  if($fruit == 140)
    echo '<p>It looks like it\'s done growing, too!</p>';
  else if($fruit >= 14)
    echo '<p>It looks about ' . floor($fruit * 10 / 140) . '0% mature.</p>';
  
  echo '<ul><li><a href="/itemaction.php?idnum=' . $this_inventory['idnum'] . '&shake=1">Shake the tree!  Shake the tree!</a> (Doing so will harvest everything, but destroy the tree.)</li></ul>';
}

$others = $database->FetchMultiple('SELECT idnum,data FROM monster_inventory WHERE itemname=' . quote_smart($this_inventory['itemname']) . ' AND user=' . quote_smart($user['user']) . ' AND location=' . quote_smart($this_inventory['location']) . ' AND idnum!=' . $this_inventory['idnum']);

if(count($others) > 0)
{
?>
<h5>Other <?= $this_inventory['itemname'] ?>s In This Room</h5>
<ul>
<?php
foreach($others as $other)
{
  echo '<li><a href="/itemaction.php?idnum=' . $other['idnum'] . '">';

  if((int)$other['data'] == 0)
    echo 'An untended ' . $this_inventory['itemname'] . ' which needs your attention';
  else
  {
    $age = ($now - (int)$other['data']) / $hour;
    $fruit = min(140, round($age / 5.25, 1));

    if($fruit == 140)
      echo 'A ' . $this_inventory['itemname'] . ' which is fully mature';
    else if($fruit > 14)
      echo 'A ' . $this_inventory['itemname'] . ' which is about ' . floor($fruit * 10 / 140) . '0% mature';
    else
      echo 'An immature ' . $this_inventory['itemname'];
  }

  echo '</a></li>'; 
}
?>
</ul>
<?php
}
?>
