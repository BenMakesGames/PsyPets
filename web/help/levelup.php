<?php
require_once 'commons/init.php';

$require_login = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/formatting.php";

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Self-Actualization</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Self-Actualization</h4>
     <p>When a pet has gained enough experience using a particular skill, that skill will increase.  However, a pet will not gain experience if any of its needs - energy, food, safety, love, or esteem - are not being met.</p>
     <p>Pets primarily train skills by using them, for example a pet that participates in a Capture the Flag event will train Stealth and Speed among other skills.  You can also train a pet's skills by reading it certain books, or playing with it using certain toys.  For example, a pet that plays with a Fishbowl will train its Fishing skill!</p>
     <p>Finally, when a pet's affection for you has reached a certain level, you will have the opportunity to either learn something about your pet, or direct its development.  At this time, you may directly increase its skills, however you may only increase a skill which you are very familiar with yourself - if you've never even trained a pet to Fish <em>using</em> a Fishbowl, don't expect to be able to do it <em>without</em> one!</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
