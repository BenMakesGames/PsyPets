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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Hourly Activities &gt; Adventuring</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Hourly Activities &gt; Adventuring</h4>
     <h5>What it Accomplishes</h5>
     <p>A pet that adventures has decided to rid the world of evil monsters.  In so doing, the pet may obtain loot dropped by monster and/or bounties that have been placed on those monsters.</p>
     <p>You can expect an adventuring pet to bring home the following:</p>
     <ul>
      <li>Pure moneys, added directly to your funds</li>
      <li>Gems and precious metals</li>
      <li>Books and scrolls</li>
      <li>Hats, swords, and other equipment</li>
      <li>Some animal materials, such as blood, leather, and bones</li>
      <li>Miscellaneous treasures, including chess pieces and plushies</li>
     </ul>
     <h5>What it Requires</h5>
     <p>To adventure, a pet should be strong, tough, and athletic, but above all, it must be trained to fight.</p>
     <p>Some monsters may have skills that prevent them from being approached unless the adventuring pet has some other specific skill.  For example, some monsters are evasive, and must be snuck up on.</p>
     <h5>Recommended Equipment</h5>
     <p>Weapons, armors, shields, and helmets make good equipment for pets looking to adventure.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
