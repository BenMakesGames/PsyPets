<?php
if($okay_to_be_here !== true)
  exit();

$inventory = get_inventory_byuser($user['user'], $this_inventory['location']);

$ACCEPTABLE_ITEMS = array(
  'Flour', 'Egg', 'Speckled Egg', 'Yeast', 'Sugar', 'Brown Sugar', 'Baking Chocolate',
  'Sour Cream', 'Butter', 'Shortening', 'Cream Cheese', 'Cream', 'Milk',
  'Wild Oats', 'Orange', 'Sour Lime'
);

$BAKES = array(
  'Brownie', 'Brownie Cheesecake Squares', 'Chocolate Bread',
  'Green Cracker', 'Shortbread', 'Soramimi Cake',
  'Sugar Cookies', 'Wheat Bread', 'White Bread', 'Surprise Dish'
);  

    foreach($BAKES as $bake)
    {
      $details = get_item_byname($bake);
      if($details === false)
      {
        echo '<p>Error for Ben to fix:  "' . $bake . '" is not an item!</p>';
        exit();
      }
    }

    foreach($ACCEPTABLE_ITEMS as $bake)
    {
      $details = get_item_byname($bake);
      if($details === false)
      {
        echo '<p>Error for Ben to fix:  "' . $bake . '" is not an item!</p>';
        exit();
      }
    }

foreach($inventory as $item)
{
  if(in_array($item['itemname'], $ACCEPTABLE_ITEMS))
    $items[$item['idnum']] = $item;
}

if($_POST['action'] == 'prepare')
{
  $idnums = array();
  $item_names = array();

  $new_list = $items;

  foreach($_POST as $key=>$value)
  {
    $id = (int)$key;
    if(($value == 'yes' || $value == 'on') && $id > 0 && array_key_exists($id, $items))
    {
      $idnums[] = $id;
      $item_names[$items[$id]['itemname']]++;
      
      unset($new_list[$id]);
    }
  }
  
  if(count($idnums) > 0)
  {
    $meal_size = 0;
    
    foreach($item_names as $itemname=>$quantity)
    {
      $details = get_item_byname($itemname);
      $meal_size += ($details['ediblefood'] + $details['ediblesafety'] + $details['ediblelove'] + $details['edibleesteem']) * $quantity;
    }
  
    $possibilities = array();
  
    foreach($BAKES as $bake)
    {
      $details = get_item_byname($bake);
      $bake_size = $details['ediblefood'] + $details['ediblesafety'] + $details['ediblelove'] + $details['edibleesteem']; 

      if($bake_size >= $meal_size - 2 && $bake_size <= $meal_size + 2)
        $possibilities[$bake] = 1;
      else if($bake_size * 2 >= $meal_size - 1 && $bake_size * 2 <= $meal_size + 1)
        $possibilities[$bake] = 2;
      else if($bake_size * 3 == $meal_size)
        $possibilities[$bake] = 3;
      else if($meal_size > $bake_size * 2)
        $too_much++;
      else if($meal_size < $bake_size)
        $too_little++;
    }
    
    if(count($possibilities) == 0)
    {
      if($too_little > $too_much)
        $messages[] = '<span class="failure">You\'ll need to put in more than that!</span>';
      else
        $messages[] = '<span class="failure">All that stuff cannot possibly fit into the Easy-Cake Oven at once!</span>';
    }
    else
    {
      $makes = array_rand($possibilities);
      $quantity = $possibilities[$makes];

      $command = 'DELETE FROM monster_inventory WHERE idnum IN (' . implode(',', $idnums) . ') LIMIT ' . count($idnums);
      $database->FetchNone($command, 'deleting ingredients');
      
      for($x = 0; $x < $quantity; $x++)
        add_inventory($user['user'], '', $makes, 'Baked in ' . $user['display'] . '\'s Easy-Cake Oven', $this_inventory['location']);

      if($quantity == 1)
        $messages[] = '<span class="success">You slide the items in... not moments later, you are presented with hot ' . $makes . '!</span>';
      else
        $messages[] = '<span class="success">You slide the items in... not moments later, you are presented with hot ' . $makes . '! ' . $quantity . ' of them!</span>';

      $items = $new_list;
    }     
  }
  else
    $messages[] = '<span class="failure">You need to select a few items to put in.</span>';
}

if(count($messages) > 0)
  echo '<ul><li>' . implode('</li><li>', $messages) . '</li></ul></p><p>';
?>
Which items will you put into the <?= $this_inventory['itemname'] ?>?</p>
<?php
if(count($items) > 0)
{
?>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
<table>
<?php
  $rowclass = begin_row_class();

  foreach($items as $idnum=>$item)
  {
    $details = get_item_byname($item['itemname']);
?>
 <tr class="<?= $rowclass ?>">
  <td><input type="checkbox" name="<?= $idnum ?>" /></td>
  <td class="centered"><img src="gfx/items/<?= $details['graphic'] ?>" alt="" /></td>
  <td><?= $item['itemname'] ?></td>
 </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
</table>
<p><input type="hidden" name="action" value="prepare" /><input type="submit" value="Bake!" /></p>
</form>
<?php
}
else
  echo '<p><i>None of the items in this room are suitable for use in the ' . $this_inventory['itemname'] . '...</i>';
?>
