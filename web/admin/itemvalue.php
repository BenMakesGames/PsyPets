<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

// DISABLED
// Header("Location: /");

$require_petload = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";
require_once 'commons/economylib.php';

if($admin["clairvoyant"] != "yes")
{
  Header("Location: /admin/tools.php");
  exit();
}

switch($_GET['sort'])
{
  case 1: $order_by = 'value ASC'; break;
  default: $order_by = 'itemname ASC'; break;
}

$command = 'SELECT COUNT(*) AS c FROM monster_items WHERE custom=\'no\'';
$data = $database->FetchSingle($command, 'fetching item count');

$num_items = (int)$data['c'];

$num_pages = ceil($num_items / 100);
$page = (int)$_GET['page'];
if($page < 1 || $page > $num_pages)
  $page = 1;

$page_list = paginate($num_pages, $page, '/admin/itemvalue.php?page=%s');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Market Analyzer</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Market Analyzer</h4>
     <?= $page_list ?>
<table>
<tr class="titlerow">
 <th>Item</th>
 <th>No. in Game</th>
 <th>Current Value</th>
 <th>Recycle Value</th>
</tr>
<?php
$command = 'SELECT itemname,value,recycle_for,recycle_fraction FROM monster_items WHERE custom=\'no\' ORDER BY ' . $order_by . ' LIMIT ' . (($page - 1) * 100) . ',100';
$result = mysql_query($command);

$bgcolor = begin_row_class();

while($item = mysql_fetch_assoc($result))
{
  $command = 'SELECT COUNT(*) FROM monster_inventory WHERE itemname=' . quote_smart($item['itemname']);
  $data = $database->FetchSingle($command, 'encyclopedia2.php');
  $number_in_game = (int)$data['COUNT(*)'];

  $command = 'SELECT SUM(quantity) FROM psypets_basement WHERE itemname=' . quote_smart($item['itemname']) . ' GROUP BY itemname';
  $data = $database->FetchSingle($command, 'encyclopedia2.php');
  $number_in_basements = (int)$data['SUM(quantity)'];

  $total_in_existance = $number_in_game + $number_in_basements;
?>
<tr class="<?= $bgcolor ?>">
 <td><a href="/encyclopedia2.php?i=<?= $item['idnum'] ?>"><?= $item['itemname'] ?></a></td>
 <td><?= $total_in_existance ?></td>
 <td><?= $item['value'] ?></td>
 <td><?= recycle_value($item) ?></td>
</tr>
<?php
  $bgcolor = alt_row_class($bgcolor);
}
?>
<tr class="<?= $bgcolor ?>">
 <th>Total</th>
 <td></td>
 <td></td>
 <td></td>
</tr>
</table>
     <?= $page_list ?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
