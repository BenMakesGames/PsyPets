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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Park Events &gt; Arts & Crafts Competition</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Park Events &gt; Arts & Crafts Competition</h4>
     <p>In an Arts & Crafts Competition, pets create crafts out of the limited resources available.  A judging panel assigns a score to each project, and the pet that receives the most points wins.</p>
     <p>Arts & Crafts Competitions test the following skills:</p>
     <ul>
      <li>The ability to think quickly and creatively</li>
      <li>Manual dexterity</li>
      <li>Handicrafting skill</li>
     </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
