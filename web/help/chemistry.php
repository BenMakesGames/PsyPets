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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Hourly Activities &gt; Chemistry</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Hourly Activities &gt; Chemistry</h4>
     <h5>What it Accomplishes</h5>
     <p>Chemists make chemicals, dyes, drugs, and even explosives (including fireworks).</p>
     <p>Chemists often require:</p>
     <ul>
      <li>Plants</li>
      <li>Rare metals</li>
      <li>Chemicals and other compounds</li>
     </ul>
     <p>Typical crafts produced are:</p>
     <ul>
      <li>Chemicals and other compounds</li>
      <li>Drugs and tonics</li>
      <li>Fireworks</li>
      <li>Perfumes</li>
     </ul>
     <h5>What it Requires</h5>
     <p>It is very important for a Chemist to have studied chemistry!  Besides that, an intelligent, quick-thinking pet with an eye for detail will make an excellent Chemist.</p>
     <p>Some chemistry projects are very unusual, and require a pet with a mind open to new ideas in order to create.</p>
     <h5>Recommended Equipment</h5>
     <p>Calculators and computers can always help with the math, but there are also many specialized tools a Chemist can use, from a Bunsen Burner to an Ebullioscope.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
