<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_login = 'no';
$require_petload = 'no';
$reading_tos = true;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Help &gt; Copyright Information</title>
<?php include "commons/head.php"; ?>
  <style type="text/css">
   .graphiccopy { border-top: 1px solid black; padding: 1em; }
   .graphiccopy img { border: 0; margin: 0; }
  </style>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help</a> &gt; Copyright Information &gt; NPC and Event Graphics</h4>
<ul class="tabbed">
 <li><a href="/meta/copyright.php">General Copyright Information</a></li>
 <li><a href="/meta/copyright_smallgfx.php">Item, Pet and Avatar Graphics</a></li>
 <li class="activetab"><a href="/meta/copyright_largegfx.php">NPC Graphics</a></li>
 <li><a href="/meta/copyright_code.php">Code Libraries</a></li>
</ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
