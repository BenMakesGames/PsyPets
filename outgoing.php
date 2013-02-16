<?php
$wiki = 'Outgoing';

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

$locid = $user['locid'];
$house = get_house_byuser($user['idnum'], $locid);

if($house['locid'] != $locid)
{
  echo "Failed to load your house.<br />\n";
  exit();
}

$addons = take_apart(',', $house['addons']);

$items_per_page = 500;

$command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=\'storage/outgoing\'';
$count = $database->FetchSingle($command, 'outgoing.php');

$num_items = $count['c'];

$num_pages = ceil($num_items / $items_per_page);
$page = (int)$_GET['page'];

if($page < 1 || $page > $num_pages)
  $page = 1;

if($num_items > 0)
{
  if($num_items < $items_per_page)
    $command = 'SELECT idnum,itemname,creator,health,message,message2,changed FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=\'storage/outgoing\' ORDER BY itemname ASC LIMIT ' . $num_items;
  else
    $command = 'SELECT idnum,itemname,creator,health,message,message2,changed FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=\'storage/outgoing\' ORDER BY itemname ASC LIMIT ' . (($page - 1) * $items_per_page) . ',' . $items_per_page;

  $incoming_inventory = $database->FetchMultiple($command, 'outgoing.php');
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

$seized_count = $database->FetchSingle('SELECT COUNT(*) AS c FROM monster_inventory WHERE location=\'seized\' AND user=' . quote_smart($user['user']));

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s Outgoing</title>
<?php include 'commons/head.php'; ?>
<?php include 'commons/ajaxinventoryjs.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><?= $user['display'] ?>'s Outgoing <i>(<span class="housebulk"><?= $size_report ?></span> <a href="storagesummary.php"><img src="gfx/summary.png" width="18" height="16" alt="(summary)" border="0" /></a>)</i></h4>
     <p><i>(Storage may be filled beyond its maximum capacity, however you will pay for the additional space when you collect <a href="allowance.php">allowance</a>.)</i></p>
     <p>Items in pending trades, park events, and other exchanges are kept here.</p>
<?php
if(array_search(date('F j'), $FREE_STORAGE_DAYS) !== false)
  echo '<ul><li>Today is a <a href="bank.php?dialog=fsd">Free Storage Day</a>!</li></ul>';
?>
     <ul class="tabbed">
      <li><a href="storage.php">Storage</a></li>
<?php if($user['license'] == 'yes') { ?><li><a href="storage_locked.php">Locked Storage</a></li><?php } ?>
      <li><a href="incoming.php">Incoming</a></li>
<?php if($user['license'] == 'yes') { ?>
      <li><a href="mystore.php">My Store</a></li>
      <li><a href="myfavorstore.php">My Custom Item Store</a></li>
<?php } ?>
      <li class="activetab"><a href="outgoing.php">Outgoing</a></li>
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
<?php
if($count['c'] > 0)
{
  if($num_pages > 1)
  {
    $page_list = paginate($num_pages, $page, 'outgoing.php?page=%s');
    echo $page_list;
  }

  render_inventory_xhtml_2($incoming_inventory, false);

  if($num_pages > 1)
  {
    $page_list = paginate($num_pages, $page, 'outgoing.php?page=%s');
    echo $page_list;
  }
}
else
  echo '     <p>You have no outgoing items.</p>';
?>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
