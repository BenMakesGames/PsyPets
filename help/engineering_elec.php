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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Hourly Activities &gt; Electrical Engineering</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Hourly Activities &gt; Electrical Engineering</h4>
     <h5>What it Accomplishes</h5>
     <p>Electrical Engineers design and build electrical devices, from Calculators to Metal Detectors to super computers.</p>
     <p>They typically require:</p>
     <ul>
      <li>Plastic and rubber</li>
      <li>Metals, gems, and other minerals</li>
     </ul>
     <p>They produce:</p>
     <ul>
      <li>Calculators and computers</li>
      <li>Handheld games and TVs</li>
      <li>Laser weapons</li>
      <li>And other electrical devices</li>
     </ul>
     <h5>What it Requires</h5>
     <p>While studying electrical engineering is most important, an electrical engineer should be intelligent, quick-thinking, and have an eye for detail.</p>
     <p>Some electronics are very unusual, and require a pet with a mind open to new ideas in order to create.</p>
     <h5>Recommended Equipment</h5>
     <p>Calculators, Soldering Irons and Ohmmeters are all tools which an electrical engineer will find helpful.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
