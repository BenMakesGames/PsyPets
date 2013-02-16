<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/economylib.php';

if($admin['manageitems'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$global_factor = get_global('economy_factor');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Market Inflation</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Market Inflation</h4>
     <ul class="tabbed">
      <li class="activetab"><a href="adminmarketinflation.php">Overview</a></li>
      <li><a href="adminmarketinflation2.php">Details</a></li>
     </ul>
<?= $message ?>
     <p>Calculated inflation since Feb 26th, 2009: <?= $factor . ' (' . quote_smart($factor) . ')' ?></p>
     <p>Used inflation value: <?= $global_factor ?></p>
<?php
if($factor != $global_factor)
  echo '<ul><li><a href="adminmarketinflation.php?update=yes">Update used inflation value</a></li></ul>';
?>
<ul>
 <li>Allowance: 50 -> <?= value_with_inflation(50) ?> -> <?= round(50 * $factor) ?></li>
 <li>LtC: 500 -> <?= value_with_inflation(500) ?> -> <?= round(500 * $factor) ?></li>
 <li>BL: 10000 -> <?= value_with_inflation(10000) ?> -> <?= round(10000 * $factor) ?></li>
</ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
