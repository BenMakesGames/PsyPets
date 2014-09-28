<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/grammar.php';
require_once 'commons/userlib.php';

// must be at least admin level 5 to view this page
if($admin['massgift'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

if($_POST['submit'] == 'Restore')
{
  $sendto = get_user_bydisplay($_POST['resident'], 'user');
  
  if($sendto !== false)
  {
    foreach($_POST as $name=>$value)
    {
      if(substr($name, 0, 4) == 'item' && ($value == 'yes' || $value == 'on'))
        $itemids[] = substr($name, 4); 
    }

    if(count($itemids) > 0)
    {
      $command = 'UPDATE monster_inventory SET location=\'storage/incoming\',user=' . quote_smart($sendto['user']) . ',changed=' . $now . ' WHERE idnum IN (' . implode(',', $itemids) . ') LIMIT ' . count($itemids);
      $database->FetchNone($command, 'restoring selected items');
      
      $item_count = $database->AffectedRows();

      if($item_count == count($itemids))
        $message = '<span class="success">Restored ' . $item_count . ' ' . plural($item_count, 'item', 'items') . '.</span>'; 
      else
        $message = '<span class="progress">Selected ' . count($itemids) . ' ' . plural(count($itemids), 'item', 'items') . '; Restored ' . $item_count . ' of them.</span>';

      flag_new_incoming_items($sendto['user']);
    }
  }
  else
    $message = '<span class="failure">There is no resident named "' . $_POST['resident'] . '".</span>';
}
else if($_POST['submit'] == 'Trash')
{
  foreach($_POST as $name=>$value)
  {
    if(substr($name, 0, 4) == 'item' && ($value == 'yes' || $value == 'on'))
      $itemids[] = substr($name, 4); 
  }

  if(count($itemids) > 0)
  {
    $command = 'UPDATE monster_inventory SET location=\'storage\',user=\'junkyard\',changed=' . $now . ' WHERE idnum IN (' . implode(',', $itemids) . ') LIMIT ' . count($itemids);
    $database->FetchNone($command, 'deleting selected items');
  
    $item_count = $database->AffectedRows();

    if($item_count == count($itemids))
      $message = '<span class="success">Trashed ' . $item_count . ' ' . plural($item_count, 'item', 'items') . '.</span>'; 
    else
      $message = '<span class="progress">Selected ' . count($itemids) . ' ' . plural(count($itemids), 'item', 'items') . '; Trashed ' . $item_count . ' of them.</span>';
  }
}

$page = (int)$_GET['page'];

if($_POST['submit'] == 'Prev')
  $page--;
else if($_POST['submit'] == 'Next')
  $page++;

if($page < 1)
  $page = 1;

$start = ($page - 1) * 1000;

$command = 'SELECT itemname,idnum FROM monster_inventory WHERE user=\'psypets\' LIMIT ' . $start . ',1000';
$items = $database->FetchMultipleBy($command, 'idnum', 'fetching items belonging to "psypets"');

$initial_count = count($items);

// remove any items which are in pending trades
$command = 'SELECT items1,items2 FROM monster_trades WHERE step<3';
$pending_trades = $database->FetchMultiple($command, 'fetching pending trades');

$trade_items = 0;

foreach($pending_trades as $trade)
{
  if(strlen($trade['items1']) > 0)
  {
    $itemids = explode(',', $trade['items1']);
    $trade_items += count($itemids);
    foreach($itemids as $id)
      unset($items[$id]);
  }
  
  if(strlen($trade['items2']) > 0)
  {
    $itemids = explode(',', $trade['items2']);
    $trade_items += count($itemids);
    foreach($itemids as $id)
      unset($items[$id]);
  }
}

// remove any items which are in auctions
$command = 'SELECT itemid FROM monster_auctions WHERE claimed=\'no\'';
$waiting_auctions = $database->FetchMultiple($command, 'fetching waiting auctions');

foreach($waiting_auctions as $auction)
  unset($items[$auction['itemid']]);

$auction_items = count($waiting_auctions);

// remove any items which are in waiting park events
$command = 'SELECT prizes FROM monster_events WHERE finished=\'no\'';
$park_events = $database->FetchMultiple($command, 'fetching waiting park events');

$event_items = 0;

foreach($park_events as $event)
{
  if(strlen($event['prizes']) > 0)
  {
    $itemids = explode(',', $event['prizes']);
    $event_items += count($itemids);
    foreach($itemids as $id)
      unset($items[$id]);
  }
}

$found = array();

if(strlen($_POST['eyeout']) > 0)
{
  $names = explode(',', $_POST['eyeout']);
  
  foreach($items as $item)
  {
    if(in_array($item['itemname'], $names))
      $found[] = $item['itemname'];
  }
}

// display the remaining items

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Gift Residents</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Lost Items (<?= count($items) ?>)</h4>
     <p>Keep an eye out for these items names (comma separated):</p>
     <form action="/admin/find_orphaned_items.php?page=<?= $page ?>" method="post">
     <p><input name="eyeout" value="<?= $_POST['eyeout'] ?>" /></p>
<?php
if(count($found) > 0)
  echo '<p>Found these items on this page: ' . implode(', ', $found) . '</p>';

if($message)
  echo '<p>' . $message . '</p>';
?>
<?= $page ?>
<p><input type="submit" name="submit" value="Prev" /> <input type="submit" name="submit" value="Next" /></p>
<table>
 <tr class="titlerow">
  <th></th>
  <th>ID#</th>
  <th>Item</th>
 </tr>
<?php
$rowstyle = begin_row_class();

foreach($items as $item)
{
?>
 <tr class="<?= $rowstyle ?>">
  <td><input type="checkbox" name="item<?= $item['idnum'] ?>" /></td>
  <td><a href="/admin/finditem.php?id=<?= $item['idnum'] ?>"><?= $item['idnum'] ?></a></td>
  <td><?= $item['itemname'] ?></td>
 </tr>
<?php
  $rowstyle = alt_row_class($rowstyle);
}
?>
</table>
<p><input type="submit" name="submit" value="Prev" /> <input type="submit" name="submit" value="Next" /></p>
<h5>Trash</h5>
<p><input type="submit" name="submit" value="Trash" /></p>
<h5>Restore</h5>
<table>
 <th>To resident:</th>
 <td><input name="resident" /></td>
</table>
<p><input type="submit" name="submit" value="Restore" /></p>
</form>
<p><i>There are <?= $trade_items ?> in trades, <?= $auction_items ?> in auctions, and <?= $event_items ?> in park events.</i></p> 
<p><i>Initially found <?= $initial_count ?> items on this page; those that were not listed are in trades, auctions, and park events.</i></p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
