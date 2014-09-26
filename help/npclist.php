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
  <title><?= $SETTINGS['site_name'] ?> &gt; PsyPets Staff</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help Desk</a> &gt; PsyPets Staff</h4>
     <h5>HERG Members</h5>
     <ul>
      <li><a href="/npcprofile.php?npc=Eve+Heidel">Eve Heidel</a>, HERG Director</a></li>
      <li><a href="/npcprofile.php?npc=Julio+Beiler">Julio Beiler</a>, Hollow Earth Archaeological and Anthropological Research Lead</a></li>
      <li><a href="/npcprofile.php?npc=Claire+Silloway">Claire Silloway</a>, Community Relations Manager</a></li>
      <li><a href="/npcprofile.php?npc=Marian+Witford">Marian Witford</a>, Archivist</li>
      <li><a href="/npcprofile.php?npc=Amanda+Branaman">Amanda Branaman</a>, Real Estate Agent</li>
     </ul>
     <h5>Community Services</h5>
     <ul>
      <li><a href="/npcprofile.php?npc=Lakisha+Pawlak">Lakisha Pawlak</a>, Bank Clerk</li>
      <li><a href="/npcprofile.php?npc=Kim+Littrell">Kim Littrell</a> of the Pet Shelter</li>
      <li><a href="/npcprofile.php?npc=Ian+Hobbs">Ian Hobbs</a>, Head of Waste Management</li>
      <li><a href="/npcprofile.php?npc=Lance+Sussman">Lance Sussman</a>, no official title</li>
     </ul>
     <h5>Independent Entrepreneurs</h5>
     <ul>
      <li><a href="/npcprofile.php?npc=Tony+Cables">Tony "Shady" Cables</a> of The Pawn Shop</li>
      <li><a href="/npcprofile.php?npc=Nina+Faber">Nina Faber</a> of The Smithery</li>
      <li><a href="/npcprofile.php?npc=Thaddeus">Thaddeus</a> of The Alchemist's</li>
      <li><a href="/npcprofile.php?npc=Matalie+Mansur">Matalie Mansur</a> of The Totem Pole Garden</li>
      <li><a href="/npcprofile.php?npc=Vanessa+Roselle">Vanessa Roselle</a> of The Florist</li>
      <li><a href="/npcprofile.php?npc=Maya+Wirt">Maya Wirt</a> of Advertising</li>
      <li><a href="/npcprofile.php?npc=Richard+Silloway">Richard Silloway</a> of the Auction House</li>
      <li><a href="/npcprofile.php?npc=Valerie+H.">Valerie H.</a> of The Tailory</li>
      <li><a href="/npcprofile.php?npc=The+Icecream+Truck">The Icecream Truck</a></li>
     </ul>
     <h5>Non-staff Characters</h5>
     <ul>
      <li><a href="/npcprofile.php?npc=Rickman+T.+Aberystwyth">Rickman T. Aberystwyth</a></li>
      <li><a href="/npcprofile.php?npc=Sharri+Sarkisian">Sharri Sarkisian</a></li>
      <li><a href="/npcprofile.php?npc=Dewey+Shireman">Dewey Shireman</a></li>
     </ul>
     <ul>
      <li><a href="/npcprofile.php?npc=Kaera+Ki+Ri+Kashu">Kaera Ki Ri Kashu</a></li>
      <li><a href="/npcprofile.php?npc=Gizubi+of+the+Golden+Sun">Gizubi of the Golden Sun</a></li>
      <li><a href="/npcprofile.php?npc=Rizi+Vizi+the+Devourer">Rizi Vizi the Devourer</a></li>
     </ul>
     <ul>
      <li><a href="/npcprofile.php?npc=Fireplace+Fairies">Fireplace Fairies</a></li>
      <li><a href="/npcprofile.php?npc=Wishing+Well+Fairy">Wishing Well Fairy</a></li>
      <li><a href="/npcprofile.php?npc=Lady+June">Lady June</a></li>
      <li><a href="/npcprofile.php?npc=The+Lady+of+the+Lake">The Lady of the Lake</a></li>
      <li><a href="/npcprofile.php?npc=Lancelot">Lancelot</a></li>
     </ul>
     <ul>
      <li><a href="/npcprofile.php?npc=Leonardo+da+Vinci">Leonardo da Vinci</a></li>
      <li><a href="/npcprofile.php?npc=Amelia+Earhart">Amelia Earhart</a></li>
      <li><a href="/npcprofile.php?npc=Agents+Mully+and+Sculder">Agents Mully and Sculder</a></li>
     </ul>
     <h5>Business Accounts</h5>
     <ul>
      <li><a href="/npcprofile.php?npc=myPlushy">myPlushy</a></li>
      <li><a href="/npcprofile.php?npc=Sphere+Quest">Sphere Quest</a></li>
      <li><a href="/npcprofile.php?npc=CubeSys+Inc.">CubeSys Inc.</a></li>
      <li><a href="/npcprofile.php?npc=Teeny+Tiny+Games+Corp.">Teeny Tiny Games Corp.</a></li>
     </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
