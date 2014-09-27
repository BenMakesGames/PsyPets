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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Park Events &gt; Brawl</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Park Events &gt; Brawl</h4>
     <p>In a Brawl, pets engage in free-for-all brawl.  The last pet standing is the winner.</p>
     <p>A Brawl tests the following skills:</p>
     <ul>
      <li>Strength</li>
      <li>Toughness</li>
      <li>Athletics</li>
      <li>The ability to think quickly</li>
      <li>Martial arts</li>
     </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
