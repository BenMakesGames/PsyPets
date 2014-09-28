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
require_once 'commons/threadfunc.php';
require_once 'commons/sqldumpfunc.php';

if($user['admin']['managedonations'] != 'yes' && $user['admin']['manageaccounts'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$itemid = (int)$_GET['id'];

if($itemid > 0)
{
  $command = 'SELECT * FROM monster_inventory WHERE idnum=' . $itemid . ' LIMIT 1';
  $inventory = $database->FetchSingle($command, 'fetching item');

  $command = 'SELECT * FROM monster_auctions WHERE itemid=' . $itemid;
  $auctions = $database->FetchMultiple($command, 'fetching auctions');

  $command = 'SELECT * FROM monster_trades WHERE items1 LIKE \'%' . $itemid . '%\' OR items2 LIKE \'%' . $itemid . '%\'';
  $results = $database->FetchMultiple($command, 'fetching trades');

  $trades = array();

  foreach($results as $result)
  {
    $items1 = explode(',', $result['items1']);
    $items2 = explode(',', $result['items2']);
  
    if(in_array($itemid, $items1) || in_array($itemid, $items2))
      $trades[] = $result;
  }

}
else
  $itemid = '';

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &nbsp; Administrative Tools &nbsp; Item Locator</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Item Locator</h4>
     <form method="get">
     <p><input name="id" value="<?= $itemid ?>" /> <input type="submit" value="Search" /></p>
     </form>
<?php
if($itemid > 0)
{
  echo '<h5>Inventory</h5>';

  if($inventory === false)
    echo '<p>That item no longer exists.</p>';
  else
    dump_sql_results($inventory);
  
  echo '<h5>Auctions</h5>';
  
  if(count($auctions) == 0)
    echo '<p>No auctions on this item have taken place.</p>';
  else
  {
    foreach($auctions as $auction)
      dump_sql_results($auction);
  }
  
  echo '<h5>Private Trades</h5>';
  
  if(count($trades) == 0)
    echo '<p>If this item has been traded, the trade record has been deleted.</p>';
  else
  {
    foreach($trades as $trade)
      dump_sql_results($trade);
  }
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
