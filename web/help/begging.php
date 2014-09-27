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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Hourly Activities &gt; Begging</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Hourly Activities &gt; Begging</h4>
     <h5>What it Accomplishes</h5>
     <p>Pets beg when they are hungry, there is no food in the house, and they either don't have the skill to hunt or gather food, or the house is so full that there is no room to place any hunted or gathered foods.</p>
     <p>Begging does not bring anything home.  Any food the pet gains through begging is consumed immediately by that pet.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
