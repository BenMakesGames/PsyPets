<?php
require_once 'commons/init.php';

$require_login = 'no';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Hourly Activities &gt; Gardening and Farming</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Hourly Activities &gt; Gardening and Farming</h4>
     <h5>What it Accomplishes</h5>
     <p>A pet that gardens may work on any gardening projects you have set up at home, or in your Greenhouse or Farm add-ons, if you have them.</p>
     <p>Gardening primarily produces flowers, fruits, and grains.  A few other items can also be grown.</p>
     <h5>What it Requires</h5>
     <p>Perceptive, intelligent, and tough pets with extensive wilderness knowledge make the best gardeners and farmers.</p>
     <p>Pets can never start their own gardening projects; almost all gardening projects are started by using a seed item, and require a <a href="/encyclopedia2.php?item=Clay+Pot">Clay Pot</a>.</p>
     <h5>Recommended Equipment</h5>
     <p>Shovels, watering cans, and gloves will all suit a gardener.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
