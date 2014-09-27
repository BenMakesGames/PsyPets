<?php
if($okay_to_be_here !== true)
  exit();

$damage_per_use = 2;
  
$PLASTIC_TYPES = array(
  'Plastic',
  'White Pawn', 'White Bishop', 'White King', 'White Queen', 'White Rook', 'White Knight',
  'Black Pawn', 'Black Bishop', 'Black King', 'Black Queen', 'Black Rook', 'Black Knight',
  'Eyes', 'Arms', 'Mouth',
  '1 Circle', '2 Circle', '3 Circle', '4 Circle', '5 Circle', '6 Circle', '7 Circle', '8 Circle', '9 Circle',
  '1 Bamboo', '2 Bamboo', '3 Bamboo', '4 Bamboo', '5 Bamboo', '6 Bamboo', '7 Bamboo', '8 Bamboo', '9 Bamboo',
  '1 Character', '2 Character', '3 Character', '4 Character', '5 Character', '6 Character', '7 Character', '8 Character', '9 Character',
  'East Wind', 'South Wind', 'West Wind', 'North Wind',
  'Red Dragon', 'Green Dragon', 'White Dragon',
  'Plum Flower', 'Orchid Flower', 'Chrysanthemum Flower', 'Bamboo Flower',
  'Spring Season', 'Summer Season', 'Autumn Season', 'Winter Season',
  'Audio CD', 'Laserdisc',
  'Cheap Sculpting Knife',
  'Protractor',
);

$items = $database->FetchMultipleBy('
  SELECT COUNT(idnum) AS qty,itemname
  FROM monster_inventory
  WHERE
    user=' . quote_smart($user['user']) . '
    AND location=' . quote_smart($this_inventory['location']) . '
    AND itemname IN (\'' . implode('\', \'', $PLASTIC_TYPES) . '\')
  GROUP BY itemname
', 'itemname');

$delete_me = false;
$softened = 0;

$uses = ceil($this_inventory['health'] / $damage_per_use);

if($_POST['action'] == 'soften')
{
  foreach($_POST as $key=>$value)
  {
    if($value > 0 && substr($key, 0, 5) == 'item_')
    {
      $quantity = (int)$value;
      $cid = substr($key, 5);

      if(array_key_exists($cid, $PLASTIC_TYPES))
      {
        $plastic = $PLASTIC_TYPES[$cid];

        // don't try to use more of an item than we have
        if($quantity > $items[$plastic]['qty'])
          $quantity = $items[$plastic]['qty'];

        // don't try to use more uses than the can has left
        if($quantity > $uses)
          $quantity = $uses;

        if($quantity == 0)
          break;

        $new_item = 'Rubber';
        $rubber_count += $quantity;

        $database->FetchNone('
          UPDATE monster_inventory
          SET
            itemname=' . quote_smart($new_item) . ',
            changed=' . $now . '
          WHERE 
            user=' . quote_smart($user['user']) . '
            AND itemname=' . quote_smart($plastic) . '
            AND location=' . quote_smart($this_inventory['location']) . '
          LIMIT ' . $quantity . '
        ');

        $affected = $database->AffectedRows();
        $uses -= $affected;
        $softened += $affected;

        // reduce known quantity of inventory on-hand
        if($affected == $items[$plastic]['qty'])
          unset($items[$plastic]);
        else
          $items[$plastic]['qty'] -= $affected;
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
    $database->FetchNone('UPDATE monster_inventory SET health=' . ($uses * $damage_per_use) . ' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1');
  }

  if($softened > 0)
  {
    $item_list = array();

    if($rubber_count > 0)
      $item_list[] = $rubber_count . ' rubber';

    echo '<p class="success">Softened ' . list_nice($item_list) . '.</p>';
  }
  else
    echo '<p class="failure">No items were softened.</p>';
}

if($delete_me === false)
{
?>
<p>How many plastics in this room will you soften?  This <?= $this_inventory['itemname'] ?> has <?= $uses ?> more uses in it.</p>
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
      $cid = array_search($item['itemname'], $PLASTIC_TYPES);
?>
 <tr class="<?= $rowclass ?>">
  <td><input type="number" name="item_<?= $cid ?>" size="3" maxlength="<?= strlen($item['qty']) ?>" min="0" max="<?= $item['qty'] ?>" /> / <?= $item['qty'] ?></td>
  <td class="centered"><img src="<?= $SETTINGS['protocol'] ?>://saffron.psypets.net/gfx/items/<?= $details['graphic'] ?>" alt="" /></td>
  <td><?= $item['itemname'] ?></td>
 </tr>
<?php
      $rowclass = alt_row_class($rowclass);
    }
?>
</table>
<p><input type="hidden" name="action" value="soften" /><input type="submit" value="Soften" /></p>
<?php
  }
  else
    echo '<p>There are no items you can soften in this room.</p>';
?>
</form>
<?php
}
else
{
?>
<p>The spray has run out!  Disappointing.</p>
<?php
  $AGAIN_WITH_ANOTHER = true;
}
?>
