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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Hourly Activities &gt; Leather-working</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Hourly Activities &gt; Leather-working</h4>
     <h5>What it Accomplishes</h5>
     <p>Leather-working creates tools and clothing made from leather.</p>
     <p>Materials needed:</p>
     <ul>
      <li>Leather</li>
      <li>Dyes</li>
      <li>Fluff</li>
     </ul>
     <p>Crafts produced:</p>
     <ul>
      <li>Hats, belts, shoes, and other clothing</li>
      <li>Whips and slings</li>
      <li>Parchment</li>
     </ul>
     <h5>What it Requires</h5>
     <p>Tailors must have steady hands, an eye for detail, intelligence, and a knowledge of leather-working itself.</p>
     <p>Some tailory projects are very unusual, and require a pet with a mind open to new ideas in order to create.</p>
     <h5>Recommended Equipment</h5>
     <p>Scissors, small hammers, and needles are the tools of a leatherworker.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
