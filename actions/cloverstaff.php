<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/itemlib.php';

$command = '
  SELECT itemname,COUNT(idnum) AS qty
  FROM monster_inventory
  WHERE
    user=' . quote_smart($user['user']) . ' AND
    location=' . quote_smart($this_inventory['location']) . ' AND
    itemname IN (\'2-Leaf Clover\', \'3-Leaf Clover\', \'4-Leaf Clover\', \'5-Leaf Clover\')
  GROUP BY itemname
';
$clovers = $database->FetchMultipleBy($command, 'itemname', 'fetching clovers');

$deleted = false;

if($_POST['action'] == 'Give')
{
  $clover_leaves = 0;

  foreach($clovers as $clover)
  {
    $leaves = (int)($clover['itemname']{0});
    $qty_to_use = (int)($_POST['clover' . $leaves]);
    
    if($qty_to_use > $clover['qty'])
      $qty_to_use = $clover['qty'];
    
    $qty_to_use = delete_inventory_byname($user['user'], $clover['itemname'], $qty_to_use, $this_inventory['location']);

    $clover_leaves += $qty_to_use * $leaves;
  }
  
  if($clover_leaves > 0)
  {
    $deleted = true;

    add_inventory_quantity($user['user'], '', 'Clover Leaf', '', $this_inventory['location'], $clover_leaves);

    delete_inventory_byid($this_inventory['idnum']);
  }
}

if($deleted === false)
{
  if(count($clovers) > 0)
  {
?>
<p>This staff seems to attract the Clovers in your house...</p>
<form action="/itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
<table>
 <tr class="titlerow">
  <th colspan="2" class="centered">Quantity</th>
  <th></th>
  <th>Item</th>
 </tr>
<?php
    $rowclass = begin_row_class();

    foreach($clovers as $clover)
    {
      $details = get_item_byname($clover['itemname']);
?>
 <tr class="<?= $rowclass ?>">
  <td><input type="text" name="clover<?= $clover['itemname']{0} ?>" size="2" maxlength="<?= strlen($clover['qty']) ?>" /></td>
  <td>/ <?= $clover['qty'] ?></td>
  <td class="centered"><?= item_display($details) ?></td>
  <td><?= $clover['itemname'] ?></td>
 </tr>
<?php
      $rowclass = alt_row_class($rowclass);
    }
?>
</table>
<p><input type="submit" name="action" value="Give" /></p>
<?php
  }
  else
    echo '<p>Nothing seems to happen.</p>';
?>
</form>
<?php
}
else
{
  echo '<p>The Clovers gather around the Clover Staff, swirling faster and faster, until, with a flash of light and a gust of wind, they explode into ' . $clover_leaves . ' Clover Leaves.</p>',
       '<p>The leaves settle before long, but the staff itself is nowhere to be seen...</p>';

  $AGAIN_WITH_ANOTHER = true;
}
?>
