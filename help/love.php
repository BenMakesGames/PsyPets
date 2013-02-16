<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_login = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/formatting.php";

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Love</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Love</h4>
     <p>A pet's Love decreases by 1 per hour.  When it gets to 0 or below, the pet whines at you, and the increase of Esteem and experience points becomes impossible.</p>
     <p>A sleeping pet does not lose any Love.</p>
     <p>As a pet feels less loved, it is more likely to spend its hourly action being comforted by love items in the house (plushies, for example).</p>
     <p>Many foods provide Love when fed to a pet, and most half-hourly actions - including petting - provide Love, when taken.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
