<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

$require_petload = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

if($admin['clairvoyant'] != 'yes')
{
  header('Location: /');
  exit();
}

$list = $database->FetchMultiple('
  SELECT name,SUM(value) AS total,MIN(date) AS first_occurance FROM psypets_daily_report_stats
  GROUP BY name
');

function popularity_sort($a, $b)
{
  if($a['average_per_day'] == $b['average_per_day'])
    return 0;
  else
    return ($a['average_per_day'] < $b['average_per_day'] ? 1 : -1);
}

foreach($list as $i=>$item)
{
  $list[$i]['days'] = ($now - strtotime($item['first_occurance'])) / (24 * 60 * 60);
  $list[$i]['average_per_day'] = $item['total'] / $list[$i]['days'];
}

usort($list, 'popularity_sort');

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &nbsp; Administrative Tools &nbsp; Daily Statistics</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Daily Statistics</h4>
  <table>
   <thead>
    <tr>
     <th>Stat</th><th>First Occurance</th><th>Total Value</th><th>Average per Day</th>
    </tr>
   </thead>
   <tbody>
<?php
$rowclass = begin_row_class();

foreach($list as $item)
{
?>
    <tr class="<?= $rowclass ?>">
     <td><?= $item['name'] ?></td>
     <td><?= $item['first_occurance'] ?><br /><i class="size8 dim"><?= floor($item['days']) ?> days ago</i></td>
     <td><?= $item['total'] ?></td>
     <td><?= round($item['average_per_day'], 2) ?></td>
    </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
   </tbody>
  </table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
