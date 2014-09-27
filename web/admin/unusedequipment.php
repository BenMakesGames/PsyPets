<?php
require_once 'commons/init.php';

$require_petload = 'no';
$IGNORE_MAINTENANCE = true;


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

$command = '
  SELECT a.itemname,COUNT(b.idnum) AS qty
  FROM
  (
    psypets.monster_pets AS b LEFT JOIN psypets.monster_inventory AS a ON b.toolid=a.idnum
  )
  LEFT JOIN psypets.monster_items AS c ON a.itemname=c.itemname
  WHERE c.custom=\'no\'
  GROUP BY a.itemname
  ORDER BY qty DESC
';
$popular_equipment = $database->FetchMultiple($command, 'fetching equipment');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Most Popular Equipment</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Most Popular Equipment</h4>
     <table>
      <tr class="titlerow">
       <th></th><th>Item</th><th>No. Equipped</th><th>No. In Game</th><th>% Equipped</th>
      </tr>
<?php
$rowclass = begin_row_class();

foreach($popular_equipment as $item)
{
  $details = get_item_byname($item['itemname']);

  $command = 'SELECT COUNT(*) FROM monster_inventory WHERE itemname=' . quote_smart($item['itemname']);
  $data = $database->FetchSingle($command, 'number in game');
  $number_in_game = (int)$data['COUNT(*)'];

  $command = 'SELECT SUM(quantity) FROM psypets_basement WHERE itemname=' . quote_smart($item['itemname']) . ' GROUP BY itemname';
  $data = $database->FetchSingle($command, 'number in basements');
  $number_in_basements = (int)$data['SUM(quantity)'];

  $total_in_existance = $number_in_game + $number_in_basements;
?>
      <tr class="<?= $rowclass ?>">
       <td class="centered"><?= item_display_extra($details) ?></td>
       <td><?= $item['itemname'] ?></td>
       <td class="righted"><?= $item['qty'] ?></td>
       <td class="righted"><?= $total_in_existance ?></td>
       <td class="righted"><?= sprintf('%01.2f', $item['qty'] / $total_in_existance) ?>%</td>
      </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
     </table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
