<?php
$IGNORE_MAINTENANCE = true;

require_once 'commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/maillib.php';

if($admin['abusewatcher'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$idnum = (int)$_GET['idnum'];

$command = 'SELECT * FROM psypets_abusereports WHERE idnum=' . $idnum . ' LIMIT 1';
$report = $database->FetchSingle($command, 'fetching psymail abuse reports');

if($report === false)
{
  header('Location: /admin/abusereports.php');
  exit();
}

if($_POST['submit'] == 'Clear Report')
{
  $command = 'DELETE FROM psypets_abusereports WHERE idnum=' . $idnum . ' LIMIT 1';
  $database->FetchNone($command, 'deleting report');
  
  header('Location: /admin/abusereports.php');
  exit();
}

$mail = get_mail_byid($report['threadid']);

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Abuse Reports &gt; PsyMail</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; <a href="/admin/abusereports.php">Abuse Reports</a> &gt; PsyMail</h4>
<div><?= $report['comment'] ?></div>
<br /><hr />
<h5>Original PsyMail</h5>
<?php
if($mail === false)
  echo '<p>Original PsyMail has been deleted.</p>';
else
{
  echo '<p><pre>';
  print_r($mail);
  echo '</pre></p>';
}
?>
<form action="/admin/abusereports_maildetails.php?idnum=<?= $idnum ?>" method="post"><p><input type="submit" name="submit" value="Clear Report" class="bigbutton" /></p></form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
