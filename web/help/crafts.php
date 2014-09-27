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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Hourly Activities &gt; Handirafts</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Hourly Activities &gt; Handicrafts</h4>
     <h5>What it Accomplishes</h5>
     <p>Handipets will take basic materials in your home and turn them into toys, tools, and other household objects.</p>
     <p>Such a pet will require materials, including:</p>
     <ul>
      <li>Dyes</li>
      <li>Fluff</li>
      <li>Plants</li>
      <li>Wood and paper</li>
      <li>Wax</li>
     </ul>
     <p>Typical crafts produced are:</p>
     <ul>
      <li>Toys and games</li>
      <li>Small tools</li>
      <li>Soap</li>
      <li>Candles</li>
     </ul>
     <h5>What it Requires</h5>
     <p>To produce quality crafts, a pet must have an eye for detail, be intelligent and dexterous, and have studied crafting.</p>
     <p>Some crafts are very unusual, and require a pet with a mind open to new ideas in order to create.</p>
     <h5>Recommended Equipment</h5>
     <p>Scissors, small hammers, knives, and Measuring Tape are obvious picks for a handipet.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
