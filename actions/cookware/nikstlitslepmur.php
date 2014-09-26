<?php
require_once 'commons/itemlib.php';

if($okay_to_be_here !== true)
  exit();

$quantity = (int)$_POST['quantity'];

$myhouse = get_inventory_byuser($user['user'], $this_inventory['location']);

$data = $database->FetchSingle('
	SELECT COUNT(idnum) AS qty
	FROM monster_inventory
	WHERE
		user=' . $database->Quote($user['user']) . '
		AND itemname=\'Gold\'
		AND location=' . $database->Quote($this_inventory['location']) . '
');
$gold = $data['qty'];

if($quantity > 0)
{
  if($gold >= $quantity)
  {
    delete_inventory_byname($user["user"], 'Gold', $quantity, $this_inventory['location']);

    for($i = 0; $i < $quantity; ++$i)
      add_inventory($user["user"], 'u:' . $user['idnum'], 'Wheat', 'Spun from Gold', $this_inventory['location']);

    $message = '<span class="success">It\'s a lot of work, but you finally spin ' . $quantity . ' Gold into Wheat!</span>';

    $gold -= $quantity;

    $RECOUNT_INVENTORY = true;
  }
  else
    $message = '<span class="failure">You do not have that much Gold.</span>';
}

if($message)
  echo $message . '</p><p>';

if($gold > 0)
{
?>
<p>You have <?= $gold ?> Gold available, which the <?= $this_inventory['itemname'] ?> seems eager to... spin?  How many would you like to spin?</p>
<form method="post">
<p><input name="quantity" size="2" maxlength="2" /> <input type="submit" value="Spin" /></p>
</form>
<?php
}
else
  echo '<p>It spins.  Uselessly.</p><p>Hm...</p>';
?>
