<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

// mail both of these to me daily
//
//   df -h
//   ls -s /mnt/backup/db/dump.db/schreider/*.sql
//
// ... | mail -s "subject" -a From:sender@psypets.net admin@psypets.net

require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';

if($admin['seeserversettings'] != 'yes')
{
  header('Location: /');
  exit();
}

$last = explode('-', file_get_contents('queries.txt'));
$timestamp_old = $last[0];
$queries_old = $last[1];

$row = mysql_fetch_assoc(mysql_query("show status like 'Questions'"));
$queries_new = $row['Value'];
$timestamp_new = time();

$qps = round(($queries_new - $queries_old) / ($timestamp_new - $timestamp_old));

if($_POST['action'] == 'Restart Timer')
{
  $fhandle = fopen('queries.txt', 'w');
  fwrite($fhandle, $timestamp_new . '-' . $queries_new);
  fclose($fhandle);
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &nbsp; Administrative Tools &nbsp; Queries Per Second</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Queries Per Second</h4>

<table>
 <tr><th>Last timestamp</th><td><?= date('F j, Y, g:i a', $timestamp_old) ?></td></tr>
 <tr><th>Querries at that time</th><td><?= $queries_old ?></td></tr>
 <tr><th>Current timestamp</th><td><?= date('F j, Y, g:i a', $timestamp_new) ?></td></tr>
 <tr><th>Querries now</th><td><?= $queries_new ?></td></tr>
 <tr><th>Time passed</th><td><?= duration($timestamp_new - $timestamp_old, 3) ?></td></tr>
 <tr><th>Querries per second</th><td><?= $qps ?></td></tr>
</table>
<form action="adminqps.php" method="post">
<p><input type="submit" name="action" value="Restart Timer" class="bigbutton" /></p>

<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
