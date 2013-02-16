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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Hourly Activities &gt; Mechanical Engineering</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Hourly Activities &gt; Mechanical Engineering</h4>
     <h5>What it Accomplishes</h5>
     <p>Mechanical Engineers design and build mechanical devices, from Sewing Machines and Compound Bows to Jet Engines.</p>
     <p>They typically require:</p>
     <ul>
      <li>Plastic and rubber</li>
      <li>Metals</li>
      <li>Wood</li>
     </ul>
     <p>They produce:</p>
     <ul>
      <li>Tools</li>
      <li>Toys</li>
      <li>Guns</li>
      <li>Engines</li>
     </ul>
     <h5>What it Requires</h5>
     <p>While studying mechanical engineering is most important, an mechanical engineer should be intelligent, quick-thinking, and have an eye for detail.</p>
     <p>Some devices are very unusual, and require a pet with a mind open to new ideas in order to create.</p>
     <h5>Recommended Equipment</h5>
     <p>Calculators, small hammers, saws, and Soldering Irons are all tools which a mechanical engineer will find helpful.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
