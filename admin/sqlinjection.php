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

if($admin['manageitems'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$itemname = chr(0xbf) . chr(0x27) . ' OR itemname = itemname -- '; 

$item_results = $database->FetchMultiple(('
  SELECT itemname
  FROM monster_items
  WHERE itemname=' . quote_smart($itemname) . '
');

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &nbsp; Administrative Tools &nbsp; SQL Injection Attempt</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; SQL Injection Attempt</h4>
<p>Item name: <input type="text" disabled="disabled" value="<?= $itemname ?>" style="width:200px;" /><p>
<p><b>Should yield 0 results.  Yielded <?= count($item_results) ?>.</b></p>
<p>If any results were found, the configuration is vulnerable to this kind of SQL injection!</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
