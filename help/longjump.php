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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Park Events &gt; Long Jump</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Park Events &gt; Long Jump</h4>
     <p>In a Long Jump, pets compete to jump further than any other pet.</p>
     <p>A Long Jump tests the following skills:</p>
     <ul>
      <li>Athletics</li>
      <li>Strength</li>
     </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
