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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Hourly Activities &gt; Fishing</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Hourly Activities &gt; Fishing</h4>
     <h5>What it Accomplishes</h5>
     <p>Fishers catch fish, of course, for food and other animal resources.</p>
     <p>You can expect an fish to bring home:</p>
     <ul>
      <li>Fish meat</li>
      <li>Scales</li>
      <li>Leather</li>
     </ul>
     <h5>What it Requires</h5>
     <p>A fisher needs to know about fish and fishing techniques, but should also be fast, perceptive, and a little sneaky.</p>
     <h5>Recommended Equipment</h5>
     <p>Fishing poles, spears, and even nets are good picks for a fisher.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
