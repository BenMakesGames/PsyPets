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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Hourly Activities &gt; Smithing</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Hourly Activities &gt; Smithing</h4>
     <h5>What it Accomplishes</h5>
     <p>Smithing pets take basic materials in your home - usually metals - and turn them into tools, weapons, and armor.</p>
     <p>Smiths commonly require the following materials:</p>
     <ul>
      <li>Metals</li>
      <li>Wood</li>
     </ul>
     <p>They produce:</p>
     <ul>
      <li>Swords, daggers</li>
      <li>Axes, hammers</li>
      <li>Other tools and weapons</li>
      <li>Armor and helmets</li>
     </ul>
     <h5>What it Requires</h5>
     <p>Smiths must be strong and have great endurance, but also be intelligent.  They should also have studied metal-working.</p>
     <p>Some smithing projects are very unusual, and require a pet with a mind open to new ideas in order to create.</p>
     <h5>Recommended Equipment</h5>
     <p>Hammers are <em>the</em> number-one tool for any smith.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
