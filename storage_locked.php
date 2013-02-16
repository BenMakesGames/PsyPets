<?php
$whereat = 'storage/locked';
$wiki = 'Storage#Locked';

$THIS_ROOM = 'Locked';

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

if($user['license'] != 'yes')
{
  header('Location: ./storage.php');
  exit();
}

$locid = $user['locid'];
$house = get_house_byuser($user['idnum'], $locid);

if($house['locid'] != $locid)
{
  echo "Failed to load your house.<br />\n";
  exit();
}

$addons = take_apart(',', $house['addons']);

if($_GET['sortby'] == 'idnum' || $_GET['sortby'] == 'bulk' ||
  $_GET['sortby'] == 'itemname' || $_GET['sortby'] == 'itemtype' ||
  $_GET['sortby'] == 'ediblefood')
{
  $user['storagesort'] = $_GET['sortby'];

  $command = 'UPDATE monster_users SET storagesort=' . quote_smart($user['storagesort']) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating storage sort');
}
else if($_GET['viewby'] == 'details' || $_GET['viewby'] == 'icons')
{
  $user['storageview'] = $_GET['viewby'];

  $command = 'UPDATE monster_users SET storageview=' . quote_smart($user['storageview']) . ' WHERE idnum=' . $user["idnum"] . " LIMIT 1";
  $database->FetchNone($command, 'updating storage view');
}

$query_time = microtime(true);

$command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=\'storage/locked\'';
$data = $database->FetchSingle($command, 'fetching storage item count');

$num_items = (int)$data['c'];
$num_pages = ceil($num_items / 2000);
$page = (int)$_GET['page'];

if($page < 1 || $page > $num_pages)
  $page = 1;

$inventory = get_room_inventory($user['user'], 'storage/locked', $num_items, $num_pages, $page, $user['storagesort']);

if($num_items >= 2000)
  $page_note = true;

$query_time = microtime(true) - $query_time;

$count_time = microtime(true);
$total_size = storage_bulk($user['user']);
$count_time = microtime(true) - $count_time;

$footer_note .= '<br />Took ' . round($query_time, 4) . 's fetching locked storage inventory, and ' . round($count_time, 4) . 's counting the total bulk.';

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
$rooms[] = 'My Store';
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

$command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE location=\'seized\' AND user=' . quote_smart($user['user']);
$seized_count = $database->FetchSingle($command, 'storage.php');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s Locked Storage</title>
<?php include 'commons/head.php'; ?>
<?php include 'commons/ajaxinventoryjs.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4 class="nomargin"><?= $user['display'] ?>'s Locked Storage <i>(<span class="housebulk"><?= $size_report ?></span> <a href="storagesummary.php"><img src="gfx/summary.png" width="18" height="16" alt="(summary)" border="0" /></a>)</i></h4>
<?php
echo '<p style="padding-left: 2em;"><a href="autosort.php?applyto=storage/locked">auto-sort items</a> | <a href="autosort_edit.php">configure auto-sorter</a>';

if($user['autosorterrecording'] == 'yes')
  echo ' | <span id="recordingautosort"><a href="#" onclick="stop_recording(); return false;">&#9632;</a> <blink style="color:red;">recording moves</blink></span>';
else
  echo ' | <span id="recordingautosort"><a href="#" onclick="start_recording(); return false;" style="color:red;">&#9679;</a></span>';

echo '
  </p>
  <p><i>(Storage may be filled beyond its maximum capacity, however you will pay for the additional space when you collect <a href="allowance.php">allowance</a>.)</i></p>
';

if(array_search(date('F j'), $FREE_STORAGE_DAYS) !== false)
  echo '<ul><li>Today is a <a href="bank.php?dialog=fsd">Free Storage Day</a>!</li></ul>';
?>
     <ul class="tabbed">
      <li><a href="storage.php">Storage</a></li>
      <li class="activetab"><a href="storage_locked.php">Locked Storage</a></li>
      <li><a href="incoming.php">Incoming</a></li>
      <li><a href="mystore.php">My Store</a></li>
      <li><a href="myfavorstore.php">My Custom Item Store</a></li>
      <li><a href="outgoing.php">Outgoing</a></li>
     </ul>
     <ul class="tabbed">
      <li<?= $user['storageview'] == 'icons' ? ' class="activetab"' : '' ?>><a href="storage.php?viewby=icons">Icon View</a></li>
      <li<?= $user['storageview'] == 'details' ? ' class="activetab"' : '' ?>><a href="storage.php?viewby=details">List View</a></li>
     </ul>
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
    <input type="hidden" name="from" value="locked storage" />
<?php
if(count($inventory) > 0)
{
  if($page_note)
  {
    echo '<p><i>(You have over 2000 items in locked storage!  When this happens, ' . $SETTINGS['site_name'] . ' paginates your inventory.)</i></p>';
    echo paginate($num_pages, $page, 'storage_locked.php?page=%s');
  }
?>
<table>
 <tr>
  <td>
   <input type="button" value="Move to" onclick="move_items('move')" />&nbsp;<select id="move1" name="move1" onchange="document.getElementById('move2').selectedIndex = this.selectedIndex">
<?php
  foreach($rooms as $room)
    echo '    <option value="' . $room . '">' . ($room{0} == '$' ? substr($room, 1) : $room) . '</option>' . "\n";
?>
   </select>
  </td>
  <td><input type="button" value="Gamesell" onclick="sell_items('yes')" /></td>
  <td><input type="button" value="Throw Out" onclick="trash_items()" /></td>
 </tr>
</table>
<?php
  echo '<ul class="filter">';

  if($user['storagesort'] == 'bulk')
    echo '<li>Size/Weight&nbsp;<a href="storage.php?sortby=idnum">&#9650;</a></li>';
  else
    echo '<li>Size/Weight&nbsp;<a href="storage.php?sortby=bulk">&#9651;</a></li>';

  if($user['storagesort'] == 'itemname')
    echo '<li>Name&nbsp;<a href="storage.php?sortby=idnum">&#9660;</a></li>';
  else
    echo '<li>Name&nbsp;<a href="storage.php?sortby=itemname">&#9661;</a></li>';

  if($user['storagesort'] == 'itemtype')
    echo '<li>Type</b>&nbsp;<a href="storage.php?sortby=idnum">&#9660;</a></li>';
  else
    echo '<li>Type</b>&nbsp;<a href="storage.php?sortby=itemtype">&#9661;</a></li>';

  if($user['storagesort'] == 'ediblefood')
    echo '<li>Meal&nbsp;Size&nbsp;<a href="storage.php?sortby=idnum">&#9650;</a></li>';
  else
    echo '<li>Meal&nbsp;Size&nbsp;<a href="storage.php?sortby=ediblefood">&#9651;</a></li>';

  if($user['storagesort'] == 'message')
    echo '<li valign="top">Comment</li>';
  else
    echo '<li valign="top">Comment</li>';

  echo '</ul>';

  if($user['storageview'] == 'icons')
    render_inventory_xhtml_3($inventory);
  else if($user['storageview'] == 'details')
    render_inventory_xhtml_3_list($inventory);
?>
<table>
 <tr>
  <td>
   <input type="button" value="Move to" onclick="move_items('move')" />&nbsp;<select id="move2" name="move2" onchange="document.getElementById('move1').selectedIndex = this.selectedIndex">
<?php
  foreach($rooms as $room)
    echo '    <option value="' . $room . '">' . ($room{0} == '$' ? substr($room, 1) : $room) . '</option>' . "\n";
?>
   </select>
  </td>
  <td><input type="button" value="Gamesell" onclick="sell_items('yes')" /></td>
  <td><input type="button" value="Throw Out" onclick="trash_items()" /></td>
 </tr>
</table>
<?php
  if($page_note)
    echo paginate($num_pages, $page, 'storage_locked.php?page=%s');

  echo '<p>Remember: you can easily select many items at once by checking the first, then holding shift while checking the second.  You can do this on nearly every page of ' . $SETTINGS['site_name'] . '.</p>';
}
else
  echo '
    <p>Your Locked Storage is empty.</p>
    <p>Though it is part of Storage, items in <em>Locked</em> Storage are not visible from Recycling, The Smithery, The Park, and other locations which look at your Storage.</p>
  ';
?>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
