<?php
if($okay_to_be_here !== true)
  exit();

$command = '
  SELECT a.itemname,b.graphictype,b.graphic,b.recycle_for,b.recycle_fraction,COUNT(a.idnum) AS qty
  FROM monster_inventory AS a LEFT JOIN monster_items AS b
  ON a.itemname=b.itemname
  WHERE
    a.user=' . quote_smart($user['user']) . ' AND
    a.location=' . quote_smart($this_inventory['location']) . ' AND
    b.is_edible=\'yes\' AND
    b.recycle_for!=\'\'
  GROUP BY(itemname)
  ORDER BY itemname ASC
';
$items = $database->FetchMultipleBy($command, 'itemname', 'fetching prepared numnums');

$did_it = false;

if($_POST['action'] == '!vvvwvwvwB')
{
  $process = array();

  foreach($_POST as $key=>$value)
  {
    if(substr($key, 0, 2) == 'i_')
    {
      $itemname = itemname_from_form_value(substr($key, 2));
      if(array_key_exists($itemname, $items))
      {
        $quantity = (int)$value;
        if($quantity > 0)
        {
          if($quantity > $items[$itemname]['qty'])
            $use_messages[] = '<span class="failure">You don\'t have ' . $quantity . ' "' . $itemname . '" in this room.</span>';
          else
            $process[$itemname] = max($process[$itemname], $quantity);
        }
          
      }
      else
        $use_messages[] = '<span class="failure">You don\'t have a "' . $itemname . '" in this room.  Not even one.</span>';
    }
  }

  if(count($process) > 0)
  {
    $items_received = array();
  
    foreach($process as $itemname=>$quantity)
    {
      $deleted = delete_inventory_byname($user['user'], $itemname, $quantity, $this_inventory['location']);
      $quantity = $deleted;
      
      if($quantity == 0)
        continue;
    
      $makes = explode(',', $items[$itemname]['recycle_for']);
      $chance = floor(100 / $items[$itemname]['recycle_fraction']);
      
      if($chance < 100)
        $chance = ceil($chance * 0.9);
      
      for($x = 0; $x < $quantity; ++$x)
      {
        foreach($makes as $item)
        {
          if(mt_rand(1, 100) < $chance)
          {
            $items_received[$item]++;
            add_inventory_cached($user['user'], 'u:' . $user['idnum'], $item, 'Unprepared with a ' . $this_inventory['itemname'], $this_inventory['location']);
          }
        }
      }
    }

    process_cached_inventory();

    if(count($items_received) > 0)
    {
      echo '<p>You unprepared the following items:</p><ul>';
      foreach($items_received as $itemname=>$quantity)
        echo '<li>' . $quantity . '&times; ' . $itemname . '</li>';
      echo '</ul>';
    }
    else
      echo '<p>Egk!  It looks like everything was ruined.  Sorry about that.  It can happen when unpreparing meals that are made from a batch.</p>';

    $did_it = true;
    $AGAIN_WITH_SAME = true;
  }
}

if(count($use_messages) > 0)
  echo '<ul><li>' . implode('</li><li>', $use_messages) . '</li></ul>';

if($did_it)
{
}
else
{
  if(count($items) == 0)
    echo '<p>You have no prepared foods in this room.</p>';
  else
  {
?>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
<table>
 <tr class="titlerow"><th colspan="2" class="centered">Quantity</th><th></th><th>Item</th></tr>
<?php
    $rowclass = begin_row_class();

    foreach($items as $item)
    {
?>
 <tr class="<?= $rowclass ?>">
  <td><input type="text" size="2" name="<?= itemname_to_form_value('i_' . $item['itemname']) ?>" maxlength="<?= strlen($item['qty']) ?>" /></td><td> / <?= $item['qty'] ?></td>
  <td class="centered"><?= item_display_extra($item) ?></td>
  <td><?= $item['itemname'] ?></td>
 </tr>
<?php
      $rowclass = alt_row_class($rowclass);
    }
?>
</table>
<p><input type="submit" name="action" value="!vvvwvwvwB" /> (the opposite of "Bwvwvwvvv!" - the sound of a Microwave)</p>
</form>
<?php
  }
}
?>
