<?php
$require_login = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/formatting.php";

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Feature Disabled Temporarily</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Feature Disabled Temporarily</h4>
     <p>The page you were trying to access has been disabled, temporarily, while I recover lost user accounts.</p>
     <p>Why?  To prevent further data-loss.  Interacting with player accounts that only partially exist could result in very weird and wrong behaviors.</p>
     <p>I will have these parts of the site back up as soon as possible.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
