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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Park Events &gt; Roborena</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Park Events &gt; Roborena</h4>
     <p>In a Roborena event, pets build remote-controlled robots on the fly before competing them against each other in a grand melee!</p>
     <p>Roborenas test the following skills:</p>
     <ul>
      <li>Mechanical and electrical engineering</li>
      <li>Smithing</li>
      <li>Manual dexterity, intelligence, and quick wits</li>
     </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
