<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";

if($admin["clairvoyant"] != "yes")
{
  header('Location: /');
  exit();
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &nbsp; Administrative Tools &nbsp; Avatar Graphic Use</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Avatar Graphic Use</h4>
<?php
 if($error_message)
   echo "<p style=\"color:red;\">" . $error_message . "</p>\n";
?>
<table>
 <tr class="titlerow">
  <th>Avatar</th>
  <th>Number</th>
 </tr>
<?php
$residents = fetch_multiple("SELECT graphic FROM `monster_users`")

$pet_stats = array();
$total = 0;

foreach($residents as $this_resident)
{
  $resident_stats[$this_resident["graphic"]]++;
  $total++;
}

arsort($resident_stats);

$rowclass = begin_row_class();

foreach($resident_stats as $graphic=>$count)
{
?>
 <tr class="<?= $rowclass ?>">
  <td><img src="gfx/avatars/<?= $graphic ?>" /></td>
  <td align="right"><?= $count ?></td>
 </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
</table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
