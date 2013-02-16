<?php
$child_safe = false;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/formatting.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/checkpet.php';
require_once 'commons/userlib.php';
require_once 'commons/utility.php';
require_once 'commons/grouplib.php';

$groupid = (int)$_GET['id'];
$group = get_group_byid($groupid);

if($group === false)
{
  header('Location: ./groupindex.php');
  exit();
}

$ranks = get_group_ranks($groupid);
$members = explode(',', $group['members']);
$organizer = get_user_byid($group['leaderid']);

$max_box_size = floor(20 * pow(2, ($now - $group['birthdate']) / (365 * 24 * 60 * 60)));

$a_member = is_a_member($group, $user['idnum']);

if($a_member)
{
  $rankid = get_member_rank($group, $user['idnum']);
  $can_add_stuff = (rank_has_right($ranks, $rankid, 'boxadd') || $group['leaderid'] == $user['idnum']);
}
else
  $can_add_stuff = false;

if(!$can_add_stuff)
{
  header('Location: ./groupbox.php?id=' . $groupid);
  exit();
}

$messages = array();

$item_count = get_itemcount_bygroup($groupid, $user['locid']);

$max_box_size = floor(20 * pow(2, ($now - $group['birthdate']) / (365 * 24 * 60 * 60)));

if($_POST['action'] == 'zaap' && $user['license'] == 'yes')
{
  $items_added = array();

  foreach($_POST as $key=>$value)
  {
    if($key{0} == 'i' && ($value == 'yes' || $value == 'on'))
    {
      $id = (int)substr($key, 1);
      $i = get_inventory_byid($id);
      $item = get_item_byname($i['itemname']);
      if($i['user'] != $user['user'] || $item === false)
      {
        $errors[] = '<span class="failure">Error collecting items to drop.</span>';
        break;
      }
      else if($item['noexchange'] == 'yes' || $item['cursed'] == 'yes')
      {
        $errors[] = '<span class="failure">' . $item['itemname'] . ' cannot be put into the ' . $ITEM_BOX . '.</span>';
        break;
      }
      else
      {
        $ids[] = $id;
        $items_added[$i['itemname']]++;
      }
    }
  }

  if(count($ids) + $item_count > $max_box_size)
    $errors[] = '<span class="failure">You cannot fit all of those items into the ' . $ITEM_BOX . '.  Only ' . ($max_box_size - $item_count) . ' more items, at most, can be put in.</span>';

  if(count($errors) == 0 && count($ids) > 0)
  {
    $command = 'UPDATE monster_inventory SET user=\'group:' . $groupid . '\',changed=' . time() . ',location=\'storage/incoming\',forsale=0 WHERE idnum IN(' . implode(',', $ids) . ') LIMIT ' . count($ids);
    $database->FetchNone($command, 'adding items to group box');

    $item_report = array();
    foreach($items_added as $name=>$amount)
      $item_report[] = ($amount != 1 ? ($amount . 'x ') : '') . $name;

    $command = 'INSERT INTO psypets_groupboxlogs (timestamp, groupid, userid, type, details) VALUES ' .
               '(' . $now . ', ' . $groupid . ', ' . $user['idnum'] . ', \'add\', ' . quote_smart(implode(', ', $item_report)) . ')';
    $database->FetchNone($command, 'recording group box activity (add)');

    $errors[] = '<span class="success">The following items were successfully added: ' . implode(', ', $item_report) . '</span>';

    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Added an Item to a Group Box', count($ids));
  }
}

$inventory = get_inventory_byuser($user['user'], 'storage');

include 'commons/html.php';
?>
 <head>
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $group['name'] ?> &gt; <?= $ITEM_BOX ?></title>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><?= $group['name'] ?>  &gt; <?= $ITEM_BOX ?> <i>(<?= $item_count . '/' . $max_box_size . '; ' . floor($item_count * 100 / $max_box_size) ?>%)</i></h4>
<?php
$activetab = 'box';
include 'commons/grouptabs.php';
?>
     <ul class="tabbed">
      <li><a href="/groupbox.php?id=<?= $groupid ?>">Browse/Take Items</a>
      <li class="activetab"><a href="/groupbox_add.php?id=<?= $groupid ?>">Add Items</a>
      <li><a href="/groupbox_logs.php?id=<?= $groupid ?>">Logs</a></li>
     </ul>
<?php
if(count($errors) > 0)
  echo '<ul><li>' . implode('</li><li>', $errors) . '</li></ul>';

if($user['license'] == 'yes')
{
?>
<p>Select items from your Storage to add to the Group's Item Box.</p>
<form action="/groupbox_add.php?id=<?= $groupid ?>" method="post">
<table>
 <tr class="titlerow">
  <th></th>
  <th></th>
  <th>Item</th>
  <th>Size/Weight</th>
  <th>Maker</th>
  <th>Comment</th>
 </tr>
<?php
  $bgcolor = begin_row_class();

  foreach($inventory as $i)
  {
    $item = get_item_byname($i['itemname']);

    if($item['noexchange'] == 'yes' || $item['cursed'] == 'yes')
      continue;

    $maker = item_maker_display($i['creator'], true);
?>
 <tr class="<?= $bgcolor ?>">
  <td><input type="checkbox" name="i<?= $i['idnum'] ?>" /></td>
  <td align="center"><?= item_display_extra($item) ?></td>
  <td><?= $item['itemname'] ?><br /><i class="dim"><?= $item['itemtype'] ?></i></td>
  <td class="centered"><?= ($item['bulk'] / 10) . '/' . ($item['weight'] / 10) ?></td>
  <td><?= $maker ?></td>
  <td><?= $i['message'] . '<br />' . $i['message2'] ?></td>
 </tr>
<?php
    $bgcolor = alt_row_class($bgcolor);
  }
?>
</table>
<p><input type="hidden" name="action" value="zaap" /><input type="submit" value="Drop" /></p>
</form>
<?php
}
else
  echo '<p><i>(You must have a <a href="/ltc.php">License to Commerce</a> to put items into a Group Box.)</i></p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
