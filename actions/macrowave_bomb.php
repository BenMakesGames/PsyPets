<?php
if($okay_to_be_here !== true)
  exit();

if(substr($this_inventory['location'], 0, 7) == 'storage')
{
  $location = 'IN(\'storage\', \'storage/incoming\', \'storage/locked\')';
  $location_desc = 'your storage';
}
else
{
  $location = 'LIKE \'home%\'';
  $location_desc = 'the house';
}

if($_GET['step'] == 2)
{
  require_once 'commons/statlib.php';
  
  delete_inventory_byid($this_inventory['idnum']);
  
  echo '<p>The ' . $this_inventory['itemname'] . ' explodes, radiating macrowaves that permeate every room!</p>';
  
  $command = '
    SELECT a.idnum,b.recycle_for,b.recycle_fraction,a.location
    FROM monster_inventory AS a
    LEFT JOIN monster_items AS b ON a.itemname=b.itemname
    WHERE a.user=' . quote_smart($user['user']) . '
    AND a.location ' . $location . '
    AND b.is_edible=\'yes\'
    AND b.recycle_for!=\'\'
    ORDER BY RAND()
    LIMIT ' . mt_rand(8, 15) . '
  ';
  $items = $database->FetchMultiple($command, 'fetching items to explode');

  if(count($items) > 0)
  {
    $items_received = array();
  
    foreach($items as $item)
    {
      $deleted = delete_inventory_byid($item['idnum']);

      if($deleted == 0)
        continue;

      $makes = explode(',', $item['recycle_for']);
      $chance = floor(100 / $item['recycle_fraction']);
      
      if($chance < 100)
        $chance = ceil($chance * 0.9);
      
      foreach($makes as $itemname)
      {
        if(mt_rand(1, 100) < $chance)
        {
          $items_received[$itemname]++;
          add_inventory_cached($user['user'], 'u:' . $user['idnum'], $itemname, 'Unprepared with a ' . $this_inventory['itemname'], $item['location']);
        }
      }
    }

    process_cached_inventory();
    
    echo '<p>Strange sounds echo around ' . $location_desc . '... something has happened!</p>';
  }
  else
    echo '<p>There\'s an uncanny silence... did it even do anything?</p>';

  record_stat($user['idnum'], 'Detonated a ' . $this_inventory['itemname'], 1);
  record_stat($user['idnum'], 'Detonated a Detonator', 1);

  $AGAIN_WITH_ANOTHER = true;
}
else
{
  echo '
    <p>Will you detonate the ' . $this_inventory['itemname'] . ' in ' . $location_desc . '?  It sounds kind of dangerous...</p>
    <ul>
     <li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&step=2">Danger is my mother\'s maiden name.  (They say I take after her.)</a></li>
    </ul>
  ';
}
?>
