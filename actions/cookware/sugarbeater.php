<?php
require_once 'commons/itemlib.php';

if($okay_to_be_here !== true)
  exit();

$quantity = (int)$_POST['quantity'];
$beet = 0;

$myhouse = get_inventory_byuser($user['user'], $this_inventory['location']);

foreach($myhouse as $item)
{
  if($item['itemname'] == 'Sugar Beet')
    $beet++;
}

if($quantity > 0)
{
  if($beet >= $quantity)
  {
    delete_inventory_byname($user["user"], 'Sugar Beet', $quantity, $this_inventory['location']);

    for($i = 0; $i < $quantity; ++$i)
    {
      add_inventory($user['user'], 'u:' . $user['idnum'], 'Sugar', 'Separated with the ' . $this_inventory['itemname'], $this_inventory['location']);
      add_inventory($user['user'], 'u:' . $user['idnum'], 'Sugar', 'Separated with the ' . $this_inventory['itemname'], $this_inventory['location']);
      add_inventory($user['user'], 'u:' . $user['idnum'], 'Brown Sugar', 'Separated with the ' . $this_inventory['itemname'], $this_inventory['location']);
    }

    $message = '<span class="success">It\'s a lot of work, but you finally beat ' . ($quantity * 2) . ' Sugar and ' . $quantity . ' Brown Sugar out of ' . $quantity . ' Sugar Beet' . ($quantity > 1 ? 's' : '') . '!</span>';

    $beet -= $quantity;

    $RECOUNT_INVENTORY = true;
  }
  else
    $message = '<span class="failure">You do not have that many Sugar Beets.</span>';
}

if($message)
  echo $message . '</p><p>';

if($beet > 0)
{
?>
You have <?= $beet ?> Sugar Beet<?= $beet != 1 ? 's' : '' ?> available.</p>
<p>How many would you like to beat?</p>
<p><form action="itemaction.php?idnum=<?= $_GET['idnum'] ?>" method="post">
<input name="quantity" size="2" maxlength="2" /> <input type="submit" value="Beat" />
</form>
<?php
}
else
  echo "You do not have any Sugar Beets in this room.";
?>
