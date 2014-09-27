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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Energy</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Energy</h4>
     <p>When a pet is awake, its Energy decreases by 1 per hour.  When it gets to 0 or below, the pet becomes exhausted, and the increase of Safety, Love, Esteem, and experience points becomes impossible.</p>
     <h5>Sleeping</h5>
     <p>Before it gets that bad, however, a pet will usually go to sleep itself.  While a pet is sleeping, its Energy increases by 1 per hour, its Food decreases more slowly than usual, and its Safety, Love, and Esteem do not drop at all.</p>
     <p>A pet will not wake up until its Energy is above 0, and usually sleeps until its Energy is completely refilled, or at least close to it.</p>
     <p>You can attempt to put a pet to sleep as a half-hourly action, however the more Energy a pet has, the less likely this action is to succeed.</p>
     <p>You can attempt to wake a pet up as a half-hourly action.  This succeeds if the pet has more than 0 energy hours (i.e. its energy need is met, even if only barely).</p>
     <h5>Caffeine</h5>
     <p>A caffeinated pet almost entirely ignores its Energy: the pet will not go to sleep, and even if its Energy drops below 0, it will not become exhausted.</p>
     <p>Its Energy value, however, is still counting down!  If the pet runs out of Caffeine and its energy is below 0, it will become exhausted.</p>
     <p>If a pet's Energy gets too low while caffeinated, the pet will fall unconscious (sleep).</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
