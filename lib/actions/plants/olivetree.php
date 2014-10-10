<?php
if($okay_to_be_here !== true)
  exit();

$FERTILIZER_TYPES = array(
	'Liquid Nitrogen',
);

$items = $database->FetchMultipleBy('
  SELECT COUNT(idnum) AS qty,itemname
  FROM monster_inventory
  WHERE
    user=' . quote_smart($user['user']) . '
    AND location=' . quote_smart($this_inventory['location']) . '
    AND itemname IN (\'' . implode('\', \'', $FERTILIZER_TYPES) . '\')
  GROUP BY itemname
', 'itemname');

$fertilizer = 0;

if($_POST['action'] == 'feed')
{
  foreach($_POST as $key=>$value)
  {
    if($value > 0 && substr($key, 0, 11) == 'fertilizer_')
    {
      $quantity = (int)$value;
      $cid = substr($key, 11);

      if(array_key_exists($cid, $FERTILIZER_TYPES))
      {
        $itemname = $FERTILIZER_TYPES[$cid];

        if($quantity > $items[$itemname]['qty'])
          $quantity = $items[$itemname]['qty'];
          
        if($quantity == 0)
          break;

        if($itemname == 'Liquid Nitrogen')
					$multiplier = 11;
				
				$database->FetchNone('
					DELETE FROM monster_inventory
					WHERE 
						user=' . quote_smart($user['user']) . '
						AND itemname=' . quote_smart($itemname) . '
						AND location=' . quote_smart($this_inventory['location']) . '
					LIMIT ' . $quantity . '
				');
					
				$affected = $database->AffectedRows();

				if($affected == $items[$itemname]['qty'])
					unset($items[$itemname]);
				else
					$items[$itemname]['qty'] -= $affected;

				add_inventory_quantity($user['user'], '', 'Olives', '', $this_inventory['location'], $affected * $multiplier);
				
				$olive_count += $affected * $multiplier;
      }
    }
  }

  if($olive_count > 0)
  {
    $item_list[] = $olive_count . ' Olives';

    echo '<p class="success">Received ' . list_nice($item_list) . '.</p>';
  }
  else
    echo '<p class="failure">No Olives were produced.</p>';
}
else if($_POST['action'] == 'tap')
{
	$tapped = true;
	$AGAIN_WITH_ANOTHER = true;
	
	$database->FetchNone('UPDATE monster_inventory SET itemname=\'Tapped Olive Tree\' WHERE idnum=' . (int)$this_inventory['idnum'] . ' LIMIT 1');
}

if(!$tapped)
{
?>
<p>This tree is in need of Nitrogen!  What will you give it?</p>
<form method="post">
<?php
	if(count($items) > 0)
	{
?>
<table>
 <tr class="titlerow">
  <th class="centered">Quantity</th>
  <th></th>
  <th>Item</th>
 </tr>
<?php
		$rowclass = begin_row_class();

		foreach($items as $item)
		{
			$details = get_item_byname($item['itemname']);
			$cid = array_search($item['itemname'], $FERTILIZER_TYPES);
?>
 <tr class="<?= $rowclass ?>">
  <td><input type="number" name="fertilizer_<?= $cid ?>" size="3" maxlength="<?= strlen($item['qty']) ?>" min="0" max="<?= $item['qty'] ?>" /> / <?= $item['qty'] ?></td>
  <td class="centered"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/<?= $details['graphic'] ?>" alt="" /></td>
  <td><?= $item['itemname'] ?></td>
 </tr>
<?php
			$rowclass = alt_row_class($rowclass);
		}
?>
</table>
<p><input type="hidden" name="action" value="feed" /><input type="submit" value="Fertilize" /></p>
<?php
	}
	else
		echo '<p>There are no Nitrogen-rich items in this room.</p>';
?>
</form>
<h5>Olive Trees Have Sap?</h5>
<p>Would you like to tap the <?= $this_inventory['itemname'] ?>?</p>
<form method="post">
<p><input type="hidden" name="action" value="tap"><input type="submit" value="Defs" /></p>
</form>
<?php
}
?>