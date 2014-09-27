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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Park Events &gt; Tug of War</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Park Events &gt; Picturades</h4>
     <p>In Picturades, pets are divided into two teams.  Alternating between the two teams, each pet draws a card with a noun written on it.  The pet draws it, and all the other pets attempt to guess what it is.  The first pet to guess correctly scores 1 point for his or her team.</p>
     <p>When every pet has had an opportunity to draw, the game is over.  In the event of a tie, a bonus round is held.</p>
     <p>Picturades test the following skills:</p>
     <ul>
      <li>Drawing and painting</li>
      <li>Quick wits</li>
      <li>Manual dexterity</li>
      <li>Perception</li>
      <li>Openness to new ideas</li>
     </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
