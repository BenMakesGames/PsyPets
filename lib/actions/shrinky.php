<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/houselib.php';
require_once 'commons/shrinkylib.php';

require_once 'libraries/extra_functions.php';

$house = get_house_byuser($user['idnum']);

if($house === false)
{
  echo "Failed to load your house.<br />\n";
  exit();
}

$allowed_items = $transformations;

$my_addons = take_apart(',', $house['addons']);

foreach($addons as $addon)
{
  if(in_array($addon, $my_addons))
    $addons_to_shrink[] = $addon;
}

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

  $i = array_search_reverse($addon, $addons_to_shrink);
  $j = array_search_reverse($addon, $my_addons);

  if($i !== false && $j !== false)
  {
    unset($addons_to_shrink[$i]);
    unset($my_addons[$j]);

    $command = 'UPDATE monster_houses SET addons=' . quote_smart(implode(',', $my_addons)) . ' WHERE userid=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'updating house addons');

    $new_item = 'Model ' . $addon;

    add_inventory($user['user'], '', $new_item, 'Shrunk from ' . $user['display'] . '\'s house', 'storage/incoming');
    flag_new_incoming_items($user['user']);

    echo '<p class="success">The ' . $addon . ' add-on has been transformed into the ' . $new_item . '!</p>';

    $damage_weapon = true;
  }
}

if($damage_weapon)
{
  require_once 'commons/statlib.php';
  $got_badge = record_stat_with_badge($user['idnum'], 'Used a Shrinky Ray Gun', 1, 100, 'shrinky');
  
  if($got_badge)
    echo '<p><i>(You received the Honey, I Shrunk the Pets Badge!)</i></p>';

  $damage = mt_rand(1, 50);

  if($damage > $this_inventory['health'])
  {
    delete_inventory_byid($this_inventory['idnum']);
    add_inventory($user['user'], '', 'Rubble', 'Destroyed remains of ' . $this_inventory['itemname'], $this_inventory['location']);

    echo '<p class="failure">The ' . $this_inventory['itemname'] . ' has taken all it can bear!  It breaks to pieces, reduced to little more than Rubble.</p>';

    $destroyed = true;

    record_stat($user['idnum'], 'Destroyed a Shrinky Ray Gun', 1);
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
  <td class="centered"><?= item_display_extra($details) ?></td>
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

  if(count($addons_to_shrink) > 0)
  {
    echo '<h4>Add-ons</h4>' .
         '<p>Some house add-ons can be shrunk down into models.  In case it wasn\'t clear, this will remove the add-on from your house!</p>' .
         '<p><strong>However:</strong> any information specific to your add-on - monsters in your Dungeon, airships in your Airship Mooring, items in your Basement, etc - will <em>not</em> be deleted.  If you ever rebuild the add-on, you will find all of these things waiting for you just as you left them.</p>' .
         '<form action="itemaction.php?idnum=' . $this_inventory['idnum'] . '" method="post">' .
         '<table><tr class="titlerow"><th></th><th>Add-on</th></tr>';

    $rowclass = begin_row_class();

    foreach($addons_to_shrink as $addon)
    {
      echo '<tr class="' . $rowclass . '"><td><input type="radio" name="addon" value="' . $addon . '" /></td><td>' . $addon . '</td></tr>';
      $rowclass = alt_row_class($rowclass);
    }

    echo '</table><p><input type="submit" name="submit2" value="Fire!" /></p></form>';
  }
} // not destroyed
?>
