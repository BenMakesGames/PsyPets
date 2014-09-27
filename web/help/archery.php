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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Park Events &gt; Archery Competition</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Park Events &gt; Archery Competition</h4>
     <p>In an Archery Competition, pets earn points by shooting targets accurately.  The pet that receives the most points wins.</p>
     <p>Archery Competitions test the following skills:</p>
     <ul>
      <li>Manual dexterity</li>
      <li>Athletics</li>
     </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
