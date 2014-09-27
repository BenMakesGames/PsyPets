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
require_once 'commons/threadfunc.php';

if($admin['coder'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Database Dump Tools</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
 <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Database Dump Tools</h4>
 <ul>
  <li><a href="/admin/dumpdb_dumpstructure.php?tables=*">Dump structure for entire DB</a></li>
 </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
