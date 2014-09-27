<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/fireworklib.php';

$data = (int)$this_inventory['data'];

$eats_items = array(
  'Broken Glass' => 16, 'Glass' => 25, 'Mirror' => 30, 'Triangular Prism' => 300,
  'Red Dye' => 10, 'Blue Dye' => 10, 'Yellow Dye' => 10,
  'Gossamer' => 600
);

if($_POST['action'] == 'Add')
{
  $item = $_POST['item'];
  $quantity = (int)$_POST['quantity'];

  if(array_key_exists($item, $eats_items) && $quantity > 0)
  {
    $eaten = delete_inventory_fromhome($user['user'], $item, $quantity);
    
    if($eaten > 0)
    {
      $gain = $eats_items[$item] * $eaten;

      $data += $gain;

      $command = 'UPDATE monster_inventory SET data=\'' . $data . '\' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'updating fountain data');

      $error = '<p class="success">' . $item . ' were given to the ' . $this_inventory['itemname'] . '!</p>';
    }
    else
      $error = '<p class="failure">You do not have any ' . $item . '!</p>';
  }
}
else if($_GET['action'] == 'glister' && $data >= 100)
{
  $quantity = (int)($data / 100);

  $data -= (100 * $quantity);

  $command = 'UPDATE monster_inventory SET data=\'' . $data . '\' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating fountain data');

  $supply = get_firework_supply($user);

  gain_firework($supply, 8, $quantity);

  $command = 'UPDATE monster_users SET fireworks=' . quote_smart(render_firework_data_string($supply)) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'giving firework to player');

  $error = '<p class="success">The Glister is ready!  (Go find a forum post or ' . $quantity . ' to apply it to!)</p>';
}

$valid_items = array_keys($eats_items);

$command = 'SELECT itemname,COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location LIKE \'home%\' AND location NOT LIKE \'home/$%\' AND itemname IN (\'' . implode('\', \'', $valid_items) . '\') GROUP BY(itemname)';
$clovers = $database->FetchMultiple($command, 'fetching coloring items from house');

echo $error .
     '<p>You may provide the Prismatic Fountain with coloring items!  Doing so will enable you to "Glister" plaza posts!  (Kinda\' like Fireworks, but rainbow-y-er :P)</p>';

if(count($clovers) > 0)
{
?>
<p><i>(The quantity listed is the number available in your house, not the number you will use.)</i></p>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
<table>
 <tr class="titlerow">
  <th></th>
  <th></th>
  <th>Item</th>
  <th>Quantity</th>
 </tr>
<?php
  $rowstyle = begin_row_class();
  foreach($clovers as $clover)
  {
    $item = get_item_byname($clover['itemname']);
?>
 <tr class="<?= $rowstyle ?>">
  <td><input type="radio" name="item" value="<?= $item['itemname'] ?>" /></td>
  <td class="centered"><?= item_display_extra($item, '', ($user['inventorylink'] == 'yes')) ?></td>
  <td><?= $clover['itemname'] ?></td>
  <td class="centered"><?= $clover['c'] ?></td>
 </tr>
<?php
    $rowstyle = alt_row_class($rowstyle);
  }
?>
</table>
<p><b>Quantity:</b> <input name="quantity" maxlength="3" size="3" value="1" /> <input type="submit" name="action" value="Add" /></p>
</form>
<?php
}
else
  echo '<p>You don\'t have any coloring items in your house.  (Items in protected rooms are not listed here.)</p>';
?>
<h5>Production Progress</h5>
<table>
 <tr>
  <td><?php
    if($data >= 100)
      echo '<a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=glister">Glister</a>';
    else
      echo '<span class="dim">Glister</span>';
?></td>
  <td><?php if($data > 0) { ?><div style="width: 50px; padding: 4px; border: 1px solid #000;" onmouseover="Tip('<?= floor($data) ?>%');"><img src="gfx/red_shim.gif" height="12" width="<?= min(50, ceil($data * 50 / 100)) ?>" alt="" /></div><?php } else echo '<i class="dim">no progress</i>'; ?></td>
 </tr>
</table>
