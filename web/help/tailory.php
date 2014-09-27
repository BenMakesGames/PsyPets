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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Hourly Activities &gt; Tailory</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Hourly Activities &gt; Tailory</h4>
     <h5>What it Accomplishes</h5>
     <p>A tailor sews to create hats, shirts, shoes, and other articles of clothing.</p>
     <p>Tailors need access to:</p>
     <ul>
      <li>Fluff and cloth</li>
     </ul>
     <p>They produce:</p>
     <ul>
      <li>Hats and capes</li>
      <li>Plushies and pillows</li>
      <li>Cloth</li>
     </ul>
     <h5>What it Requires</h5>
     <p>Tailors must have steady hands, an eye for detail, intelligence, and know basic sewing methods and patterns.</p>
     <p>Some tailory projects are very unusual, and require a pet with a mind open to new ideas in order to create.</p>
     <h5>Recommended Equipment</h5>
     <p>Thimbles, needles, and sewing machines are the best tools for a tailor.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
