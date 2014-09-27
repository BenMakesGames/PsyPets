<?php
if($okay_to_be_here !== true)
  exit();

$acceptable_items = array(
  'Sigil of Water' => 4,
  'Carafe of Water' => 40,
  'Holy Water' => 90,
  'Water Cooler' => 15,
  'Wand of Water' => 5,
  'Watermelon' => 3,
  'Crown of Water' => 1,
);

if($_POST['action'] == 'Perform')
{
  $itemid = (int)$_POST['itemid'];
  
  $item = get_inventory_byid($itemid);

  if(array_key_exists($item['itemname'], $acceptable_items) &&
    $item['user'] == $user['user'] && $item['location'] == $this_inventory['location'])
  {
    delete_inventory_byid($itemid);
    delete_inventory_byid($this_inventory['idnum']);
    
    $quantity = $acceptable_items[$item['itemname']];
    $quantity = mt_rand(ceil($quantity * .8), floor($quantity * 1.2));
    
    $wines = array(
      'Blueberry Wine', 'Redsberry Wine', 'Blueberry Wine', 'Redsberry Wine', 'Sake', 'Honey Wine',
    );
    
    $wine = $wines[array_rand($wines)];
    
    if($quantity == 1)
      echo '<p>The ' . $item['itemname'] . ' is transformed into wine!  ' . $wine . ', to be exact!</p>';
    else
      echo '<p>The ' . $item['itemname'] . ' is transformed into ' . $quantity . ' wine!  ' . $quantity . ' ' . $wine . ', to be exact!</p>';

    add_inventory_quantity($user['user'], 'u:' . $user['idnum'], $wine, 'Transformed from ' . $item['itemname'] , $this_inventory['location'], $quantity);

    $AGAIN_WITH_ANOTHER = true;

    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Performed a Small Miracle', 1);
  }
}

if(!$AGAIN_WITH_ANOTHER)
{
  $inventory = get_inventory_byuser($user['user'], $this_inventory['location']);

  $items = 0;
  $rowclass = begin_row_class();
  $previous = '';
  $count = 0;

  foreach($inventory as $item)
  {
    if($item['idnum'] != $this_inventory['idnum'])
    {
      $details = get_item_byname($item['itemname']);

      if(array_key_exists($item['itemname'], $acceptable_items))
      {
        $items++;
        $count++;

        if($items == 1)
        {
          echo '
            <p>What will you perform a Small Miracle on?</p>
            <p><i>(The quantity listed is the number available in your house, not the number you will use.  You will always only use 1 item.)</i></p>
            <form action="itemaction.php?idnum=' . $this_inventory['idnum'] . '" method="post">
            <table>
            <tr class="titlerow">
            <th></th><th></th><th>Item</th><th>Quantity</th>
            </tr>
          ';

          $count--;
        }
        else if($previous != $item['itemname'])
        {
?>
  <td class="centered"><?= $count ?></td>
 </tr>
<?php
          $count = 0;
        }
        else
          continue;
?>
 <tr class="<?= $rowclass ?>">
  <td><input type="radio" name="itemid" value="<?= $item['idnum'] ?>" /></td><td class="centered"><img src="gfx/items/<?= $details['graphic'] ?>" alt="" /></td><td><?= $item['itemname'] ?></td>
<?php
        $rowclass = alt_row_class($rowclass);

        $previous = $item['itemname'];
      }
    }
  }

  if($items > 0)
  {
    echo ' <td class="centered">' . ($count + 1) . '</td></tr>' .
         '</table>' .
         '<p><input type="submit" name="action" value="Perform" /></p></form>';
  }
  else
    echo '<p>There are no items here to perform a small miracle on.</p>';
}
?>
