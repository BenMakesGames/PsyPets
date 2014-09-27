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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Park Events &gt; Cook-Off</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Park Events &gt; Cook-Off</h4>
     <p>In a Cook-Off, pets must create food using unknown ingredients.    A judging panel assigns a score to each dish, and the pet that receives the most points wins.</p>
     <p>A Cook-Off tests the following skills:</p>
     <ul>
      <li>Intelligence and the ability to think quickly</li>
      <li>Manual dexterity</li>
      <li>An eye for detail</li>
      <li>Handicrafting skill</li>
      <li>A willingness to try new ideas</li>
      <li>Conscientiousness</li>
     </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
