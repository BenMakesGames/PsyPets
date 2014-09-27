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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Hourly Activities &gt; Inventing</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Hourly Activities &gt; Inventing</h4>
     <h5>What it Accomplishes</h5>
     <p>Inventors take basic materials in your home and turn them into electronic and/or novel tools.</p>
     <p>Inventors require materials, most commonly:</p>
     <ul>
      <li>Plastic and rubber</li>
      <li>Metals, gems, and other minerals</li>
     </ul>
     <p>They produce:</p>
     <ul>
      <li>Calculators and computers</li>
      <li>Handheld games</li>
      <li>Household tools and TVs</li>
      <li>Fireworks and other explosives</li>
      <li>Laser weapons</li>
     </ul>
     <h5>What it Requires</h5>
     <p>To invent, a pet must be intelligent, have an eye for detail, and understand principles of engineering.</p>
     <p>Some inventions are very unusual, and require a pet with a mind open to new ideas in order to create.</p>
     <h5>Recommended Equipment</h5>
     <p>Calculators and measuring tools are best for inventors, but many magic jewelries, wreaths, and wands also enhance the mental skills needed by inventive pets.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
