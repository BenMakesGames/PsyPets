<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/itemlib.php';

$delete_me = false;

$items = get_houseinventory_byuser($user['user']);

$itemlist = array();

foreach($userpets as $pet)
{
  if($pet['toolid'] > 0)
  {
    $item = get_inventory_byid($pet['toolid']);
    $details = get_item_byname($item['itemname']);
/*
    if($user['user'] == 'telkoth')
      echo $item['health'] . ' / ' . $details['durability'] . '<br />';
*/
    if($item['health'] < $details['durability'])
      $equiplist[] = $item;
  }
}

foreach($items as $item)
{
  if($item['health'] > 0)
  {
    $details = get_item_byname($item['itemname']);

    if($item['health'] < $details['durability'])
      $itemlist[] = $item;
  }
}

$delete_me = false;

if($_POST['action'] == 'tapeup')
{
  $item = get_inventory_byid((int)$_POST['itemid']);

  if($item['user'] == $user['user'])
  {
    $details = get_item_byname($item['itemname']);

    if($item['health'] < $details['durability'] && $details['norepair'] == 'no')
    {
      $old_health = $item['health'];
      $item['health'] = $details['durability'];
        
      $health_up = $item['health'] - $old_health;

      $command = "UPDATE monster_inventory SET health=" . $details['durability'] . ",changed=$now WHERE idnum=" . $item['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'taping stuff up');

      delete_inventory_byid($this_inventory['idnum']);

      $delete_me = true;

      require_once 'commons/statlib.php';
      record_stat($user['idnum'], 'Repaired an Item with Duct Tape', 1);
    }
  }
}

if($delete_me === false)
{
  if(count($itemlist) > 0 || count($equiplist) > 0)
  {
?>
Which item will you patch up?</p>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
<h5>Equipped Items</h5>
<?php
    if(count($equiplist) > 0)
    {
?>
<table>
 <tr class="titlerow">
  <th></th>
  <th></th>
  <th>Item</th>
  <th>Type</th>
  <th>Condition</th>
  <th>Comment</th>
  <th></th>
 </tr>
<?php
      $rowclass = begin_row_class();

      foreach($equiplist as $item)
      {
        $details = get_item_byname($item['itemname']);
        $can_repair = ($details['norepair'] == 'no');
?>
 <tr class="<?= $rowclass ?>">
  <td><?php if($can_repair) echo '<input type="radio" name="itemid" value="' . $item['idnum'] . '" />'; else echo '<input type="radio" disabled="disabled">'; ?></td>
  <td class="centered"><?= item_display($details, '') ?></td>
  <td><?= $item['itemname'] ?></td>
  <td><?= $details['itemtype'] ?></td>
  <td><?= durability($item['health'], $details['durability']) ?></td>
  <td><?= $item['message'] . '<br />' . $item['message2'] ?></td>
  <td><?= $can_repair ? '' : 'This item cannot be repaired.' ?></td>
 </tr>
<?php
        $rowclass = alt_row_class($rowclass);
      }
?>
</table>
<p><input type="hidden" name="action" value="tapeup" /><input type="submit" value="Tape" /></p>
<?php
    }
    else
      echo '<p>None of your pets\' equipment are in need of repair.</p>';
?>
<h5>Items in the House</h5>
<?php
    if(count($itemlist) > 0)
    {
?>
<table>
 <tr class="titlerow">
  <th></th>
  <th></th>
  <th>Item</th>
  <th>Type</th>
  <th>Condition</th>
  <th>Comment</th>
  <th></th>
 </tr>
<?php
      $rowclass = begin_row_class();

      foreach($itemlist as $item)
      {
        $details = get_item_byname($item['itemname']);
        $can_repair = ($details['norepair'] == 'no');
?>
 <tr class="<?= $rowclass ?>">
  <td><?php if($can_repair) echo '<input type="radio" name="itemid" value="' . $item['idnum'] . '" />'; else echo '<input type="radio" disabled="disabled">'; ?></td>
  <td class="centered"><img src="gfx/items/<?= $details['graphic'] ?>" alt="" /></td>
  <td><?= $item['itemname'] ?></td>
  <td><?= $details['itemtype'] ?></td>
  <td><?= durability($item['health'], $details['durability']) ?></td>
  <td><?= $item['message'] . '<br />' . $item['message2'] ?></td>
  <td><?= $can_repair ? '' : 'This item cannot be repaired.' ?></td>
 </tr>
<?php
        $rowclass = alt_row_class($rowclass);
      }
?>
</table>
<p><input type="hidden" name="action" value="tapeup" /><input type="submit" value="Tape" /></p>
<?php
    }
    else
      echo '<p>None of the items in your house are in need of repair.</p>';
?>
</form>
<?php
  }
  else
  {
?>
<p>All of your posessions seem to be in good condition already.  Fantastic!</p>
<?php
  }
}
else
{
?>
<p>You tape up the <?= $item['itemname'] ?> as best as you can.  Which is pretty darn good.</p>
<?php
  $AGAIN_WITH_ANOTHER = true;
}
?>
