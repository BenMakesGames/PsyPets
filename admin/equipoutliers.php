<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

// DISABLED
// Header("Location: /");

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/equiplib.php';

if($admin['manageitems'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$page = (int)$_GET['page'];
if($page < 1)
  $page = 1;

$command = 'SELECT * FROM monster_items WHERE is_equipment=\'yes\' AND custom=\'no\' ORDER BY itemname ASC LIMIT ' . (($page - 1) * 20) . ',20';
$items = $database->FetchMultiple(($command, 'fetching equipment items');

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Equipment Effect-Availability Outliers</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Equipment Effect-Availability Outliers</h4>
<form action="/admin/equipoutliers.php" method="get">
<p>Page: <input type="text" name="page" maxlength="2" size="2" value="<?= $page ?>" /> <input type="submit" value="Go" /></p>
</form>
<table>
<tr class="titlerow">
 <th>Name</th><th>Equipment Details</th>
 <th class="centered">Equipment Level</th>
 <th class="centered">In Game</th>
 <th class="righted">Sellback Value</th>
 <th>Recycled</th><th>Sold-back</th><th>Pawned</th>
</tr>
<?php
$rowstyle = begin_row_class();

foreach($items as $item)
{
  $command = 'SELECT COUNT(*) FROM monster_inventory WHERE itemname=' . quote_smart($item['itemname']);
  $data = $database->FetchSingle($command, 'encyclopedia2.php');
  $number_in_game = (int)$data['COUNT(*)'];

  $command = 'SELECT SUM(quantity) FROM monster_market WHERE itemname=' . quote_smart($item['itemname']) . ' GROUP BY itemname';
  $data = $database->FetchSingle($command, 'encyclopedia2.php');
  $number_in_market = (int)$data['SUM(quantity)'];

  $command = 'SELECT SUM(quantity) FROM psypets_basement WHERE itemname=' . quote_smart($item['itemname']) . ' GROUP BY itemname';
  $data = $database->FetchSingle($command, 'encyclopedia2.php');
  $number_in_basements = (int)$data['SUM(quantity)'];

  $total_in_existance = $number_in_game + $number_in_basements + $number_in_market;
?>
<tr class="<?= $rowstyle ?>">
 <td><a href="encyclopedia2.php?i=<?= $item['idnum'] ?>"><?= $item['itemname'] ?></a></td>
 <td>
  <?= $item['equipreincarnateonly'] == 'yes' ? 'reincarnate-only' : '' ?>
 </td>
 <td class="centered"><?= EquipLevel($item) ?></td>
 <td class="centered"><?= $total_in_existance ?></td>
 <td class="righted"><?= ceil($item['value'] * sellback_rate()) ?><span class="money">m</span></td>
 <td></td>
 <td></td>
 <td></td>
</tr>
<?php
  $rowstyle = alt_row_class($rowstyle);
}
?>
</table>
<form method="get">
<p>Page: <input type="text" name="page" maxlength="2" size="2" value="<?= $page ?>" /> <input type="submit" value="Go" /></p>
</form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
