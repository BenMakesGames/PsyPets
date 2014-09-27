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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; Pet Care</h4>
     <h5>Basic Needs</h5>
     <ul>
      <li><a href="/help/energy.php">Energy</a></li>
      <li><a href="/help/food.php">Food</a></li>
      <li><a href="/help/safety.php">Safety</a></li>
      <li><a href="/help/love.php">Love</a></li>
      <li><a href="/help/esteem.php">Esteem</a></li>
     </ul>
     <h5>Hourly Activities</h5>
     <h6>Collection</h6>
     <ul>
      <li><a href="/help/adventuring.php">Adventuring</a></li>
      <li><a href="/help/fishing.php">Fishing</a></li>
      <li><a href="/help/gather.php">Gathering</a></li>
      <li><a href="/help/hunting.php">Hunting</a></li>
      <li><a href="/help/lumberjacking.php">Lumberjacking</a></li>
      <li><a href="/help/mining.php">Mining</a></li>
      <li><a href="/help/vhagst.php">Virtual Hide-and-Go-Seek Tag</a></li>
     </ul>
     <h6>Production</h6>
     <ul>
      <li><a href="/help/carpentry.php">Carpentry</a></li>
      <li><a href="/help/chemistry.php">Chemistry</a></li>
      <li><a href="/help/engineering_elec.php">Electrical Engineering</a></li>
      <li><a href="/help/garden.php">Gardening and Farming</a></li>
      <li><a href="/help/crafts.php">Handicrafts</a></li>
      <li><a href="/help/jeweling.php">Jeweling</a></li>
      <li><a href="/help/leatherworking.php">Leather-working</a></li>
      <li><a href="/help/binding.php">Magic-binding</a></li>
      <li><a href="/help/engineering_mech.php">Mechanical Engineering</a></li>
      <li><a href="/help/painting.php">Painting</a></li>
      <li><a href="/help/sculpture.php">Sculpting</a></li>
      <li><a href="/help/smithing.php">Smithing</a></li>
      <li><a href="/help/tailory.php">Tailory</a></li>
     </ul>
     <h6>Miscellaneous</h6>
     <ul>
      <li><a href="/help/begging.php">Begging</a></li>
      <li><a href="/help/eating.php">Eating</a></li>
      <li><a href="/help/sleeping.php">Sleeping</a></li>
     </ul>
     <h5>Park Events</h5>
     <ul>
      <li><a href="/help/archery.php">Archery Competition</a></li>
      <li><a href="/help/arts_and_crafts.php">Arts &amp; Crafts Competition</a></li>
      <li><a href="/help/brawl.php">Brawl</a></li>
      <li><a href="/help/ctf.php">Capture the Flag</a></li>
      <li><a href="/help/cookoff.php">Cook-Off</a></li>
      <li><a href="/help/dancemania.php">Dance Mania Competition</a></li>
      <li><a href="/help/race.php">Distance Race</a></li>
      <li><a href="/help/fashionshow.php">Fashion Show</a></li>
      <li><a href="/help/fishingcompetition.php">Fishing</a></li>
      <li><a href="/help/longjump.php">Long Jump</a></li>
      <li><a href="/help/roborena.php">Roborena</a></li>
      <li><a href="/help/scavengerhunt.php">Scavenger Hunt</a></li>
      <li><a href="/help/swimming.php">Swimming Race</a></li>
      <li><a href="/help/strategy.php">Strategy Games</a></li>
      <li><a href="/help/tug_of_war.php">Tug of War</a></li>
     </ul>
     <h5>Other Topics</h5>
     <ul>
      <li><a href="/help/pregnancy.php">Pregnancy</a></li>
      <li><a href="/help/levelup.php">Self-Actualization</a></li>
      <li><a href="/help/equipment.php">Tools and Keys</a></li>
      <li><a href="/help/wounds.php">Wounds</a></li>
     </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
