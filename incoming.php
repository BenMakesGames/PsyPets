<?php
$wiki = 'Incoming';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/economylib.php';
require_once 'commons/questlib.php';

$sort_name = array(
  'itemname ASC' => 'item name',
  'changed DESC' => 'arrival date',
);

$opposite_sort = array(
  'itemname ASC' => 'changed DESC',
  'changed DESC' => 'itemname ASC',
);

$locid = $user['locid'];
$house = get_house_byuser($user['idnum'], $locid);

if($house['locid'] != $locid)
{
  echo "Failed to load your house.<br />\n";
  exit();
}

if($_GET['action'] == 'togglesort')
{
  $user['incomingsort'] = $opposite_sort[$user['incomingsort']];
  $command = 'UPDATE monster_users SET incomingsort=' . quote_smart($user['incomingsort']) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'incoming.php');
}

$incoming_tutorial_quest = get_quest_value($user['idnum'], 'tutorial: incoming');
if($incoming_tutorial_quest === false)
  $no_tip = true;

$addons = take_apart(',', $house['addons']);

$items_per_page = 500;

$command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=\'storage/incoming\'';
$count = $database->FetchSingle($command, 'incoming.php');

$num_items = $count['c'];

$num_pages = ceil($num_items / $items_per_page);
$page = (int)$_GET['page'];

if($page < 1 || $page > $num_pages)
  $page = 1;

if($num_items > 0)
{
  if($num_items < $items_per_page)
    $command = 'SELECT idnum,itemname,creator,health,message,message2,changed FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=\'storage/incoming\' ORDER BY ' . $user['incomingsort'] . ' LIMIT ' . $num_items;
  else
    $command = 'SELECT idnum,itemname,creator,health,message,message2,changed FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=\'storage/incoming\' ORDER BY ' . $user['incomingsort'] . ' LIMIT ' . (($page - 1) * $items_per_page) . ',' . $items_per_page;

  $incoming_inventory = $database->FetchMultiple($command, 'incoming.php');
}
else
  $incoming_inventory = array();

$total_size = storage_bulk($user['user']);

$freespace = 1000;

if(array_search('Attic', $addons) !== false)
  $freespace += 500;

if(array_search('Colossus', $addons) !== false)
  $pay_for = 60;
else
  $pay_for = 50;

if($total_size > $freespace)
  $size_report = ($total_size / 10) . '/' . ($freespace / 10) . '; ' . storage_fees($total_size - $freespace, $pay_for) . '<span class="money">m</span> fee<a href="/allowance.php?dialog=storage" class="help">?</a>';
else
  $size_report = ($total_size / 10) . '/' . ($freespace / 10) . '; no fees<a href="/allowance.php?dialog=storage" class="help">?</a>';

$rooms[] = 'Storage';

if($user['license'] == 'yes')
{
  $rooms[] = 'Locked Storage';
  $rooms[] = 'My Store';
}

$rooms[] = 'Common';

if(strlen($house['rooms']) > 0)
{
  $m_rooms = explode(',', $house['rooms']);
  foreach($m_rooms as $room)
    $rooms[] = $room;
}

if(array_search('Library', $addons) !== false)
  $rooms[] = 'Library Add-on';
if(array_search('Basement', $addons) !== false)
  $rooms[] = 'Basement';

$database->FetchNone('UPDATE monster_users SET newincoming=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1');

$seized_count = $database->FetchSingle('SELECT COUNT(*) AS c FROM monster_inventory WHERE location=\'seized\' AND user=' . quote_smart($user['user']));

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s Incoming</title>
<?php include 'commons/head.php'; ?>
<?php include 'commons/ajaxinventoryjs.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<?php
if($incoming_tutorial_quest === false)
{
  include 'commons/tutorial/incoming.php';
  add_quest_value($user['idnum'], 'tutorial: incoming', 1);
}
?>
     <h4 class="nomargin"><?= $user['display'] ?>'s Incoming <i>(<span class="housebulk"><?= $size_report ?></span> <a href="storagesummary.php"><img src="gfx/summary.png" width="18" height="16" alt="(summary)" border="0" /></a>)</i></h4>
<?php
echo '<p style="padding-left: 2em;"><a href="autosort.php?applyto=storage/incoming">auto-sort items</a> | <a href="autosort_edit.php">configure auto-sorter</a>';

if($user['autosorterrecording'] == 'yes')
  echo ' | <span id="recordingautosort"><a href="#" onclick="stop_recording(); return false;">&#9632;</a> <blink style="color:red;">recording moves</blink></span>';
else
  echo ' | <span id="recordingautosort"><a href="#" onclick="start_recording(); return false;" style="color:red;">&#9679;</a></span>';

echo '</p>';
?>
     <p><i>(Storage may be filled beyond its maximum capacity, however you will pay for the additional space when you collect <a href="allowance.php">allowance</a>.)</i></p>
<?php
if(array_search(date('F j'), $FREE_STORAGE_DAYS) !== false)
  echo '<ul><li>Today is a <a href="bank.php?dialog=fsd">Free Storage Day</a>!</li></ul>';
?>
     <ul class="tabbed">
      <li><a href="storage.php">Storage</a></li>
<?php if($user['license'] == 'yes') { ?>      <li><a href="storage_locked.php">Locked Storage</a></li><?php } ?>
      <li class="activetab"><a href="incoming.php">Incoming</a></li>
<?php if($user['license'] == 'yes') { ?>      <li><a href="mystore.php">My Store</a></li>
      <li><a href="myfavorstore.php">My Custom Item Store</a></li><?php } ?>
      <li><a href="outgoing.php">Outgoing</a></li>
     </ul>
    <p>Items are sorted by <?= $sort_name[$user['incomingsort']] ?> (<a href="incoming.php?action=togglesort">sort by <?= $sort_name[$opposite_sort[$user['incomingsort']]] ?></a>).</p>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";

if($seized_count['c'] > 0)
  echo '<ul><li><a href="/storage_seized.php" class="failure">' . $seized_count['c'] . ' item' . ($seized_count['c'] != 1 ? 's have' : ' has') . ' been seized for failure to pay storage fees!</a></li></ul>';
?>
    <div id="message_area"></div>
    <form action="moveinventory2.php?confirm=1" method="post" id="homeaction" name="homeaction">
    <input type="hidden" name="from" value="incoming" />
<?php
if($count['c'] > 0)
{
  if($num_pages > 1)
  {
    $page_list = paginate($num_pages, $page, 'incoming.php?page=%s');
    echo $page_list;
  }
?>
<table>
 <tr>
  <td>
   <input type="button" value="Move to" onclick="move_items('move')" />&nbsp;<select id="move1" name="move1" onchange="document.getElementById('move2').selectedIndex = this.selectedIndex">
<?php
  foreach($rooms as $room)
  {
    if($room != $curroom)
      echo '    <option value="' . $room . '">' . ($room{0} == '$' ? substr($room, 1) : $room) . '</option>' . "\n";
  }
?>
   </select>
  </td>
  <td><input type="button" value="Gamesell" onclick="sell_items('<?= $user['license'] ?>')" /></td>
  <td><input type="button" value="Throw Out" onclick="trash_items()" /></td>
 </tr>
</table>
<?php
  render_inventory_xhtml_2($incoming_inventory);
?>
<table>
 <tr>
  <td>
   <input type="button" value="Move to" onclick="move_items('move')" />&nbsp;<select id="move2" name="move2" onchange="document.getElementById('move1').selectedIndex = this.selectedIndex">
<?php
  foreach($rooms as $room)
  {
    if($room != $curroom)
      echo '    <option value="' . $room . '">' . ($room{0} == '$' ? substr($room, 1) : $room) . '</option>' . "\n";
  }
?>
   </select>
  </td>
  <td><input type="button" value="Gamesell" onclick="sell_items('<?= $user['license'] ?>')" /></td>
  <td><input type="button" value="Throw Out" onclick="trash_items()" /></td>
 </tr>
</table>
<?php
  if($num_pages > 1)
  {
    $page_list = paginate($num_pages, $page, 'incoming.php?page=%s');
    echo $page_list;
  }
?>
<p><i>Remember: you can easily select many items at once by checking the first, then holding shift while checking the second.  You can do this on nearly every page of <?= $SETTINGS['site_name'] ?>.</i></p>
<?php
}
else
  echo '     <p>You have no incoming items.</p>';
?>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
