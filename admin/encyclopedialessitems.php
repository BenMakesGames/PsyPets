<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

// DISABLED
// Header("Location: /");

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

if($admin['clairvoyant'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$items = $database->FetchMultiple('
  SELECT idnum,itemname,graphic,graphictype
  FROM `monster_items`
  WHERE enc_entry=\'\'
  AND custom=\'no\'
  ORDER BY RAND()
  LIMIT 10
');

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Items Without Encyclopedia Entries</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Items Without Encyclopedia Entries</h4>
     <p>Here are 10 random standard-availability items without encyclopedia entries.  Tragic!</p>
<table>
<?php
$rowclass = begin_row_class();
foreach($items as $item)
{
  echo '<tr class="' . $rowclass . '"><td class="centered">' . item_display($item) . '</td><td>' . $item['itemname'] . '</td></tr>';
  $rowclass = alt_row_class($rowclass);
}
?>
</table>
<ul><li><a href="/admin/encyclopedialessitems.php">Show me another set!</a></li></ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
