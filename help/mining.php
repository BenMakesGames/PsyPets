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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Hourly Activities &gt; Mining</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Hourly Activities &gt; Mining</h4>
     <h5>What it Accomplishes</h5>
     <p>A pet that mines explores the outdoors looking for mineral resources.</p>
     <p>You can expect a miner to bring home:</p>
     <ul>
      <li>Metals, gems, and other minerals</li>
      <li>Clay and rocks</li>
      <li>Baking soda</li>
     </ul>
     <h5>What it Requires</h5>
     <p>To mine, a pet must not only be strong and hardy, but perceptive.  Studying geology also helps!</p>
     <h5>Recommended Equipment</h5>
     <p>Shovels and picks are the best equipment for miners.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
