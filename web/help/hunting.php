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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Hourly Activities &gt; Hunting</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Hourly Activities &gt; Hunting</h4>
     <h5>What it Accomplishes</h5>
     <p>A hunting pet hunts prey animals for food and animal resources.</p>
     <p>You can expect a hunter to bring home:</p>
     <ul>
      <li>Fluff, leather, scales, feathers, bones...</li>
      <li>Meats such as steak, chicken, and fish</li>
      <li>Eggs, milk, and other animal products (including pearls)</li>
     </ul>
     <h5>What it Requires</h5>
     <p>To hunt, a pet must be strong, athletic, perceptive, and/or stealthy, and have wilderness survival training.</p>
     <p>Some prey may have skills that prevent them from being approached unless the hunting pet has some other specific skill.  For example, some prey are very athletic, and will escape capture unless its hunter is athletic enough to keep up.</p>
     <h5>Recommended Equipment</h5>
     <p>Bows, bolas, guns, and spears all make excellent hunting tools.  Many swords and daggers can also be useful.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
