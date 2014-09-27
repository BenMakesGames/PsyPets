<?php
if($okay_to_be_here !== true)
  exit();

if($_GET['step'] == 2)
{
  $items = array(
    'Fluff',
    'Gold Leaf',
    'Gossamer',
    'Gossamer',
    'Gold',
    'Gold',
  );
  
  $descript = array(
    'break apart',
    'unravel',
    'pick through',
  );
  
  $descript2 = array(
    'eventually recovering',
    'revealing',
    'exposing',
  );
  
  $num_items = rand() % 4 + 1;
  
	delete_inventory_byid($this_inventory['idnum']);
  
  $itemnames = array();
  
  for($i = 0; $i < $num_items; ++$i)
    $itemnames[] = $items[array_rand($items)];
  
  sort($itemnames);
  
  $items = 0;

  foreach($itemnames as $itemname)
  {
    add_inventory($user['user'], '', $itemname, 'Recovered from ' . $this_item['itemname'], $this_inventory['location']);
  
    $items++;
  
    if($items > 1)
      $itemlist .= ($items == count($itemnames) ? ' and ' : ', ');
  
    $itemlist .= $itemname;
  }
  
  echo 'You ' . $descript[array_rand($descript)] . ' the ' . $this_item['itemname'] . ', ' . $descript2[array_rand($descript2)] . ' ' . $itemlist . '.';

  require_once 'commons/statlib.php';
  record_stat($user['idnum'], 'Rummaged Through a Tumbleweed', 1);
}
else
{
?>
Really go rummaging through the Tumbleweed?  You know it'll probably fall apart if you do...</p>
<ul>
 <li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&step=2">Yes, yes, I know.</a></li>
</ul>
<?php
}
?>
