<?php
if($okay_to_be_here !== true)
  exit();

$maxbulk = $action_info[2];

require_once "commons/itemlib.php";

$inventory = get_inventory_byuser($user["user"], $this_inventory["location"]);

if($_POST["action"] == "zaap")
{
  $this_user = get_user_bydisplay($_POST["recipient"]);

  if($this_user == false)
    $errors[] = "Could not find a resident named \"" . $_POST["recipient"] . "\"";
  else if($this_user["idnum"] == $user["idnum"])
    $errors[] = "You cannot teleport things to yourself.  You already have those things...";
  else
  {
		$receiving_teleporter = $database->FetchSingle('SELECT * FROM monster_inventory WHERE user=' . quote_smart($this_user["user"]) . " AND itemname='Teleporter' LIMIT 1");

		if($receiving_teleporter === false)
			$errors[] = $_POST['recipient'] . ' does not have a teleporter...';
  }

  if(count($errors) == 0)
  {
    foreach($_POST as $key=>$value)
    {
      if($key{0} == 'i' && ($value == 'yes' || $value == 'on'))
      {
        $id = (int)substr($key, 1);
        $i = get_inventory_byid($id);
        $item = get_item_byname($i['itemname']);
        if($i['user'] != $user['user'] || $item === false)
        {
          $errors[] = 'Error collecting items to teleport (some or all of them no longer exist, or do not belong to you).';
          break;
        }
        else if($item['bulk'] > $maxbulk)
        {
          $errors[] = $item['itemname'] . ' is too large to fit in the teleporter.';
          break;
        }
        else if($item['location'] == 'pet' || $item['location'] == 'seized')
        {
          $errors[] = 'Error collecting items to teleport (some or all of them are inaccessible - equipped to a pet, or in seized storage).';
          break;
        }
        else if($item['noexchange'] == 'yes' || $item['cursed'] == 'yes')
        {
          $errors[] = $item['getname'] . ' is non-exchangeable and/or cursed.';
          break;
        }
        else
        {
          $ids[] = $id;
          $item_names[$i['itemname']]++;
        }
      }
    }
    
    if(count($errors) == 0 && count($ids) > 0)
    {
      $command = 'UPDATE monster_inventory SET user=' . quote_smart($this_user['user']) . ',changed=' . $now . ',location=\'storage/incoming\' WHERE idnum IN (' . implode(',', $ids) . ') LIMIT ' . count($ids);
      $database->FetchNone($command, 'teleporting items');

      flag_new_incoming_items($this_user['user']);

      $errors[] = count($ids) . ' ' . (count($ids) > 1 ? 'items were' : 'item was') . ' teleported successfully!';

      $RECOUNT_INVENTORY = true;

      foreach($item_names as $name=>$quantity)
        $item_list[] = $name . ';' . $quantity;
      
      $command = 'INSERT INTO monster_trades (userid1, userid2, timestamp, anonymous, step, dialog, items1, itemsdesc1) VALUES ' .
        '(' . $user['idnum'] . ', ' . $this_user['idnum'] . ', ' . $now . ', \'yes\', 3, \'<i>Items sent via Teleporter</i>\', ' .
        quote_smart(implode(',', $ids)) . ', ' . quote_smart(implode('<br />', $item_list)) . ')';
      $database->FetchNone($command, 'adding trade record for sent items');
    }
  }
}

$inventory = get_inventory_byuser($user['user'], $this_inventory['location']);

if(count($errors) > 0)
  echo '<ul><li>' . implode('</li><li>', $errors) . '</li></ul>';
?>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
<p><input type="submit" value="Make It So" /></p>
<table>
 <tr>
  <td>Recipient:</td>
  <td><input name="recipient" /></td>
 </tr>
</table></p>
<p>Which items will you teleport?</p>
<table>
 <tr class="titlerow">
  <th></th>
  <th></th>
  <th>Item Name</th>
  <th>Maker</th>
  <th>Comment</th>
 </tr>
<?php
$bgcolor = begin_row_class();

foreach($inventory as $i)
{
  $item = get_item_byname($i["itemname"]);

  if($item['bulk'] > $maxbulk || $item["noexchange"] == "yes" || $item["cursed"] == "yes")
    continue;

  $maker = item_maker_display($i['creator']);
?>
 <tr class="<?= $bgcolor ?>">
  <td><input type="checkbox" name="i<?= $i["idnum"] ?>" /></td>
  <td align="center"><img src="gfx/items/<?= $item["graphic"] ?>" /></td>
  <td><?= $item["itemname"] ?></td>
  <td><?= $maker ?></td>
  <td><?= $i["message"] . "<br />" . $i["message2"] ?></td>
 </tr>
<?php
  $bgcolor = alt_row_class($bgcolor);
}
?>
</table>
<p><input type="hidden" name="action" value="zaap" class="bigbutton" /><input type="submit" value="Make It So" /></p>
</form>
