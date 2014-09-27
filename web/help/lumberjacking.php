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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Hourly Activities &gt; Lumberjacking</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Hourly Activities &gt; Lumberjacking</h4>
     <h5>What it Accomplishes</h5>
     <p>A lumberjack cuts trees, not only for food, but other resources produced by trees.</p>
     <p>You can expect a lumberjack to bring home:</p>
     <ul>
      <li>Wood</li>
      <li>Amber</li>
      <li>Fruit</li>
      <li>Leaves</li>
     </ul>
     <h5>What it Requires</h5>
     <p>A lumberjack must not only be strong and hardy, but perceptive.  Having wilderness knowledge also helps!</p>
     <p>Lumberjacking and <a href="/help/gather.php">Gathering</a> both rely on the same wilderness knowledge to perform well.</p>
     <h5>Recommended Equipment</h5>
     <p>Axes are <em>the</em> best equipment for lumberjacks.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
