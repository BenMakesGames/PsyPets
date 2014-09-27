<?php
$require_login = "no";
$invisible = "yes";
$require_petload = 'no';

header('HTTP/1.0 403 Forbidden');

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/inventory.php";
require_once "commons/formatting.php";

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; 403</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Access Denied</h4>
     <p>You do not have permission to access that page.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
