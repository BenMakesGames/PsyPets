<?php
require_once 'commons/itemlib.php';

if($okay_to_be_here !== true)
  exit();

$quantity = (int)$_POST['quantity'];

$command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=' . quote_smart($this_inventory['location']) . ' AND itemname=\'Speckled Egg\'';
$data = $database->FetchSingle($command, 'fetching speckled egg count');
$egg = (int)$data['c'];

if($quantity > 0 && $quantity <= $egg)
{
  delete_inventory_byname($user['user'], 'Speckled Egg', $quantity, $this_inventory['location']);

  add_inventory_quantity($user['user'], 'u:' . $user['idnum'], 'Egg', 'Despeckled by an Egg Despeckler', $this_inventory['location'], $quantity);
  add_inventory_quantity($user['user'], 'u:' . $user['idnum'], 'Speckles', 'Byproduct of despecklizing process', $this_inventory['location'], $quantity);

  $message = 'You have successfully despeckled ' . $quantity . ' Speckled Egg' . ($quantity != 1 ? 's' : '') . '.';

  $egg -= $quantity;

  $RECOUNT_INVENTORY = true;
}

if($message)
  echo "<font style=\"color:green;\">$message</font></p>\n<p>";

if($egg > 0)
{
?>
You have <?= $egg ?> Speckled Egg<?= $egg != 1 ? 's' : '' ?> available.</p>
<p>How many would you like to despeckle?</p>
<form action="itemaction.php?idnum=<?= $_GET['idnum'] ?>" method="post">
<p><input name="quantity" size="2" maxlength="2" /> <input type="submit" value="Process" /></p>
</form>
<?php
}
else
  echo 'You do not have any Speckled Eggs in this room.';
?>
