<?php
if($okay_to_be_here !== true)
  exit();

$CLOTH_TYPES = array(
  'Black Cloth',
  'Blue Cloth',
  'Dirty Linen',
  'Green Cloth',
  'Leopard Print Cloth',
  'Orange Cloth',
  'Purple Cloth',
  'Red Cloth',
  'Red-striped Cloth',
  'Stripy Cloth',
  'Yellow Cloth',
  'Speckled Egg',
  'Red Wood',
  'Dark Wood',
);

$items = $database->FetchMultipleBy('
  SELECT COUNT(idnum) AS qty,itemname
  FROM monster_inventory
  WHERE
    user=' . quote_smart($user['user']) . '
    AND location=' . quote_smart($this_inventory['location']) . '
    AND itemname IN (\'' . implode('\', \'', $CLOTH_TYPES) . '\')
  GROUP BY itemname
', 'itemname');

$delete_me = false;
$bleached = 0;

$uses = ceil($this_inventory['health'] / 10);

if($_POST['action'] == 'bleach')
{
  foreach($_POST as $key=>$value)
  {
    if($value > 0 && substr($key, 0, 6) == 'cloth_')
    {
      $quantity = (int)$value;
      $cid = substr($key, 6);

      if(array_key_exists($cid, $CLOTH_TYPES))
      {
        $cloth = $CLOTH_TYPES[$cid];

        if($quantity > $items[$cloth]['qty'])
          $quantity = $items[$cloth]['qty'];

        if($quantity > $uses)
          $quantity = $uses;
          
        if($quantity == 0)
          break;

        if($cloth == 'Speckled Egg')
        {
          $new_item = 'Egg';
          $egg_count += $quantity;
        }
        else if($cloth == 'Red Wood' || $cloth == 'Dark Wood')
        {
          $new_item = 'Wood';
          $wood_count += $quantity;
        }
        else
        {
          $new_item = 'White Cloth';
          $cloth_count += $quantity;
        }

        $database->FetchNone('
          UPDATE monster_inventory
          SET
            itemname=' . quote_smart($new_item) . ',
            changed=' . $now . '
          WHERE 
            user=' . quote_smart($user['user']) . '
            AND itemname=' . quote_smart($cloth) . '
            AND location=' . quote_smart($this_inventory['location']) . '
          LIMIT ' . $quantity . '
        ');

        $affected = $database->AffectedRows();
        $uses -= $affected;
        $bleached += $affected;

        if($affected == $items[$cloth]['qty'])
          unset($items[$cloth]);
        else
          $items[$cloth]['qty'] -= $affected;
      }
    }
  }

  if($uses <= 0)
  {
    delete_inventory_byid($this_inventory['idnum']);
    $delete_me = true;
  }
  else
  {
    $database->FetchNone('UPDATE monster_inventory SET health=' . ($uses * 10) . ' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1');
  }

  if($bleached > 0)
  {
    $item_list = array();

    if($cloth_count > 0)
      $item_list[] = $cloth_count . ' cloth';
    if($egg_count > 0)
      $item_list[] = $egg_count . ' egg' . ($egg_count != 1 ? 's' : '');
    if($wood_count > 0)
      $item_list[] = $wood_count . ' wood';

    echo '<p class="success">Bleached ' . list_nice($item_list) . '.</p>';
  }
  else
    echo '<p class="failure">No items were bleached.</p>';
}

if($delete_me === false)
{
?>
<p>Which items in this room will you bleach?  This bottle has enough bleach for <?= $uses ?> items.</p>
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
      $cid = array_search($item['itemname'], $CLOTH_TYPES);
?>
 <tr class="<?= $rowclass ?>">
  <td><input type="number" name="cloth_<?= $cid ?>" size="3" maxlength="<?= strlen($item['qty']) ?>" min="0" max="<?= $item['qty'] ?>" /> / <?= $item['qty'] ?></td>
  <td class="centered"><img src="<?= $SETTINGS['protocol'] ?>://saffron.psypets.net/gfx/items/<?= $details['graphic'] ?>" alt="" /></td>
  <td><?= $item['itemname'] ?></td>
 </tr>
<?php
      $rowclass = alt_row_class($rowclass);
    }
?>
</table>
<p><input type="hidden" name="action" value="bleach" /><input type="submit" value="Bleach" /></p>
<?php
  }
  else
    echo '<p>There are no items you can bleach in this room.</p>';
?>
</form>
<?php
}
else
{
?>
<p>The bleach has run out!  Unfortunate.</p>
<?php
  $AGAIN_WITH_ANOTHER = true;
}
?>
