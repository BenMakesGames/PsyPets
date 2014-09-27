<?php
$require_login = "no";
$require_petload = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/sessions.php";
require_once "commons/rpgfunctions.php";
require_once "commons/formatting.php";

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Under Construction</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <center><img src="gfx/construction.png" /></center>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
