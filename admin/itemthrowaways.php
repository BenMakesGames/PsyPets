<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';
$_GET['maintenance'] = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/utility.php';

if($admin['clairvoyant'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

if($_POST['action'] == 'Clear' && $admin['manageitems'] == 'yes')
{
  $command = 'TRUNCATE TABLE psypets_gamesold';
  $database->FetchNone(($command, 'emptying item throwaways table');
}

$command = 'SELECT itemname,quantity AS qty FROM psypets_gamesold WHERE transaction=\'recycled\' ORDER BY quantity DESC';
$recycled = $database->FetchMultiple(($command, 'fetching most-recycled items');

$command = 'SELECT itemname,quantity AS qty FROM psypets_gamesold WHERE transaction=\'greenhoused\' ORDER BY quantity DESC';
$greenhoused = $database->FetchMultiple(($command, 'fetching most-tossed items');

$command = 'SELECT itemname,quantity AS qty FROM psypets_gamesold WHERE transaction=\'pawned\' ORDER BY quantity DESC';
$pawned = $database->FetchMultiple(($command, 'fetching most-pawned items');

$command = 'SELECT itemname,quantity AS qty FROM psypets_gamesold WHERE transaction=\'sold\' ORDER BY quantity DESC';
$gamesold = $database->FetchMultiple(($command, 'fetching most-sold items');

$command = 'SELECT itemname,quantity AS qty FROM psypets_gamesold WHERE transaction=\'tossed\' ORDER BY quantity DESC';
$tossed = $database->FetchMultiple(($command, 'fetching most-tossed items');

$command = 'SELECT itemname,SUM(quantity) AS qty FROM psypets_gamesold GROUP BY itemname ORDER BY qty DESC';
$total = $database->FetchMultiple(($command, 'fetching total discarded items');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Top Throw-away Items</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Top Throw-away Items</h4>
<ul class="tabbed">
 <li class="activetab"><a href="/admin/itemthrowaways.php">Top Throw-away Items</a></li>
 <li><a href="/admin/wanteditems.php">Most-wanted Items</a></li>
 <li><a href="/admin/itemvotes.php">Item Votes</a></li>
</ul>
<?php
if($admin['manageitems'] == 'yes')
  echo '<form method="post" onsubmit="return confirm(\'Really?  Really-really?\');"><p><input type="submit" name="action" value="Clear" /></p></form>';
?>
     <h5>Total</h5>
     <p>Does not include items voted most useless.</p>
     <div style="width:300px; height:200px; overflow: auto; margin-bottom: 1em;">
      <table>
       <thead><tr class="titlerow"><th>Itemname</th><th>Quantity</th></tr></thead>
       <tbody>
<?php
foreach($total as $item)
  echo '<tr><td><a href="/encyclopedia2.php?item=' . link_safe($item['itemname']) . '">' . $item['itemname'] . '</a></td><td class="centered">' . $item['qty'] . '</td></tr>';
?>
       </tbody>
      </table>
     </div>
     <h5>Recycled</h5>
     <p class="dim">The top-recycled items are probably recycled because the materials are valuable.  They should not be used in crafts.  Similarly, the least-recycled items are probably valuable, which is why they are not frequently recycled.</p>
     <div style="width:300px; height:200px; overflow: auto; margin-bottom: 1em;">
      <table>
       <thead><tr class="titlerow"><th>Itemname</th><th>Quantity</th></tr></thead>
       <tbody>
<?php
foreach($recycled as $item)
  echo '<tr><td><a href="/encyclopedia2.php?item=' . link_safe($item['itemname']) . '">' . $item['itemname'] . '</a></td><td class="centered">' . $item['qty'] . '</td></tr>';
?>
       </tbody>
      </table>
     </div>
     <h5>Greenhouse'd</h5>
     <div style="width:300px; height:200px; overflow: auto; margin-bottom: 1em;">
      <table>
       <thead><tr class="titlerow"><th>Itemname</th><th>Quantity</th></tr></thead>
       <tbody>
<?php
foreach($greenhoused as $item)
  echo '<tr><td><a href="/encyclopedia2.php?item=' . link_safe($item['itemname']) . '">' . $item['itemname'] . '</a></td><td class="centered">' . $item['qty'] . '</td></tr>';
?>
       </tbody>
      </table>
     </div>
     <h5>Pawned</h5>
     <div style="width:300px; height:200px; overflow: auto; margin-bottom: 1em;">
      <table>
       <thead><tr class="titlerow"><th>Itemname</th><th>Quantity</th></tr></thead>
       <tbody>
<?php
foreach($pawned as $item)
  echo '<tr><td><a href="/encyclopedia2.php?item=' . link_safe($item['itemname']) . '">' . $item['itemname'] . '</a></td><td class="centered">' . $item['qty'] . '</td></tr>';
?>
       </tbody>
      </table>
     </div>
     <h5>Gamesold</h5>
     <div style="width:300px; height:200px; overflow: auto; margin-bottom: 1em;">
      <table>
       <thead><tr class="titlerow"><th>Itemname</th><th>Quantity</th></tr></thead>
       <tbody>
<?php
foreach($gamesold as $item)
  echo '<tr><td><a href="/encyclopedia2.php?item=' . link_safe($item['itemname']) . '">' . $item['itemname'] . '</a></td><td class="centered">' . $item['qty'] . '</td></tr>';
?>
       </tbody>
      </table>
     </div>
     <h5>Tossed</h5>
     <div style="width:300px; height:200px; overflow: auto; margin-bottom: 1em;">
      <table>
       <thead><tr class="titlerow"><th>Itemname</th><th>Quantity</th></tr></thead>
       <tbody>
<?php
foreach($tossed as $item)
  echo '<tr><td><a href="/encyclopedia2.php?item=' . link_safe($item['itemname']) . '">' . $item['itemname'] . '</a></td><td class="centered">' . $item['qty'] . '</td></tr>';
?>
       </tbody>
      </table>
     </div>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
