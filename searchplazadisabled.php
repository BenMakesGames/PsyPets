<?php
$require_petload = 'no';
$child_safe = false;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/threadfunc.php';
require_once 'commons/userlib.php';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Plaza Forums &gt; Search</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="plaza.php">Plaza Forums</a> &gt; Search</h4>
     <p>Plaza search is disabled while I work on making it, you know, work :P</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
