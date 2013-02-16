<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_login = 'no';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; NPC Directory</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; NPC Directory</h4>
     <ul>
      <li><a href="/npcprofile.php?npc=The Scientist">The Scientist</a></li>
      <li><a href="/npcprofile.php?npc=The Archaeologist">The Archaeologist</a></a></li>
      <li><a href="/npcprofile.php?npc=The Receptionist">The Receptionist</a></li>
      <li><a href="/npcprofile.php?npc=The Librarian">The Librarian</a></li>
      <li><a href="/npcprofile.php?npc=The Real Estate Agent">The Real Estate Agent</a></li>
      <li><a href="/npcprofile.php?npc=The Banker">The Banker</a></li>
      <li><a href="/npcprofile.php?npc=The Pet Shelter Girl">The Pet Shelter Girl</a></li>
      <li><a href="/npcprofile.php?npc=The Hippy">The Hippy</a></li>
      <li><a href="/npcprofile.php?npc=The Monk">The Monk</a></li>
      <li><a href="/npcprofile.php?npc=The Pawn Shop Guy">The Pawn Shop Guy</a></li>
      <li><a href="/npcprofile.php?npc=The Smith">The Smith</a></li>
      <li><a href="/npcprofile.php?npc=The Alchemist">The Alchemist</a></li>
      <li><a href="/npcprofile.php?npc=The Totem Fanatic">The Totem Fanatic</a></li>
      <li><a href="/npcprofile.php?npc=The Florist">The Florist</a></li>
      <li><a href="/npcprofile.php?npc=The Advertising Agent">The Advertising Agent</a></li>
      <li><a href="/npcprofile.php?npc=The Auctioneer">The Auctioneer</a></li>
      <li><a href="/npcprofile.php?npc=The Tailor">The Tailor</a></li>
      <li><a href="/npcprofile.php?npc=The+Icecream+Truck">The Icecream Truck</a></li>
     </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
