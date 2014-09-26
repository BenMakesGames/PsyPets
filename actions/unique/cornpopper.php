<?php
require_once 'commons/itemlib.php';

if($okay_to_be_here !== true)
  exit();

$quantity = (int)$_POST['quantity'];

$command = 'SELECT COUNT(*) AS qty FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Corn\' AND location=' . quote_smart($this_inventory['location']);
$data = $database->FetchSingle($command, 'fetching corn count');

$corn = (int)$data['qty'];

if($quantity > 0)
{
  if($quantity % 2 != 0)
    $message = '<span class="failure">You must provide an even number of Corn.</span>';
  else if($corn < $quantity)
    $message = '<span class="failure">You do not have that much Gold.</span>';
  else
  {
    $quantity = delete_inventory_byname($user['user'], 'Corn', $quantity, $this_inventory['location']);
    $makes = mt_rand($quantity / 2, floor($quantity * .75));

    $possible_items = array(
      'Candy Corn',
      'Caramel Corn',
      'Popcorn Shrimp',
      'Chocolate Popcorn',
      'Spicy Popcorn',
      'Parmesan Popcorn',
    );

    for($i = 0; $i < $makes; ++$i)
    {
      $itemname = $possible_items[array_rand($possible_items)];
      add_inventory($user["user"], 'u:' . $user['idnum'], $itemname, 'Popped in a Gourmet Popcorn Maker', $this_inventory['location']);
      $results[$itemname]++;
    }

    $message = '<p>You produced:</p><ul>';
    
    foreach($results as $itemname=>$qty)
      $message .= '<li>' . $qty . '&times; ' . $itemname . '</li>';

    $message .= '</ul>';

    $corn -= $quantity;

    $RECOUNT_INVENTORY = true;
  }
}

if($message)
  echo $message . '</p><p>';

if($corn > 0)
{
?>
You have <?= $corn ?> Corn available.</p>
<p>How many would you like to pop?  You must pop an even number of Corn (2, 4, 6, 8, etc).</p>
<p><form action="itemaction.php?idnum=<?= $_GET['idnum'] ?>" method="post">
<input name="quantity" size="3" maxlength="3" /> <input type="submit" value="Pop!" />
</form>
<?php
}
else
  echo "You do not have any Corn in this room.";
?>
