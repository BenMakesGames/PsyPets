<?php
$whereat = 'home';
$wiki = 'My_House#Inventory_Summary';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';

if(!addon_exists($house, 'Basement'))
{
  header('Location: ./housesummary.php');
  exit();
}

$command = '
  SELECT quantity,itemname
  FROM psypets_basement
  WHERE userid=' . $user['idnum'] . '
  ORDER BY itemname ASC
';
$inventory = $database->FetchMultiple($command, 'fetching basement inventory');

foreach($inventory as $item)
  $total_items += $item['quantity'];

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Inventory Summary</title>
<?php include 'commons/head.php'; ?>
  <style type="text/css">
   .col2 { padding-left: 2em; }
   h6 { padding-top: 1em; }
  </style>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<h4><a href="/myhouse/addon/basement.php"><?= $user['display'] ?>'s Basement</a> &gt; Inventory Summary</h4>
<ul class="tabbed">
 <li><a href="housesummary.php">House Summary</a></li>
 <li class="activetab"><a href="basementsummary.php">Basement Summary</a></li>
 <li><a href="storagesummary.php">Storage Summary</a></li>
</ul>
<?php
if(count($inventory) > 0)
{
?>
<table>
 <tr><td colspan="4"><h5 class="separator"><a href="/myhouse/addon/basement.php">Basement</a> (<?= $total_items ?> items)</h5></td></tr>
 <tr class="titlerow"><th>Qty</th><th>Item</th><th class="col2">Qty</th><th>Item</th></tr>
<?php
  $col = 1;
  $items = 0;
  $row_class = begin_row_class();

  foreach($inventory as $item)
  {
    if($col == 1)
    {
      echo '<tr class="' . $row_class . '"><td class="centered">';
      $row_class = alt_row_class($row_class);
    }
    else
      echo '<td class="col2 centered">';

    echo $item['quantity'] . '</td><td>' . $item['itemname'] . '</td>';

    if($col == 2)
    {
      echo '</tr>';
      $col = 1;
    }
    else
      $col = 2;
  }

  if($col == 2)
    echo '<td colspan="2"></td></tr>';
?>
</table>
<ul><li><a href="myhouse.php">Back to my basement</a></li></ul>
<?php
}
else
  echo '<p>You don\'t have any items in your basement.  Not a single one.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
