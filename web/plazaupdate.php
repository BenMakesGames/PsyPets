<?php
$require_login = "no";
$invisible = "yes";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/inventory.php";
require_once "commons/formatting.php";

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Plaza Maintenance</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Plaza Maintenance!</h4>
     <p>The Plaza is currently undergoing an upgrade that requires it to be taken off-line for a little bit.</p>
     <p>Sorry about that.  I'm working as fast as I can!</p>
     <p>I'll keep you guys posted on my progress via the City Hall.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
