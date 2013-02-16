<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$whereat = 'home';
$wiki = 'Basement';
$THIS_ROOM = 'Basement';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/checkpet.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/basementlib.php';
require_once 'commons/utility.php';

if(!addon_exists($house, 'Basement'))
{
  header('Location: /myhouse.php');
  exit();
}

$num_items = get_basement_count($user['idnum']);

$page = (int)$_GET['page'];
$items_per_page = 20;

if($_GET['action'] == 'search')
{
  $itemname = trim($_POST['item']);
  $details = get_item_byname($itemname);
  if($details === false)
    $error_message = '<span class="failure">There is no item called "' . $itemname . '".  Make sure you typed the full item name correctly.  (Refer to <a href="/encyclopedia.php">The Encyclopedia</a> for a complete list of items.)</span>';
  else
  { 
    $_POST['item'] = $details['itemname'];

    $command = 'SELECT COUNT(*) AS c FROM psypets_basement WHERE userid=' . (int)$user['idnum'] . ' AND itemname<' . quote_smart($details['itemname']);
    $data = fetch_single($command, 'fetching item page');
    $i = (int)$data['c'] + 1;
    $page = ceil($i / $items_per_page);
    $error_message = 'If you have any of that item, it would be on this page:';
  }
}

$pages = ceil($num_items / $items_per_page);

if($page > $pages)
  $page = $pages;

if($page < 1)
  $page = 1;

$items = get_basement_items($user['idnum'], $user['locid'], ($page - 1) * $items_per_page, $items_per_page);

$page_list = paginate($pages, $page, '/myhouse/addon/basement.php?page=%s');

$percent = floor($house['curbasement'] * 100 / $house['maxbasement']);

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Basement</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Basement <i>(<?= $house['curbasement'] . '/' . $house['maxbasement'] . '; ' . $percent ?>% full <a href="/basementsummary.php"><img src="/gfx/summary.png" width="18" height="16" alt="(summary)" border="0" /></a>)</i></h4>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";

room_display($house);
?>
<ul class="tabbed">
 <li class="activetab"><a href="/myhouse/addon/basement.php">View Basement</a></li>
 <li><a href="/myhouse/addon/basement_recycle.php">Take Apart Basement</a></li>
</ul>
<div class="infotip">
<p>Items in the basement are removed from the house - they do not appear on the profile and are not accessible to your pets in any way.  Also, items placed in the basement lose their comments.</p>
<p>Some items, for example Stockings and damaged items, cannot be stored in the Basement.</p>
</div>
<?php
if($pages > 1)
{
?>
<form action="/myhouse/addon/basement.php?action=search" method="post">
<p><input name="item" value="<?= $_POST['item'] ?>" /> <input type="submit" value="Find Item" /></p>
</form>
<?php
}

if(count($items) > 0)
{
  $rooms[] = 'Storage';
  $rooms[] = 'My Store';
  $rooms[] = 'Common';

  if(strlen($house['rooms']) > 0)
  {
    $m_rooms = explode(',', $house['rooms']);
    foreach($m_rooms as $room)
      $rooms[] = $room;
  }
?>
     <?= $page_list ?>
     <form action="/myhouse/addon/basementmove.php?page=<?= $page ?>" method="post">
     <table>
      <tr class="titlerow">
       <th colspan="2" class="centered">Quantity</th>
       <th></th>
       <th>Item</th>
      </tr>
<?php
  $row_class = begin_row_class();

  foreach($items as $item)
  {
    $details = get_item_byname($item['itemname']);
?>
<tr class="<?= $row_class ?>">
<td><input maxlength="<?= strlen($item['quantity']) ?>" size="3" name="<?= urlencode('i_' . $details['idnum']) ?>" /></td>
<td>/ <?= $item['quantity'] ?></td>
<td class="centered"><?= item_display_extra($details, '', true) ?></td>
<td><?= $details['itemname'] ?></td>
</tr>
<?php
    $row_class = alt_row_class($row_class);
  }
?>
     </table>
     <p><input type="submit" name="submit" value="Move to" />&nbsp;<select name="room">
<?php
  foreach($rooms as $room)
    echo '<option value="' . $room . '">' . ($room{0} == '$' ? substr($room, 1) : $room) . '</option>';
?>
     </select></p>
     </form>
<?php
  echo $page_list;
}
else
  echo '<p>You do not have any items in your basement.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
