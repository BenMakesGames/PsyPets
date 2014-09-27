<?php
require_once 'commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/economylib.php';

if($admin['manageitems'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

foreach($core_items as $itemname=>$value)
  $items[] = quote_smart($itemname);

$list_command = 'SELECT itemname,idnum FROM monster_items WHERE itemname IN (' . implode(',', $items) . ') LIMIT ' . count($items); 
$item_list = $database->FetchMultiple($list_command, 'fetching item ids');

foreach($item_list as $item)
{
  $command = 'SELECT price AS sellback FROM psypets_market_history WHERE itemid=' . $item['idnum'] . ' ORDER BY timestamp DESC LIMIT 28';
  $data = $database->FetchMultiple($command, 'fetching average in last 4 weeks');

  $total = 0;
  foreach($data as $datum)
    $total += ceil($datum['sellback']);

  $four_week_average[$item['itemname']] = number_format($total / 28, 2);
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Market Inflation</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Market Inflation</h4>
     <ul class="tabbed">
      <li><a href="adminmarketinflation.php">Overview</a></li>
      <li class="activetab"><a href="adminmarketinflation2.php">Details</a></li>
     </ul>

<table>
<tr class="titlerow"><th>Item</th><th>30-Day Average</th></tr>
<?php
$rowclass = begin_row_class();
foreach($four_week_average as $itemname=>$average)
  echo '<tr class="' . ($rowclass = alt_row_class($rowclass)) . '"><td><a href="encyclopedia2.php?item=' . $itemname . '">' . $itemname . '</a></td><td class="centered">' . $average . '</td></tr>';
?>
</table>     
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
