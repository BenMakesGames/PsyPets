<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; Item Encyclopedia &gt; <?= $item['itemname'] ?></title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <h4>Thank You!</h4>
  <p>Thanks for supporting PsyPets!</p>
  <p>You should receive an in-game mail confirming everything shortly.  If you don't receive anything within 24 hours, <a href="admincontact.php">let me know</a>!</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
