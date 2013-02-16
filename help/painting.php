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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Hourly Activities &gt; Painting</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Hourly Activities &gt; Painting</h4>
     <h5>What it Accomplishes</h5>
     <p>Painters use dyes to create works of art.</p>
     <p>Such a pet will require materials, including:</p>
     <ul>
      <li>Dyes</li>
      <li>Chalk</li>
      <li>Paper</li>
     </ul>
     <p>Typical crafts produced are:</p>
     <ul>
      <li>Paintings</li>
      <li>Drawings</li>
     </ul>
     <h5>What it Requires</h5>
     <p>To produce quality paintings, a pet must have an eye for detail, be intelligent and dextrous, and have studied painting.</p>
     <p>Some crafts are very unusual, and require a pet with a mind open to new ideas in order to create.</p>
     <h5>Recommended Equipment</h5>
     <p>Paintbrushes and easels make good equipment for a painter.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
