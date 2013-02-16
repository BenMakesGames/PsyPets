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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Hourly Activities &gt; Jeweling</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Hourly Activities &gt; Jeweling</h4>
     <h5>What it Accomplishes</h5>
     <p>Jewelers fashion jewelry from precious metals and gems.  Jewelry is often used by <a href="/help/binding.php">Magic-binders</a> to create powerful artifacts, but even "mundane" jewelry is not without value!</p>
     <p>Jewelers require:</p>
     <ul>
      <li>Gems and precious metals</li>
     </ul>
     <p>Typical crafts produced are:</p>
     <ul>
      <li>Earrings</li>
      <li>Necklaces, charms, and amulets</li>
      <li>Crowns</li>
      <li>Bracelets</li>
     </ul>
     <h5>What it Requires</h5>
     <p>High-quality jewelry is made by a pet with a keen eye, intelligence, and dexterity, who has studied jeweling.</p>
     <p>Some crafts are very unusual, and require a pet with a mind open to new ideas in order to create.</p>
     <h5>Recommended Equipment</h5>
     <p><!-- ??? --></p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
