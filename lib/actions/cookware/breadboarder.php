<?php
require_once 'commons/itemlib.php';

if($okay_to_be_here !== true)
  exit();

$quantity = (int)$_POST['quantity'];

$myhouse = get_inventory_byuser($user['user'], $this_inventory['location']);

foreach($myhouse as $item)
{
  if($item['itemname'] == 'Plastic')
    $plastic++;
  else if($item['itemname'] == 'Copper')
    $copper++;

  if($plastic > 0 && $copper > 0)
  {
    $plastic--;
    $copper--;
    $simple_circuit++;
  }
}

$total = $simple_circuit;

if($quantity > 0)
{
  if($total >= $quantity)
  {
    delete_inventory_byname($user['user'], 'Plastic', $quantity, $this_inventory['location']);
    delete_inventory_byname($user['user'], 'Copper', $quantity, $this_inventory['location']);

    for($i = 0; $i < $quantity; ++$i)
      add_inventory($user['user'], 'u:' . $user['idnum'], 'Simple Circuit', 'Made with ' . $this_inventory['itemname'], $this_inventory['location']);

    if($quantity == 1)
      $descript = 'a Simple Circuit.';
    else if($quantity < 5)
      $descript = 'a few Simple Circuits.';
    else if($quantity < 11)
      $descript = 'some Simple Circuits.';
    else if($quantity < 31)
      $descript = 'a lot of Simple Circuits!';
    else
      $descript = '<em>so many</em> Simple Circuits!  You really have.  Wow.';

    $message = "You have pressed $descript";

    $total -= $quantity;

    $RECOUNT_INVENTORY = true;
  }
  else
    $error_message = 'You do not have enough Plastic and/or Copper to make that many.';
}

if($message)
  echo "<font class=\"success\">$message</font></p>\n<p>";

if($error_message)
  echo "<font class=\"failure\">$error_message</font></p>\n<p>";

if($total > 0)
{
?>
<p>You have enough Plastic and Copper to make <?= $total ?> Simple Circuit<?= $total != 1 ? 's' : '' ?>.</p>
<p>How many would you like to press?</p>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
<p><input name="quantity" size="4" maxlength="<?= strlen($total) ?>" /> <input type="submit" value="Press" /></p>
</form>
<?php
}
else
  echo '<p>You do not have enough Plastic and/or Copper in this room.</p>';
?>
