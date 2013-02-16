<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

require_once 'commons/admincheck.php';

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";

if($admin['seeserversettings'] != 'yes')
{
  header('Location: /');
  exit();
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &nbsp; Administrative Tools &nbsp; Server Settings</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Server Settings</h4>
<?php
 if($error_message)
   echo "<p style=\"color:red;\">" . $error_message . "</p>\n";
?>
     <h5>MySQL Variables</h5>
     <table border=0 cellspacing=0 cellpadding=4>
      <tr class="titlerow">
       <th>Variable</th>
       <th>Value</th>
      </tr>
<?php
$command = "SHOW VARIABLES";
$result = mysql_query($command);

$rowclass = begin_row_class();

while($data = mysql_fetch_row($result))
{
?>
      <tr class="<?= $rowclass ?>">
       <td><?= $data[0] ?></td>
       <td><?= $data[1] ?></td>
      </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
     </table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
