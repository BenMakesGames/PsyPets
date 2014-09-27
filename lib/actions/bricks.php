<?php
if($okay_to_be_here !== true)
  exit();

$command = '
  SELECT COUNT(idnum) AS c
  FROM monster_inventory
  WHERE
    user=' . quote_smart($user['user']) . ' AND
    location=' . quote_smart($this_inventory['location']) . ' AND
    itemname=\'Bricks\'
';

$data = $database->FetchSingle($command, 'fetching brick count');

$bricks = (int)$data['c'];

$walls = floor($bricks / 4);

$success = false;

if($_GET['action'] == 'make1')
{
  if($walls >= 1)
  {
    $deleted = delete_inventory_byname($user['user'], 'Bricks', 4, $this_inventory['location']);

    if($deleted < 4)
      die('Bad DB error (or maybe cheating): could not find 4 Bricks to delete.');

    add_inventory($user['user'], 'u:' . $user['idnum'], 'Brick Wall', 'Laid by ' . $user['display'], $this_inventory['location']);

    echo '<p class="success">Created 1 Brick Wall.</p>';
    $AGAIN_WITH_ANOTHER = true;
    $success = true;
  }
  else
    echo '<p class="failure">You do not have enough bricks!</p>';
}
else if($_POST['action'] == 'That Many!')
{
  $quantity = (int)$_POST['quantity'];
  
  if($walls >= $quantity && $quantity > 0)
  {
    $bricks = $quantity * 4;
  
    $deleted = delete_inventory_byname($user['user'], 'Bricks', $bricks, $this_inventory['location']);

    if($deleted != $bricks)
      die('Bad DB error (or maybe cheating): could not find ' . $bricks . ' Bricks to delete.');

    add_inventory_quantity($user['user'], 'u:' . $user['idnum'], 'Brick Wall', 'Laid by ' . $user['display'], $this_inventory['location'], $quantity);

    echo '<p class="success">Created ' . $quantity . ' Brick Wall' . ($quantity != 1 ? 's' : '') . '.</p>';
    $AGAIN_WITH_ANOTHER = true;
    $success = true;
  }
}

if(!$success)
{
?>
<p>You have <?= $bricks ?> Brick<?= $bricks != 1 ? 's' : '' ?> in this room, enough to make <?= $walls ?> Brick Wall<?= $walls != 1 ? 's' : '' ?>.  (Each Brick Wall needs 4 Bricks.)</p>
<?php
  if($walls == 1)
    echo '<ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&amp;action=make1">Cool!  Let\'s make one!</a></li></ul>';
  else if($walls >= 2)
  {
?>
<p>How many will you make?</p>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
<p><input type="number" min="1" max="<?= $walls ?>" size="3" maxlength="<?= strlen($walls) ?>" name="quantity" /> <input type="submit" name="action" value="That Many!" /></p>
</form>
<?php
  }
}
?>
