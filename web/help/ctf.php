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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Park Events &gt; Capture the Flag</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Park Events &gt; Capture the Flag</h4>
     <p>In Capture the Flag, pets are divded into two teams.  Each team claims half of a field, and hides their flags somewhere in their half.</p>
     <p>The pets then attempt to find the flag of the opposing team, and bring it back to their side.  The team that does so wins.</p>
     <p>A pet in enemy territory may be tagged by an opponent and forced to walk - slowly - back to their side.  If a tagged pet had the flag, the flag is returned to its hiding spot.</p>
     <p>Capture the Flag tests the following skills:</p>
     <ul>
      <li>Athletics</li>
      <li>Strength</li>
      <li>Stamina</li>
      <li>Stealth</li>
      <li>Perception</li>
      <li>Intelligence</li>
     </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
