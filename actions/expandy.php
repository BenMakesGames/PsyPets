<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/houselib.php';
require_once 'commons/shrinkylib.php';

$house = get_house_byuser($user['idnum']);

if($house === false)
{
  echo "Failed to load your house.<br />\n";
  exit();
}

$allowed_items = array_flip($transformations);

$my_addons = take_apart(',', $house['addons']);

foreach($addons as $addon)
  $addons_to_expand[] = $addon;

$damage_weapon = false;

if($_POST['submit'] == 'Fire!')
{
  $target_item = get_inventory_byid($_POST['item']);

  if($target_item['user'] == $user['user'] && $target_item['location'] == $this_inventory['location'])
  {
    if(in_array($target_item['itemname'], $allowed_items))
    {
      $new_item = array_search($target_item['itemname'], $allowed_items);
      
      $new_item_details = get_item_byname($new_item);
      $this_item_details = get_item_byname($target_item['itemname']);

      if($new_item_details['durability'] == 0)
        $new_health = 0;
      else if($this_item_details['durability'] == 0)
        $new_health = $new_item_details['durability'];
      else
        $new_health = max(1, floor($target_item['health'] * ($new_item_details['durability'] / $this_item_details['durability'])));

      $command = 'UPDATE monster_inventory SET itemname=' . quote_smart($new_item) . ',health=' . $new_health . ',changed=' . $now . ' WHERE idnum=' . $target_item['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'resizing item');

      echo '<p class="success">The ' . $target_item['itemname'] . ' has been transformed into the ' . $new_item . '!</p>';
      
      $damage_weapon = true;
    }
    else
      echo '<p class="failure">The ' . $this_inventory['itemname'] . ' cannot be used on ' . $target_item['itemname'] . '.</p>';    
  }
  else
    echo '<p class="failure">You don\'t seem to have that item in this room. (Where could they have gone?)</p>';
}
else if($_POST['submit2'] == 'Fire!')
{
  $addon = $_POST['addon'];
  
  if(!in_array($addon, $my_addons))
  {
    $item = 'Model ' . $addon;
    
    $command = 'DELETE FROM monster_inventory WHERE itemname=' . quote_smart($item) . ' AND user=' . quote_smart($user['user']) . ' AND location=' . quote_smart($this_inventory['location']) . ' LIMIT 1';
    $database->FetchNone($command, 'deleting model');
    
    if($database->AffectedRows() > 0)
    {
      $my_addons[] = $addon;

      $command = 'UPDATE monster_houses SET addons=' . quote_smart(implode(',', $my_addons)) . ' WHERE userid=' . $user['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'updating house addons');

      echo '<p class="success">The Model ' . $addon . ' has been transformed into the ' . $addon . ' add-on!</p>';

      $damage_weapon = true;
    }
  }
}

if($damage_weapon)
{
  require_once 'commons/statlib.php';
  record_stat($user['idnum'], 'Used an Expandy Ray Gun', 1);

  $damage = mt_rand(1, 50);

  if($damage > $this_inventory['health'])
  {
    delete_inventory_byid($this_inventory['idnum']);
    add_inventory($user['user'], '', 'Rubble', 'Destroyed remains of ' . $this_inventory['itemname'], $this_inventory['location']);

    echo '<p class="failure">The ' . $this_inventory['itemname'] . ' has taken all it can bear!  It breaks to pieces, reduced to little more than Rubble.</p>';

    $destroyed = true;

    record_stat($user['idnum'], 'Destroyed an Expandy Ray Gun', 1);
  }
  else
  {
    $command = 'UPDATE monster_inventory SET health=health-' . $damage . ' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'damaging ray gun');

    echo '<p class="failure">The ' . $this_inventory['itemname'] . ' has taken a little damage...</p>';
  }
}

if($destroyed)
  $AGAIN_WITH_ANOTHER = true;
else
{
  $command = 'SELECT idnum,itemname,COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=' . quote_smart($this_inventory['location']) . ' AND itemname IN (';
  
  $first = true;
  foreach($allowed_items as $itemname)
  {
    if($first)
      $first = false;
    else
      $command .= ', ';
    
    $command .= quote_smart($itemname);
  }
  
  $command .= ') GROUP BY itemname';
  
  $inventory = $database->FetchMultiple($command, 'fetching transformable items');
  
  if(count($inventory) == 0)
    echo '<p>There are no items in this room which the ' . $this_inventory['itemname'] . ' can operate on.</p>';
  else
  {
?>
<h4>Items</h4>
<p>The <?= $this_inventory['itemname'] ?> can be operated on the following items in this room.</p>
<p>The <?= $this_inventory['itemname'] ?> is an unstable device.  Every use damages it.  You might want to keep some Duct Tape handy if you plan on using it a lot.</p>
<p><i>(The quantity shown is the number of the items in the room, not the number you will use.  You will always only use one of the selected item per use of the <?= $this_inventory['itemname'] ?>.)</i></p>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
<table>
 <tr class="titlerow">
  <th></th><th></th><th>Item</th><th>Qty</th>
 </tr>
<?php
    $rowclass = begin_row_class();
  
    foreach($inventory as $item)
    {
      $details = get_item_byname($item['itemname']);
?>
 <tr class="<?= $rowclass ?>">
  <td><input type="radio" name="item" value="<?= $item['idnum'] ?>" /></td>
  <td class="centered"><?= item_display($details, '') ?></td>
  <td><?= $item['itemname'] ?></td>
  <td><?= $item['c'] ?></td>
 </tr>
<?php
      $rowclass = alt_row_class($rowclass);
    }
?>
</table>
<p><input type="submit" name="submit" value="Fire!" /></p>
</form>
<?php
  } // there are items we can target

  $command = 'SELECT idnum,itemname FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=' . quote_smart($this_inventory['location']) . ' AND itemname IN (';

  $first = true;
  foreach($addons_to_expand as $addon)
  {
    if($first)
      $first = false;
    else
      $command .= ', ';

    $command .= quote_smart('Model ' . $addon);
  }

  $command .= ') GROUP BY itemname';

  $inventory = $database->FetchMultiple($command, 'fetching transformable models');

  if(count($inventory) > 0)
  {
		echo '
			<h4>Models</h4>
			<p>Models can be expanded into house add-ons.</p>
			<form action="itemaction.php?idnum=' . $this_inventory['idnum'] . '" method="post">
			<table><tr class="titlerow"><th></th><th></th><th>Item</th><th></th></tr>
		';

    $rowclass = begin_row_class();
    
    foreach($inventory as $item)
    {
      $details = get_item_byname($item['itemname']);

      $addon = substr($item['itemname'], 6);

      echo '<tr class="' . $rowclass . '"><td><input type="radio" name="addon" value="' . $addon . '" /></td><td class="centered">' . item_display($details, '') . '</td><td>' . $item['itemname'] . '</td><td>';

      if(in_array($addon, $my_addons))
      {
        echo '<i>(You already have the ' . $addon . ' Add-on)</i>';
      }

      echo '</td></tr>';

      $rowclass = alt_row_class($rowclass);
    }
    
    echo '</table><p><input type="submit" name="submit2" value="Fire!" /></p></form>';
  }
} // not destroyed
?>
