<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_login = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/formatting.php";

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; House Space</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; House Space</h4>
     <p>Your house has limited capacity, and the items and pets in your house take up space.</p>
     <p>How much space your house has, and how much space is being taken up, are shown when visiting your house.  For example:</p>
     <p style="padding-left:2em;"><b>Common Room, Someone's House (400 / 500; 80% full)</b></p>
     <p>This house has 500 maximum space, and 400 is taken up (80%).</p>
     <p>You cannot <a href="/help/hours.php">run hours</a> if your house is over capacity.</p>
     <h5>Pet Space</h5>
     <p>In addition to physically taking up some space, pets need room to move around in.</p>
     <p>You cannot <a href="/help/hours.php">run hours</a> if you have more pets than your house can comfortably house.</p>
     <p>This amount is shown at home, just above your pets, for example:</p>
     <p style="padding-left:2em;"><b>Pets (2 / 6)</b></p>
     <p>... indicates a house with 2 pets out of a maximum of 6.</p>
     <p>As you increase your house's maximum space, the maximum allowed pets will also increase.</p>
     <p>You can also increase your maximum number of pets by acquiring a Breeder's License.</p>
     <h5 class="obstacle">"I Have Too Many Pets!  What Can I Do?"</h5>
     <p>Consider placing some pets into <a href="/daycare.php">Daycare</a>.  (Daycare is free to use!)  Once you've increased your allowed pet maximum, you can retrieve the pets.</p>
     <p>You can also <a href="/giveuppet.php">give pets to the Pet Shelter</a>, for other residents to buy.  If you don't think you'll ever retrieve a pet from Daycare, please consider giving someone else the opportunity to care for it!</p>
     <h5>Increasing House Space</h5>
     <p>There are three ways to increase your house's size:</p>
     <ul>
      <li><p>You can buy additional house space from Real Estate (in the Services menu).  Players with small houses receive special offers.</p></li>
      <li><p>You can increase your house by using Walls in conjunction with Roofs, Windows, Doors, Piping, and other items.</p></li>
      <li><p>You can buy house space from other players, in the form of Deeds.  For example, a Deed to 100 Units, when used, adds 100 to your house's size.</p></li>
     </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
