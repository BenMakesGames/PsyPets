<?php
require_once 'commons/itemlib.php';

if($okay_to_be_here !== true)
  exit();

$wheat_quantity = (int)$_POST['wheat'];
$rice_quantity = (int)$_POST['rice'];
$rye_quantity = (int)$_POST['rye'];
$wheat = 0;
$rice = 0;
$tools = 0;

$myhouse = get_inventory_byuser($user['user'], $this_inventory['location']);

$okay = false;

foreach($myhouse as $item)
{
  if($item['itemname'] == 'Wheat')
    $wheat++;
  else if($item['itemname'] == 'Rice')
    $rice++;
  else if($item['itemname'] == 'Rye')
    $rye++;
  else if($item['itemname'] == $this_inventory['itemname'])
    $tools++;
}

if($tools > 1)
{
  if($wheat_quantity > 0)
  {
    if($wheat >= $wheat_quantity)
    {
      $wheat_quantity = delete_inventory_byname($user['user'], 'Wheat', $wheat_quantity, $this_inventory['location']);
      add_inventory_quantity($user['user'], 'u:' . $user['idnum'], 'Flour', 'Ground with a ' . $this_inventory['itemname'], $this_inventory['location'], $wheat_quantity);

      $message .= '<p class="success">It\'s a lot of work, but you grind ' . $wheat_quantity . ' Wheat into Flour!</p>';

      $wheat -= $wheat_quantity;

      $RECOUNT_INVENTORY = true;
    }
    else
      $message .= '<p class="failure">You do not have that much Wheat.</p>';
  }

  if($rye_quantity > 0)
  {
    if($rye >= $rye_quantity)
    {
      $rye_quantity = delete_inventory_byname($user['user'], 'Rye', $rye_quantity, $this_inventory['location']);
      add_inventory_quantity($user['user'], 'u:' . $user['idnum'], 'Rye Flour', 'Ground with a ' . $this_inventory['itemname'], $this_inventory['location'], $rye_quantity);

      $message .= '<p class="success">It\'s a lot of work, but you grind ' . $rye_quantity . ' Rye into Rye Flour!</p>';

      $rye -= $rye_quantity;

      $RECOUNT_INVENTORY = true;
    }
    else
      $message .= '<p class="failure">You do not have that much Rye.</p>';
  }

  if($rice_quantity > 0)
  {
    if($rice >= $rice_quantity)
    {
      $rice_quantity = delete_inventory_byname($user['user'], 'Rice', $rice_quantity, $this_inventory['location']);
      add_inventory_quantity($user['user'], 'u:' . $user['idnum'], 'Rice Flour', 'Ground with a ' . $this_inventory['itemname'], $this_inventory['location'], $rice_quantity);

      $message .= '<p class="success">It\'s a lot of work, but you grind ' . $rice_quantity . ' Rice into Rice Flour!</p>';

      $rice -= $rice_quantity;

      $RECOUNT_INVENTORY = true;
    }
    else
      $message .= '<p class="failure">You do not have that much Rice.</p>';
  }
}

if($message)
  echo $message;

if($tools < 2)
  echo '<p>You will need two ' . $this_inventory['itemname'] . 's in order to get anything useful accomplished.</p>';
else if($wheat > 0 || $rice > 0 || $rye > 0)
{
?>
<p>You have <?= $wheat ?> bale<?= $wheat != 1 ? 's' : '' ?> of Wheat, <?= $rye ?> bale<?= $rye != 1 ? 's' : '' ?> of Rye, and <?= $rice ?> bag<?= $rice != 1 ? 's' : '' ?> of Rice available.</p>
<p>How many would you like to grind into Flour?</p>
<form action="itemaction.php?idnum=<?= $_GET['idnum'] ?>" method="post">
<table>
<?php
  if($wheat > 0)
    echo '<tr><th>Wheat:</th><td><input name="wheat" size="3" maxlength="' . strlen($wheat) . '" /></td></tr>';
  if($rye > 0)
    echo '<tr><th>Rye:</th><td><input name="rye" size="3" maxlength="' . strlen($rye) . '" /></td></tr>';
  if($rice > 0)
    echo '<tr><th>Rice:</th><td><input name="rice" size="3" maxlength="' . strlen($rice) . '" /></td></tr>';
?>
</table>
<p><input type="submit" value="Grind" /></p>
</form>
<?php
}
else
  echo '<p>You do not have any Wheat, Rye, or Rice in this room.</p>';
?>
