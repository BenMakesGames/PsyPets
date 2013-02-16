<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

// DISABLED
// Header("Location: /");

$require_petload = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";
require_once "commons/marketlib.php";

if($admin["clairvoyant"] != "yes")
{
  Header("Location: /admin/tools.php");
  exit();
}

$validtypes = array_keys($categories);

$command = "SELECT * FROM monster_items WHERE 1";
$result = mysql_query($command);

if(!$result)
{
  echo "adminitemtypes.php<br />\n" .
       "Error in <i>$command</i><br />\n" .
       mysql_error() . "<br />\n";
  exit();
}

while($item = mysql_fetch_assoc($result))
{
  $i = strpos($item["itemtype"], "/");
  if($i === false)
    $type = $item["itemtype"];
  else
    $type = substr($item["itemtype"], 0, $i);

  if(!array_key_exists($type, $categories))
    $baditems[$type][] = $item["itemname"];

  $types[$type]++;

}

mysql_free_result($result);

ksort($types);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Market Analyzer</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Market Analyzer</h4>
<table border=0 cellspacing=0 cellpadding=4>
<tr class="titlerow">
 <th>Item Type</th>
 <th>Number</th>
 <th>Notes</th>
</tr>
<?php
$bgcolor = begin_row_color();

foreach($types as $type=>$count)
{
  if(!array_key_exists($type, $categories))
    $notes = implode("<br />", $baditems[$type]);
  else
    $notes = "";
?>
<tr bgcolor="<?= $bgcolor ?>">
 <td valign="top"><?= $type ?></td>
 <td valign="top"><?= $count ?></td>
 <td valign="top"><?= $notes ?></td>
</tr>
<?php
  $bgcolor = alt_row_color($bgcolor);
}

?>
</table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
