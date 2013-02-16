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
  $can_take_stuff = (rank_has_right($ranks, $rankid, 'boxtake') || $group['leaderid'] == $user['idnum']);
}
else
  $can_take_stuff = false;

if($can_take_stuff)
{
  $locid = $user['locid'];
  $house = get_house_byuser($user['idnum'], $locid);

  if($house['locid'] != $locid)
  {
    echo "Failed to load your house.<br />\n";
    exit();
  }

  $addons = take_apart(',', $house['addons']);

  $rooms[] = 'Storage';
  $rooms[] = 'Common';

  if(strlen($house['rooms']) > 0)
  {
    $m_rooms = explode(',', $house['rooms']);
    foreach($m_rooms as $room)
      $rooms[] = $room;
  }

  if(array_search('Basement', $addons) !== false)
    $rooms[] = 'Basement';
}

$items = get_items_bygroup($groupid, $user['locid']);
$item_count = count($items);

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

if($a_member)
{
?>
     <ul class="tabbed">
      <li class="activetab"><a href="groupbox.php?id=<?= $groupid ?>">Browse/Take Items</a>
      <li><a href="groupbox_add.php?id=<?= $groupid ?>">Add Items</a>
      <li><a href="groupbox_logs.php?id=<?= $groupid ?>">Logs</a></li>
     </ul>
<?php
  if(count($items) > 0)
  {
    if($can_take_stuff)
    {
?>
     <form action="moveinventory2.php?confirm=1" method="post" id="homeaction" name="homeaction">
     <input type="hidden" name="from" value="groupbox" />
     <input type="hidden" name="id" value="<?= $group['idnum'] ?>" />
     <p><input type="submit" name="submit" value="Move to" />&nbsp;<select id="move1" name="move1" onchange="document.getElementById('move2').selectedIndex = this.selectedIndex">
<?php
      foreach($rooms as $room)
      {
        if($room != $curroom)
          echo '      <option value="' . $room . '">' . ($room{0} == '$' ? substr($room, 1) : $room) . '</option>' . "\n";
      }
?>
     </select></p>
<?php
    }
?>
     <table>
      <tr class="titlerow">
<?php if($can_take_stuff) echo '<th></th>'; ?>
       <th></th>
       <th>Item</th>
       <th>Size/Weight</th>
       <th>Maker</th>
       <th>Comment</th>
      </tr>
<?php
    $rowclass = begin_row_class();
    foreach($items as $item)
    {
      $details = get_item_byname($item['itemname']);
      $maker = item_maker_display($item['creator'], true);
?>
      <tr class="<?= $rowclass ?>">
<?php if($can_take_stuff) echo '<td><input type="checkbox" name="' . $item['idnum'] . '" /></td>'; ?>
       <td class="centered"><?= item_display($details, '') ?></td>
       <td><?= $item['itemname'] ?><br /><i class="dim"><?= $details['itemtype'] ?></i></td>
       <td class="centered"><?= ($details['bulk'] / 10) . '/' . ($details['weight'] / 10) ?></td>
       <td><?= $maker ?></td>
       <td><?= $item['message'] . '<br />' . $item['message2'] ?></td>
      </tr>
<?php
      $rowclass = alt_row_class($rowclass);
    }
?>
     </table>
<?php
    if($can_take_stuff)
    {
?>
     <p><input type="submit" name="submit" value="Move to" />&nbsp;<select id="move2" name="move2" onchange="document.getElementById('move1').selectedIndex = this.selectedIndex">
<?php
      foreach($rooms as $room)
      {
        if($room != $curroom)
          echo '      <option value="' . $room . '">' . ($room{0} == '$' ? substr($room, 1) : $room) . '</option>' . "\n";
      }
?>
     </select></p>
     </form>
<?php
    }
  }
  else
    echo '<p>There are no items in the ' . $ITEM_BOX . '.</p>';
}
else
  echo '<p>Only group members may view the contents of the ' . $ITEM_BOX . '.</p>';
/*
if($user['user'] == 'telkoth')
{
  // box size calculation information :P
  echo $now - $group['birthdate'] . '<br />' .
       ($now - $group['birthdate']) / (365 * 24 * 60 * 60) . '<br />' .
       pow(2, ($now - $group['birthdate']) / (365 * 24 * 60 * 60));
}*/
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
