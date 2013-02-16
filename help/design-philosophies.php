<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_login = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/formatting.php";
require_once 'commons/bannedurls.php';

include 'commons/html.php';
?>
 <head>
  <title>City Hall &gt; Help Desk &gt; Design Philosophies</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <h4><a href="/cityhall.php">City Hall</a> &gt; <a href="/help/">Help Desk</a> &gt; Design Philosophies</h4>
  <p>If a feature is to be added, it should be compared against these philosophies.  If a feature does not fit within these philosophies, it should not be added.</p>
  <p>If a feature is to be removed (or changed), the same question should be asked: will <em>removing</em> this feature serve these philosophies?</p>

  <!-- describe your design philosophies here -->

<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
