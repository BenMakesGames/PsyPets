<?php
require_once 'commons/itemlib.php';

if($okay_to_be_here !== true)
  exit();

$quantity = (int)$_POST['quantity'];
$spicy_jerky = 0;

$myhouse = get_inventory_byuser($user['user'], $this_inventory['location']);

foreach($myhouse as $item)
{
  if($item['itemname'] == 'Spicy Jerky')
    $spicy_jerky++;
}

if($quantity > 0)
{
  if($spicy_jerky >= $quantity)
  {
    delete_inventory_byname($user['user'], 'Spicy Jerky', $quantity, $this_inventory['location']);

    for($i = 0; $i < $quantity; ++$i)
      add_inventory($user['user'], 'u:' . $user['idnum'], 'Jerky', 'Despicified Spicy Jerky', $this_inventory['location']);

    $message = '<span class="success">Success!  Unfortunately, the Fire Spice is irrecoverable.</span>';

    $spicy_jerky -= $quantity;

    $RECOUNT_INVENTORY = true;
  }
  else
    $message = '<span class="failure">You do not have that much Spicy Jerky.</span>';
}

if($message)
  echo $message . '</p><p>';

if($spicy_jerky > 0)
{
?>
You have <?= $spicy_jerky ?> piece<?= $spicy_jerky != 1 ? 's' : '' ?> of Spicy Jerky available.</p>
<p>How many would you like to despicify?</p>
<p><form action="itemaction.php?idnum=<?= $_GET['idnum'] ?>" method="post">
<input name="quantity" size="3" maxlength="3" /> <input type="submit" value="Despicify" />
</form>
<?php
}
else
  echo "You do not have any Spicy Jerky in this room.";
?>
