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

if($_POST['action'] == 'Apply')
  $when_stats =
    str_pad((int)$_POST['y'], 2, '0', STR_PAD_LEFT) . '-' .
    str_pad((int)$_POST['m'], 2, '0', STR_PAD_LEFT) . '-' .
    str_pad((int)$_POST['d'], 2, '0', STR_PAD_LEFT)
  ;
else
  $when_stats = date('Y-m-d');

$events = $database->FetchMultiple('
  SELECT * FROM psypets_daily_report_stats
  WHERE `date`=\'' . $when_stats . '\'
  ORDER BY name ASC
');

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &nbsp; Administrative Tools &nbsp; Daily Statistics</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Daily Statistics</h4>
  <form method="post">
  <p>
   <input type="number" name="y" size="4" min="2011" value="<?= substr($when_stats, 0, 4) ?>" />
   <input type="number" name="m" size="2" min="1" max="12" value="<?= substr($when_stats, 5, 2) ?>" />
   <input type="number" name="d" size="2" min="1" max="31" value="<?= substr($when_stats, 8, 2) ?>" />
   <input type="submit" name="action" value="Apply" />
  </p>   
  </form>
  <table>
   <thead>
    <tr><th>Stat</th><th>Value</th></tr>
   </thead>
   <tbody>
<?php
$rowclass = begin_row_class();
foreach($events as $event)
{
?>
    <tr class="<?= $rowclass ?>">
     <td><?= $event['name'] ?></td>
     <td class="righted"><?= $event['value'] ?></td>
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
