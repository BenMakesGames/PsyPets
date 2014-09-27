<?php
if($okay_to_be_here !== true)
  exit();

if($this_item['durability'] > 0 && $this_inventory['health'] < $this_item['durability'] / 4)
{
  echo '<p>This ' . $this_inventory['itemname'] . ' is too dull to cut with.</p>';
}
else
{
  $PIZZA_TYPES = array(
    'Cheese Pizza',
    'Cheese Pizza Supreme',
    'Hamburger Pizza',
    'Meat Lover\'s Pizza',
    'Meat Lover\'s Pizza Supreme',
    'Meat Pizza',
    'Meat Pizza Supreme',
  );

  $items = $database->FetchMultipleBy('
    SELECT COUNT(idnum) AS qty,itemname
    FROM monster_inventory
    WHERE
      user=' . quote_smart($user['user']) . '
      AND location=' . quote_smart($this_inventory['location']) . '
      AND itemname IN (' . implode(',', quote_smart($PIZZA_TYPES)) . ')
    GROUP BY itemname
  ', 'itemname');

  $delete_me = false;
  $sliced = 0;

  if($_POST['action'] == 'slice')
  {
    foreach($_POST as $key=>$value)
    {
      if($value > 0 && substr($key, 0, 6) == 'pizza_')
      {
        $quantity = (int)$value;
        $pid = substr($key, 6);
        
        if(array_key_exists($pid, $PIZZA_TYPES))
        {
          $pizza = $PIZZA_TYPES[$pid];

          if($quantity > $items[$pizza]['qty'])
            $quantity = $items[$pizza]['qty'];
            
          if($quantity == 0)
            break;

          $database->FetchNone('
            DELETE FROM monster_inventory
            WHERE
              user=' . quote_smart($user['user']) . '
              AND itemname=' . quote_smart($pizza) . '
              AND location=' . quote_smart($this_inventory['location']) . '
            LIMIT ' . $quantity . '
          ');

          $affected = $database->AffectedRows();

          if($affected > 0)
          {
            add_inventory_quantity($user['user'], $this_inventory['creator'], 'Slice of ' . $pizza, '', $this_inventory['location'], $affected * 4);
          
            $sliced += $affected;

            if($affected == $items[$pizza]['qty'])
              unset($items[$pizza]);
            else
              $items[$pizza]['qty'] -= $affected;
          }
        }
      }
    }

    if($sliced > 0)
    {
      echo '<p class="success">Slice, slice, slice... you make ' . ($sliced * 4) . ' slices of pizza.</p>';
    }
    else
      echo '<p class="failure">No pizzas were sliced.</p>';
  }
?>
<p>Which pizza in this room will you slice?</p>
<?php
  if(count($items) > 0)
  {
?>
<form method="post">
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
      $pid = array_search($item['itemname'], $PIZZA_TYPES);
?>
 <tr class="<?= $rowclass ?>">
  <td><input type="number" name="pizza_<?= $pid ?>" size="3" maxlength="<?= strlen($item['qty']) ?>" min="0" max="<?= $item['qty'] ?>" /> / <?= $item['qty'] ?></td>
  <td class="centered"><img src="<?= $SETTINGS['protocol'] ?>://saffron.psypets.net/gfx/items/<?= $details['graphic'] ?>" alt="" /></td>
  <td><?= $item['itemname'] ?></td>
 </tr>
<?php
      $rowclass = alt_row_class($rowclass);
    }
?>
</table>
<p><input type="hidden" name="action" value="slice" /><input type="submit" value="Slice" /></p>
</form>
<?php
  }
  else
    echo '<p>There are no pizzas you can slice in this room.</p>';
} // has enough durability
?>
