<?php
require_once 'commons/itemlib.php';

if($okay_to_be_here !== true)
  exit();

$quantity = (int)$_POST['quantity'];

$data = $database->FetchSingle('
	SELECT COUNT(idnum) AS qty
	FROM monster_inventory
	WHERE
		user=' . $database->Quote($user['user']) . '
		AND itemname=\'Fluff\'
		AND location=' . $database->Quote($this_inventory['location']) . '
');
$fluff = $data['qty'];

if($quantity > 0)
{
  if($fluff >= $quantity * 2)
  {
    delete_inventory_byname($user["user"], 'Fluff', $quantity * 2, $this_inventory['location']);

    add_inventory_quantity($user["user"], 'u:' . $user['idnum'], 'Stringy Rope', 'Spun from Fluff', $this_inventory['location'], $quantity);

    $message = '<span class="success">It\'s a lot of work, but you finally spin ' . ($quantity * 2) . ' Fluff into ' . $quantity . ' Stringy Rope!</span>';

    $fluff -= $quantity * 2;

    $RECOUNT_INVENTORY = true;
  }
  else
    $message = '<span class="failure">You do not have that much Fluff.</span>';
}

if($message)
  echo '<p>' . $message . '</p>';

if($fluff > 1)
{
?>
<p>You have <?= $fluff ?> Fluff available.  You can spin a Stringy Rope from two Fluff.  How many would you like to spin?</p>
<form method="post">
<p><input name="quantity" type="number" size="2" maxlength="2" /> <input type="submit" value="Spin" /></p>
</form>
<?php
}
else
  echo '<p>You could spin a Stringy Rope from two Fluff, but you don\'t even have two Fluff.</p>';
?>
