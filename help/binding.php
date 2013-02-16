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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Hourly Activities &gt; Magic-binding</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Hourly Activities &gt; Magic-binding</h4>
     <h5>What it Accomplishes</h5>
     <p>Magic-binding weaves magic into ordinary items to create powerful artifacts.</p>
     <p>Magic binders use:</p>
     <ul>
      <li>Gossamer and Dark Gossamer</li>
      <li>Pyrestone</li>
      <li>Jewelry, clothes, tools, weapons, and other equipment</li>
     </ul>
     <p>They produce:</p>
     <ul>
      <li>Magic cloaks, rings and amulets</li>
      <li>Enchanted tools and weapons</li>
      <li>Other strange things</li>
     </ul>
     <h5>What it Requires</h5>
     <p>Besides studying magic-binding, magic-binders should be intelligent, physically tough, and quick-witted.</p>
     <p>Some magic-binding projects are very unusual (even among magic-bound items!), and require a pet with a mind open to new ideas in order to create.</p>
     <h5>Recommended Equipment</h5>
     <p>Wands, staffs, amulets and masks commonly help a magic-binder focus his or her art, but magic can be focused through other odd things as well.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
