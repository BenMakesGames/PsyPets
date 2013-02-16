<?php
$require_login = "no";
$require_petload = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/encryption.php';
require_once 'commons/formatting.php';
require_once 'commons/sitestatslib.php';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Statistics</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
    <h4>Statistics</h4>
<?php
$stats = $database->FetchMultiple('
  SELECT
    *,
    numpets/numusers AS pets_per_resident,
    numactivepets/numactiveusers AS active_pets_per_resident,
    numpets-malepets AS female_pets,
    totallevels/numpets AS average_level_per_pet,
    cash+savings AS total_moneys,
    (cash+savings)/numusers AS average_money_per_resident,
    objects/numusers AS items_per_resident
  FROM monster_statistics
  ORDER BY timestamp DESC
  LIMIT 14
');

for($i = 0; $i < 7; ++$i)
{
  $stat = $stats[$i];
?>
    <h5><?= local_date($stat['timestamp'], 0, 'no') ?></h5>
    <table>
     <thead>
      <tr>
       <th>Stat</th><th class="righted">Value</th><th class="righted">&#916; Yesterday</th><th class="righted">&#916; Last Week</th>
      </tr>
     </thead>
     <tbody>
<?php
$rowclass = begin_row_class();

foreach($SITE_STATISTICS_LABELS as $label=>$value)
{
  if($label == 'Comment')
  {
?>
      <tr class="<?= $rowclass ?>">
       <th>Comment</th>
       <td colspan="3"><?= $stat[$value] ?></td>
      </tr>
<?php
  }
  else
  {
    $yesterday_delta = $stat[$value] - $stats[$i + 1][$value];
    $lastweek_delta = $stat[$value] - $stats[$i + 7][$value];

    $decimals = (strpos($label, '/') !== false ? 2 : 0);
?>
      <tr class="<?= $rowclass ?>">
       <th><a href="/statistics_graph.php?data=<?= $value ?>"><?= $label ?></a></th>
       <td class="righted"><?= number_format($stat[$value], $decimals) ?></td>
       <td class="righted"><?= ($yesterday_delta > 0 ? '+' : '') . ($yesterday_delta != 0 ? number_format($yesterday_delta, $decimals) : '<span class="dim">no change</span>') ?></td>
       <td class="righted"><?= ($lastweek_delta > 0 ? '+' : '') . ($lastweek_delta != 0 ? number_format($lastweek_delta, $decimals) : '<span class="dim">no change</span>') ?></td>
      </tr>
<?php
  }
  
  $rowclass = alt_row_class($rowclass);
}
?>
    </table>
<?php
}
?>
    <p>* Dead pets are excluded from all other pet statistics.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
