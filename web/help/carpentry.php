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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Hourly Activities &gt; Carpentry</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Hourly Activities &gt; Carpentry</h4>
     <h5>What it Accomplishes</h5>
     <p>Carpenter pets can turn wood into tools, toys, and works of art.  They're also good at expanding your house.</p>
     <p>Besides wood, carpenters sometimes need other materials, such as:</p>
     <ul>
      <li>Metal, gems, and other minerals</li>
      <li>Clay</li>
     </ul>
     <p>Typical crafts produced are:</p>
     <ul>
      <li>Wands and staffs</li>
      <li>Furniture</li>
      <li>Toys and games</li>
      <li>House extensions</li>
     </ul>
     <h5>What it Requires</h5>
     <p>Besides studying carpentry, carpenters should strive to be nimble, strong, and perceptive.</p>
     <p>Some carpentry projects are very unusual, and require a pet with a mind open to new ideas in order to create.</p>
     <h5>Recommended Equipment</h5>
     <p>Hammers, saws, and other common tools.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
