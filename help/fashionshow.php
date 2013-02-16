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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Park Events &gt; Fashion Show</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Park Events &gt; Fashion Show</h4>
     <p>In a Fashion Show pets compete to create the best outfits from scratch.</p>
     <p>To succeed, a pet should be skilled in:</p>
     <ul>
      <li>Tailory</li>
      <li>Jeweling</li>
      <li>Manual dexterity</li>
      <li>Perception</li>
     </ul>
     <p>An openness to new ideas is also important.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
