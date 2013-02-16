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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Hourly Activities &gt; Sculpting</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Hourly Activities &gt; Sculpting</h4>
     <h5>What it Accomplishes</h5>
     <p>Sculptors turn clay and rock into works of art.</p>
     <p>Besides clay and rock, Sculptors might require:</p>
     <ul>
      <li>Metals</li>
      <li>Plastic</li>
      <li>Wax</li>
      <li>Logs and Wood</li>
     </ul>
     <p>Typical crafts produced are:</p>
     <ul>
      <li>Vases</li>
      <li>Statues</li>
      <li>Figurines</li>
      <li>Masks</li>
      <li>Totems</li>
     </ul>
     <h5>What it Requires</h5>
     <p>Besides studying sculpture, sculptors should have an eye for detail, be dextrous, and be intelligent.</p>
     <p>Some sculpting projects are very unusual, and require a pet with a mind open to new ideas in order to create.</p>
     <h5>Recommended Equipment</h5>
     <p>Chisels, knives and hammers can all be useful in the hands of a sculptor.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
