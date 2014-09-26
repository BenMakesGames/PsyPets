<?php
require_once 'commons/itemlib.php';

if($okay_to_be_here !== true)
  exit();

$quantity = (int)$_POST['quantity'];
$raw_milk = 0;

$myhouse = get_inventory_byuser($user['user'], $this_inventory['location']);

foreach($myhouse as $item)
{
  if($item['itemname'] == 'Raw Milk')
    $raw_milk++;
}

if($quantity > 0)
{
  if($raw_milk >= $quantity)
  {
    delete_inventory_byname($user["user"], 'Raw Milk', $quantity, $this_inventory['location']);
    for($i = 0; $i < $quantity; ++$i)
    {
      add_inventory($user["user"], 'u:' . $user['idnum'], 'Milk', 'Separated with the ' . $this_inventory["itemname"], $this_inventory['location']);
      add_inventory($user["user"], 'u:' . $user['idnum'], 'Cream', 'Separated with the ' . $this_inventory["itemname"], $this_inventory['location']);
    }

    $message = '<span class="success">You separated out ' . $quantity . ' Milk and ' . $quantity . ' Cream!</span>';

    $raw_milk -= $quantity;

    $RECOUNT_INVENTORY = true;
  }
  else
    $message = '<span class="failure">You do not have that much Raw Milk.</span>';
}

if($message)
  echo $message . '</p><p>';

if($raw_milk > 0)
{
?>
You have <?= $raw_milk ?> bottle<?= $raw_milk != 1 ? 's' : '' ?> of Raw Milk available.</p>
<p>How many would you like to separate into Milk and Cream?</p>
<p><form action="itemaction.php?idnum=<?= $_GET['idnum'] ?>" method="post">
<input name="quantity" size="2" maxlength="2" /> <input type="submit" value="Separate" />
</form>
<?php
}
else
  echo "You do not have any Raw Milk in this room.";
?>
