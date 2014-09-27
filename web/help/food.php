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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Food</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Food</h4>
     <p>A pet's Food decreases by 1 per hour.  When it gets to 0 or below, the pet becomes starving, and the increase of Safety, Love, Esteem, and experience points becomes impossible.</p>
     <p>A sleeping pet's Food decreases at a slower rate.</p>
     <p>As a pet becomes hungry, it is more likely to spend its hourly action eating food in the house.  If there is no food in the house, it may attempt to Hunt or Gather some.</p>
     <p>By hand-feeding a pet, you prevent the pet from wasting hourly actions worrying about food (pets also feel loved when you hand-feed them).</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
