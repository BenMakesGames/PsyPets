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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Hourly Activities &gt; Sleeping</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Hourly Activities &gt; Sleeping</h4>
     <h5>What it Accomplishes</h5>
     <p>Pets sleep when they are tired, and awake when fully rested.</p>
     <p>You may also attempt to put a pet to bed, or wake it up, however unagreeable pets may not always do as you say.</p>
     <p>A caffeinated pet will not fall asleep.</p>
     <p>Pets that fall asleep to a burning Fireplace house add-on will feel loved and safe.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
