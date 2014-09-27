<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/itemlib.php';

if(substr($this_inventory['location'], 0, 4) != 'home')
  echo '<p>You cannot use the ' . $this_inventory['itemname'] . ' away from home.</p>';
else
{
  $quantity = (int)$_POST['quantity'];
  $errored = false;
  $max_quantity = 0;

  $myhouse = get_inventory_byuser($user['user'], $this_inventory['location']);

  foreach($myhouse as $item)
  {
    if($item['itemname'] == 'Dirty Linen')
      $max_quantity++;
  }

  if($quantity > 0 && $max_quantity > 0)
  {
    if($quantity > $max_quantity)
      $quantity = $max_quantity;
  
    delete_inventory_byname($user['user'], 'Dirty Linen', $quantity, $this_inventory['location']);

    add_inventory_quantity($user['user'], 'u:' . $user['idnum'], 'White Cloth', 'Cleaned with ' . $this_inventory['itemname'], $this_inventory['location'], $quantity);

    $max_quantity -= $quantity;

    echo '<p class="success">You have washed ' . $quantity . '&times; Dirty Linen into White Cloth!<p>';

    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Dirty Linens Washed', $quantity);

    if($_POST['whee'] == 'yes' || $_POST['whee'] == 'on')
      record_stat($user['idnum'], 'Washing Machine Rides Taken', 1);

    $RECOUNT_INVENTORY = true;
  }

  if($max_quantity > 0)
  {
?>
<p>You have <?= $max_quantity ?> Dirty Linen in your house.  How many would you like to wash?</p>
<form action="itemaction.php?idnum=<?= $_GET['idnum'] ?>" method="post">
<table>
 <tr>
  <th>Dirty Linens:</th>
  <td style="padding-right:3em;"><input name="quantity" size="3" maxlength="<?= strlen($max_quantity) ?>" /></td>
  <td><input type="checkbox" name="whee" /></td>
  <td>Sit on the washing machine while it runs</td>
 </tr>
 <tr>
  <td colspan="4"><input type="submit" value="Wash" /></td>
 </tr>
</table>
</form>
<?php
  }
  else
    echo '<p>You have no Dirty Linens to wash.</p>';
} // you're at home
?>
