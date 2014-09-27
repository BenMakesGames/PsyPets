<?php
require_once 'commons/itemlib.php';

if($okay_to_be_here !== true)
  exit();

$quantity = (int)$_POST['quantity'];
$cream = 0;

$myhouse = get_inventory_byuser($user['user'], $this_inventory['location']);

foreach($myhouse as $item)
{
  if($item['itemname'] == 'Cream')
    $cream++;
}

if($quantity > 0)
{
  if($cream >= $quantity)
  {
    delete_inventory_byname($user["user"], 'Cream', $quantity, $this_inventory['location']);

    for($i = 0; $i < $quantity; ++$i)
      add_inventory($user["user"], 'u:' . $user['idnum'], 'Butter', 'Churned in the ' . $this_inventory['itemname'], $this_inventory['location']);

    $message = '<span class="success">It\'s a lot of work, but you finally churn ' . $quantity . ' Cream into Butter!</span>';

    $cream -= $quantity;

    $RECOUNT_INVENTORY = true;
  }
  else
    $message = '<span class="failure">You do not have that much Cream.</span>';
}

if($message)
  echo $message . '</p><p>';

if($cream > 0)
{
?>
You have <?= $cream ?> carton<?= $cream != 1 ? 's' : '' ?> of Cream available.</p>
<p>How many would you like to churn into Butter?</p>
<p><form action="itemaction.php?idnum=<?= $_GET['idnum'] ?>" method="post">
<input name="quantity" size="2" maxlength="2" /> <input type="submit" value="Churn" />
</form>
<?php
}
else
  echo "You do not have any Cream in this room.";
?>
