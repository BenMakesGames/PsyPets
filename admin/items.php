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
require_once "commons/userlib.php";

if($admin['clairvoyant'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$item_entries = array();

$command = "SELECT monster_items.graphic,monster_inventory.* FROM monster_items,monster_inventory WHERE monster_inventory.itemname=monster_items.itemname AND monster_items.rare='yes' ORDER BY monster_inventory.idnum ASC";
$result = mysql_query($command);
if(!$result)
{
  echo "adminitems.php<br />\n" .
       "Error in <i>$command</i><br />\n" .
       mysql_error() . "<br />\n";
  exit();
}

while($item = mysql_fetch_assoc($result))
  $item_entries[] = $item;

mysql_free_result($result);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Unique Item Finder</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Unique Item Finder</h4>
<?php
 if($error_message)
   echo "<p style=\"color:red;\">" . $error_message . "</p>\n";
?>
     <table>
      <tr class="titlerow">
       <th>&nbsp;</th>
       <th>Item Name</th>
       <th>Inventory ID</th>
       <th>Comment</th>
       <th>Account</th>
      </tr>
<?php
foreach($item_entries as $item)
{
?>
      <tr>
       <td align="center"><img src="/gfx/items/<?= $item["graphic"] ?>" /></td>
       <td><?= $item['itemname'] ?></td>
       <td><?= $item['idnum'] ?></td>
       <td><?= $item['message'] . ' / ' . $item['message2'] ?></td>
       <td><a href="/admin/resident.php?user=<?= $item['user'] ?>"><?= $item['user'] ?></a></td>
      </tr>
<?php
}
?>
     </table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
