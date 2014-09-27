<?php
if($okay_to_be_here !== true)
  exit();

$command = '
  SELECT COUNT(idnum) AS c
  FROM monster_inventory
  WHERE
    user=' . quote_smart($user['user']) . ' AND
    location=' . quote_smart($this_inventory['location']) . ' AND
    itemname=\'Stone Block\'
';

$data = $database->FetchSingle($command, 'fetching stone block count');

$bricks = (int)$data['c'];

$walls = floor($bricks / 12);

$success = false;

if($_GET['action'] == 'make1')
{
  if($walls >= 1)
  {
    $deleted = delete_inventory_byname($user['user'], 'Stone Block', 12, $this_inventory['location']);

    if($deleted != 12)
      die('Bad DB error (or maybe cheating): could not find 12 Stone Blocks to delete.');

    add_inventory($user['user'], 'u:' . $user['idnum'], 'Stone Wall', 'Laid by ' . $user['display'], $this_inventory['location']);

    echo '<p class="success">Created 1 Stone Wall.</p>';
    $AGAIN_WITH_ANOTHER = true;
    $success = true;
  }
  else
    echo '<p class="failure">You do not have enough Stone Blocks!</p>';
}
else if($_POST['action'] == 'That Many!')
{
  $quantity = (int)$_POST['quantity'];
  
  if($walls >= $quantity)
  {
    $bricks = $quantity * 12;
  
    $deleted = delete_inventory_byname($user['user'], 'Stone Block', $bricks, $this_inventory['location']);

    if($deleted != $bricks)
      die('Bad DB error (or maybe cheating): could not find ' . $bricks . ' Stone Blocks to delete.');

    add_inventory_quantity($user['user'], 'u:' . $user['idnum'], 'Stone Wall', 'Laid by ' . $user['display'], $this_inventory['location'], $quantity);

    echo '<p class="success">Created ' . $quantity . ' Stone Wall' . ($quantity != 1 ? 's' : '') . '.</p>';
    $AGAIN_WITH_ANOTHER = true;
    $success = true;
  }
}
else if($_GET['action'] == 'break')
{
  require_once 'commons/rocks.php';

  $AGAIN_WITH_ANOTHER = true;
  $RECOUNT_INVENTORY = true;

  $num_items = (rand() % 4) - 1;

  delete_inventory_byid($this_inventory['idnum']);

  $itemnames = GenerateItemsFromRocks($num_items);

  $descript = array(
    'break apart',
    'crack open',
    'chisel away',
  );

  $descript2 = array(
    'eventually recovering',
    'revealing',
    'exposing',
  );

  if(count($itemnames) > 0)
  {
    $items = 0;
    foreach($itemnames as $itemname)
    {
      add_inventory($user['user'], '', $itemname, 'Recovered from ' . $this_item['itemname'], $this_inventory['location']);

      $items++;

      if($items > 1)
        $itemlist .= ($items == count($itemnames) ? ' and ' : ', ');

      $itemlist .= $itemname;
    }

    $message = '<p>You ' . $descript[array_rand($descript)] . ' the ' . $this_item['itemname'] . ', ' . $descript2[array_rand($descript2)] . ' '. $itemlist . '.</p>';
  }
  else
    $message = '<p>You ' . $descript[array_rand($descript)] . ' the ' . $this_item['itemname'] . ', ' . $descript2[array_rand($descript2)] . ' nothing!  How unfortunate.</p>';

  echo $message;
  
  $success = true;
}

if(!$success)
{
?>
<p>You have <?= $bricks ?> Stone Block<?= $bricks != 1 ? 's' : '' ?> in this room, enough to make <?= $walls ?> Stone Wall<?= $walls != 1 ? 's' : '' ?>.  (Each Stone Wall needs 12 Stone Blocks.)</p>
<?php
  if($walls == 1)
    echo '<ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&amp;action=make1">Cool!  Let\'s make one!</a></li></ul>';
  else if($walls >= 2)
  {
?>
<p>How many will you make?</p>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
<p><input type="text" size="3" maxlength="<?= strlen($bricks) ?>" name="quantity" /> <input type="submit" name="action" value="That Many!" /></p>
</form>
<?php
  }

  echo '
    <p>Alternatively, you can just bust open the Stone Block!  You never know what you might find inside!</p>
    <ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&amp;action=break">Oh man, I hope there\'s some Whoop Ass in there!  Bust away!</a></li></ul>
  ';
}
?>
