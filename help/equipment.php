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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Equipment and Keys</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Tools and Keys</h4>
     <p>Pets can equip two kinds of items: Tools, and Keys.  A pet can be equipped with up to one of each.</p>
     <h5>Tools</h5>
     <p>Tools include swords, shields, capes, hats, nailguns and crowbars, to name only a few.</p>
     <p>Tools provide pets with bonuses when equipped.  These can be bonuses to specific tasks, such as gardening or hunting, or to basic statistics, such as strength or conscientiousness.</p>
     <p>Tools typically have a durability rating; as a tool is used, its condition will worsen, until it finally breaks.  You can prevent items from breaking by repairing them with <?= item_text_link('Duct Tape') ?>, however for very cheap tools it's often more economical to simply replace them when broken.</p>
     <h5>Keys</h5>
     <p>Keys allow pets to access special locations, monsters, or prey during their hourly activities.  For example, the legendary monster Kundrav cannot be approached by a pet unless that pet is holding the <?= item_text_link('Key to Kundrav\'s Lair') ?>.</p>
     <p>When a pet accesses the location, monster, or prey allowed by a key, the key is consumed.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
