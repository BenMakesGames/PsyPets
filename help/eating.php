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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Hourly Activities &gt; Eating</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Hourly Activities &gt; Eating</h4>
     <h5>What it Accomplishes</h5>
     <p>Pets eat when they are hungry, provided there is food in the house to eat.</p>
     <p>When a pet eats, it consumes food - nothing is created.</p>
     <p>It should be noted that hand-feeding a pet makes a pet feel loved, however a pet that eats on its own does not receive that benefit.  It is therefore preferable to hand feed your pets, when you can.</p>
     <h5>What it Requires</h5>
     <p>Food must be present in the house (and not in a protected room) for a pet to eat.  If a pet is hungry, but there is no food available, it may attempt to get some on its own.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
