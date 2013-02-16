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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Park Events &gt; Tug of War</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Park Events &gt; Tug of War</h4>
     <p>In Tug of War, pets are divided into two teams.  Each team grabs hold of one end of the same rope, and a line is drawn between the two.  Each team then attempts to pull the entire opposing team over the line.  When one team has passed entirely over the line, the other team is declared the winner.</p>
     <p>Tug of War tests the following skills:</p>
     <ul>
      <li>Strength and endurance</li>
      <li>Athletic training</li>
      <li>Balance</li>
     </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
