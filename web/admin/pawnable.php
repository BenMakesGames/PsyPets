<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

// DISABLED
// Header("Location: /");

$require_petload = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";

if($admin["manageitems"] != "yes")
{
  header("Location: /admin/tools.php");
  exit();
}

$command = 'SELECT itemname FROM monster_items WHERE can_pawn_for=\'yes\' AND nosellback=\'yes\'';
$items = $database->FetchMultiple($command, 'fetching standard items that do not recycle');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Items Which May Not Be Sold Back, But Which Can Be Acquired Through The Pawn Shop</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Items Which May Not Be Sold Back, But Which Can Be Acquired Through The Pawn Shop</h4>
<table>
<tr class="titlerow">
 <th>Name</th>
</tr>
<?php
$rowstyle = begin_row_class();

foreach($items as $item)
{
?>
<tr class="<?= $rowstyle ?>">
 <td><?= $item['itemname'] ?></td>
</tr>
<?php
  $rowstyle = alt_row_class($rowstyle);
}
?>
</table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
