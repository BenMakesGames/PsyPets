<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/paperlib.php';

$AGAIN_WITH_ANOTHER = true;

$command = 'SELECT itemname,COUNT(itemname) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname IN (\'Paper Airplane\', \'Paper Crane\', \'Paper Hat\', \'Paper Boat\') AND location=' . quote_smart($this_inventory['location']) . ' GROUP BY itemname'; 
$items = $database->FetchMultipleBy($command, 'itemname', 'fetching items');

if($_GET['step'] == 2 && $items !== false && count($items) > 0)
{
  $command = 'DELETE FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname IN (\'Paper Airplane\', \'Paper Crane\', \'Paper Hat\', \'Paper Boat\') AND location=' . quote_smart($this_inventory['location']);
  $database->FetchNone($command, 'deleting paper crafts');

  $treasures = array();
  $unfolded = 0;
  $tore = 0;

  foreach($items as $itemname=>$details)
  {
    $quantity = $details['c'];
    if($itemname == 'Paper Crane')
    {
      for($x = 0; $x < $quantity; ++$x)
      {
        $name = unfold_crane();
        if($name !== false)
        {
          $treasures[$name]++;
          add_inventory_cached($user['user'], '', $name, 'Unfolded from a Paper Crane.', $this_inventory['location']);
          $unfolded++;
        }
        else
          $tore++;
      }
    }
    else if($itemname == 'Paper Hat')
    {
      for($x = 0; $x < $quantity; ++$x)
      {
        $name = unfold_hat();
        if($name !== false)
        {
          $treasures[$name]++;
          add_inventory_cached($user['user'], '', $name, 'Unfolded from a Paper Hat.', $this_inventory['location']);
          $unfolded++;
        }
        else
          $tore++;
      }
    }
    else if($itemname == 'Paper Airplane')
    { 
      for($x = 0; $x < $quantity; ++$x)
      {
        $name = unfold_airplane();
        if($name !== false)
        {
          $treasures[$name]++;
          add_inventory_cached($user['user'], '', $name, 'Unfolded from a Paper Airplane.', $this_inventory['location']);
          $unfolded++;
        }
        else
          $tore++;
      }
    }
    else if($itemname == 'Paper Boat')
    { 
      for($x = 0; $x < $quantity; ++$x)
      {
        $name = unfold_boat();
        if($name !== false)
        {
          $treasures[$name]++;
          add_inventory_cached($user['user'], '', $name, 'Unfolded from a Paper Boat.', $this_inventory['location']);
          $unfolded++;
        }
        else
          $tore++;
      }
    }
  }

  process_cached_inventory();

  echo '<p>The ' . $this_inventory['itemname'] . ' hums and shakes... ';

  if(count($treasures) == 0)
    echo 'but in the end, you receive nothing!</p>';
  else
  {
    echo 'it eventually ejects the following items for your collection:</p><ul>';
    foreach($treasures as $itemname=>$quantity)
      echo '<li>' . $quantity . 'x ' . $itemname . '</li>';
    echo '</ul>';
  }    

  require_once 'commons/statlib.php';

  if($unfolded > 0)
    record_stat($user['idnum'], 'Unfolded a Piece of Paper', $unfolded);

  if($tore > 0)
    record_stat($user['idnum'], 'Tore a Piece of Paper', $tore);
}
else
{
  if($items === false || count($items) == 0)
    echo '<p>There are no Paper Hats, Cranes, Airplanes, or Boats to unfold in this room.</p>';
  else
  {
    echo '<p>This room contains:</p><ul>';
    foreach($items as $itemname=>$details)
      echo '<li>' . $details['c'] . 'x ' . $itemname . '</li>';
    echo '</ul><p>Do you want to process <em>every last one of them</em> using this ' . $this_inventory['itemname'] . '?</p><ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&step=2">Do I!?</a> <i>(Rhetorical.)</i></li></ul>';
  }
}
?>
