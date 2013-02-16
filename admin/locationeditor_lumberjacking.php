<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

if($admin["manageitems"] != "yes")
{
  header("Location: /admin/tools.php");
  exit();
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Location Editor</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Location Editor</h4>
     <ul class="tabbed">
      <li><a href="locationeditor_gathering.php">Gathering</a></li>
      <li><a href="locationeditor_mines.php">Mining</a></li>
      <li class="activetab"><a href="locationeditor_lumberjacking.php">Lumberjacking</a></li>
     </ul>
     <ul>
      <li><a href="/admin/newlocation.php">New location</a></li>
     </ul>
<table>
<tr class="titlerow">
 <th></th>
 <th>Level</th>
 <th>Name</th>
 <th>Loot</th>
 <th>Drop Rate</th>
 <th>(Expected)</th>
 <th>Average <span class="money">m</span></th>
 <th>(Expected)</th>
</tr>
<?php
$result = mysql_query('SELECT * FROM psypets_locations WHERE type=\'lumberjack\' ORDER BY level ASC');

$bgcolor = begin_row_class();

while($location = mysql_fetch_assoc($result))
{
  $prizes = take_apart(',', $location['prizes']);

  $real_prizes = $prizes;
?>
<tr class="<?= $bgcolor ?>">
 <td valign="top"><a href="/admin/editlocation.php?idnum=<?= $location['idnum'] ?>">edit</a></td>
 <td valign="top" class="centered"><?php
  echo $location['level'];

  if($location['needs_key'] > 0)
    echo '<br />(key #' . $location['needs_key'] . ')';
?></td>
 <td valign="top"><?= $location['name'] ?><br /><i class="dim"><?= $location['type'] ?></i></td>
 <td valign="top"><?php
  $drop_rate = 1.00;
  $moneys_value = 0;
  foreach($real_prizes as $prize)
  {
    $rate = explode('|', $prize);

    $details = get_item_byname($rate[1]);
    
    $moneys_value += ceil($details['value'] * sellback_rate()) * ($rate[0] / 1000);
    
    $drop_rate *= (1 - ($rate[0] / 1000));
    echo ($rate[0] / 10) . "% - <a href=\"/encyclopedia2.php?item=" . link_safe($rate[1]) . "\">" . $rate[1] . "</a><br />";

    $expected_moneys_value = 10 + $location['level'] * 2 + floor($location['level'] * $location['level'] / 15);
  }
 ?></td>
 <td valign="top" align="right"><?= 100 - round($drop_rate * 100) ?>%</td>
 <td valign="top" align="right">(<?= 70 + $location['level'] ?>-<?= 80 + $location['level'] ?>%)</td>
 <td valign="top" class="centered"><?= $moneys_value ?></td>
 <td valign="top" class="centered">(<?= $expected_moneys_value * .90 ?>-<?= $expected_moneys_value * 1.1 ?>)</td>
</tr>
<?php
  $bgcolor = alt_row_class($bgcolor);
}
?>
</table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
