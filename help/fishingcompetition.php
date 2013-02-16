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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Park Events &gt; Fishing</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Park Events &gt; Fishing</h4>
     <p>In a Fishing competition, pets compete to catch the largest fish possible.</p>
     <p>To succeed, a pet should be skilled in:</p>
     <ul>
      <li>Fishing</li>
      <li>Manual dexterity</li>
      <li>Stealth</li>
     </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
