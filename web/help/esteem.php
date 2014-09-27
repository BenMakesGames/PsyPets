<?php
require_once 'commons/init.php';

$require_login = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/formatting.php";

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Esteem</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Esteem</h4>
     <p>A pet's Esteem decreases by 1 per hour, plus 1 for every 10 levels of the pet.  When it gets to 0 or below, the pet feels depressed, and gaining experience points becomes impossible.</p>
     <p>A sleeping pet does not lose any Esteem.</p>
     <p>As a pet feels less esteemed, it is more likely to spend its hourly action being reassured by esteem items in the house (paintings, vases, and other treasures, for example).</p>
     <p>A few, rare foods provide Esteem when fed to a pet, and several half-hourly actions - including petting - provide Esteem, when taken.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
