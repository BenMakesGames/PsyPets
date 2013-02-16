<?php
// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/formatting.php";

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; EXP to Level</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h5>EXP to Level</h5>
     <table>
      <tr class="titlerow">
       <td><p><b>Level</b></p></td>
       <td><p><b>EXP</b></p></td>
      <tr>
<?php
  for($i = 1; $i < 100; ++$i)
  {
?>
      <tr>
       <td align="center"><p><?= $i ?></p></td>
       <td align="right"><p><?= level_exp($i) ?></p></td>
      </tr>
<?php
  }
?>
     </table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
