<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_login = 'no';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Hourly Activities &gt; Gathering</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Hourly Activities &gt; Gathering</h4>
     <h5>What it Accomplishes</h5>
     <p>A pet that gathers explores the outdoors looking for food, plants, and other natural resources.</p>
     <p>You can expect an gatherer to bring home:</p>
     <ul>
      <li>Leaves and flowers</li>
      <li>Fruits and vegetables</li>
      <li>Eggs, shells, fluff, and other animal products</li>
     </ul>
     <h5>What it Requires</h5>
     <p>To gather, a pet should be perceptive, intelligent, rugged, and have extensive wilderness knowledge.</p>
     <p>Gathering and <a href="/help/lumberjacking.php">Lumberjacking</a> both rely on the same wilderness knowledge to perform well.</p>
     <h5>Recommended Equipment</h5>
     <p>Flashlights, wagons, maps, and compasses all help a pet get around in the wild.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
