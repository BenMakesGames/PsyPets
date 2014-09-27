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

if($admin["clairvoyant"] != "yes")
{
  header('Location: /admin/tools.php');
  exit();
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Item Stats</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Item Stats</h4>
     <h5>Durability Scale</h5>
     <ul>
      <li>600 - High Magic and Giamonds</li>
      <li>500 - Hyper Tech/Magic</li>
      <li>400 - Metal (and living plants)</li>
      <li>300 - Wood</li>
      <li>200 - Strong Paper? (kites; things which get thrown around, like shuriken)</li>
      <li>100 - Paper</li>
     </ul>
<table border=0 cellspacing=0 cellpadding=4>
<tr class="titlerow">
 <th>Item</th>
 <th>Durability</th>
 <th>Rare?</th>
 <th>Combine?</th>
</tr>
<?php
$command = "SELECT * FROM monster_items WHERE 1 ORDER BY itemname ASC";
$result = mysql_query($command);

$bgcolor = begin_row_color();

while($item = mysql_fetch_assoc($result))
{
?>
<tr bgcolor="<?= $bgcolor ?>">
 <td><?= $item["itemname"] ?></td>
 <td align="right"><?= $item["durability"] > 0 ? $item["durability"] : "--" ?></td>
 <td align="center"><?= $item["rare"] ?></td>
 <td align="center"><?= $item["cancombine"] ?></td>
</tr>
<?php
  $bgcolor = alt_row_color($bgcolor);
}

mysql_free_result($result);
?>
</table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
