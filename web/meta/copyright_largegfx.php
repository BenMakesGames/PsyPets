<?php
require_once 'commons/init.php';

$require_login = 'no';
$require_petload = 'no';
$reading_tos = true;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; Help &gt; Copyright Information</title>
<?php include "commons/head.php"; ?>
  <style type="text/css">
   .graphiccopy { border-top: 1px solid black; padding: 1em; }
   .graphiccopy img { border: 0; margin: 0; }
  </style>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help</a> &gt; Copyright Information &gt; NPC and Event Graphics</h4>
<ul class="tabbed">
 <li><a href="/meta/copyright.php">General Copyright Information</a></li>
 <li><a href="/meta/copyright_smallgfx.php">Item, Pet and Avatar Graphics</a></li>
 <li><a href="/meta/copyright_mahjong.php">Mahjong Graphics</a></li>
 <li class="activetab"><a href="/meta/copyright_largegfx.php">NPC Graphics</a></li>
 <li><a href="/meta/copyright_code.php">Code Libraries</a></li>
</ul>
     <p>The graphics listed here are used with permission from the artist.</p>
     <div class="graphiccopy">
      <p><img src="//saffron.psypets.net/gfx/npcs/smithy2.png" /> <img src="//saffron.psypets.net/gfx/npcs/concept/smithy.png" /></p>
      <p>&copy; Ben Hendel-Doying &amp; "Sepia"</p>
     </div>
     <div class="graphiccopy">
      <p><img src="//saffron.psypets.net/gfx/npcs/banker_lakisha.png" /> <img src="//saffron.psypets.net/gfx/npcs/concept/banker_lakisha.png" /></p>
      <p>&copy; Ben Hendel-Doying &amp; "Sepia"</p>
     </div>
     <div class="graphiccopy">
      <p><img src="//saffron.psypets.net/gfx/npcs/thaddeus.png" /> <img src="//saffron.psypets.net/gfx/npcs/concept/alchybw.png" /></p>
      <p>&copy; Ben Hendel-Doying &amp; "Sepia"</p>
     </div>
     <div class="graphiccopy">
      <p><img src="//saffron.psypets.net/gfx/npcs/receptionist.png" /> <img src="//saffron.psypets.net/gfx/npcs/concept/receptionist.png" /></p>
      <p>&copy; Ben Hendel-Doying &amp; Amanda Wiebe</p>
     </div>
     <div class="graphiccopy">
      <p><img src="//saffron.psypets.net/gfx/npcs/eve_heidel.png" /> <img src="//saffron.psypets.net/gfx/npcs/concept/eve.jpg" /></p>
      <p>&copy; Ben Hendel-Doying &amp; Amanda Wiebe</p>
     </div>
     <div class="graphiccopy">
      <p><img src="//saffron.psypets.net/gfx/npcs/totemgirl.jpg" /> <img src="//saffron.psypets.net/gfx/npcs/concept/natalie.png" /></p>
      <p>&copy; Ben Hendel-Doying &amp; "Shayra"</p>
     </div>
     <div class="graphiccopy">
      <p><img src="//saffron.psypets.net/gfx/npcs/hippy.png" /> <img src="//saffron.psypets.net/gfx/npcs/concept/hippie-3-1.png" /></p>
      <p>&copy; Ben Hendel-Doying &amp; Amanda Wiebe</p>
     </div>
     <div class="graphiccopy">
      <p><img src="//saffron.psypets.net/gfx/npcs/monk.png" /> <img src="//saffron.psypets.net/gfx/npcs/concept/monk-1.png" /></p>
      <p>&copy; Ben Hendel-Doying &amp; "FaileV"</p>
     </div>
     <div class="graphiccopy">
      <p><img src="//saffron.psypets.net/gfx/monsters/fallenangel.png" /></p>
      <p>&copy; Aileen MacKay &amp; Ben Hendel-Doying</p>
     </div>
     <div class="graphiccopy">
      <p><img src="//saffron.psypets.net/gfx/npcs/flowergirl.jpg" /> <img src="//saffron.psypets.net/gfx/npcs/concept/florist-2.jpg" /></p>
      <p>&copy; Ben Hendel-Doying &amp; Megan "Nanashi-chan" Ballenger</p>
     </div>
     <div class="graphiccopy">
      <p><img src="//www.psypets.net/gfx/iciclelights.gif" /></p>
      <p><img src="//www.psypets.net/gfx/iciclelightscolored.gif" /></p>
      <p><img src="//www.psypets.net/gfx/blinkylights.gif" /></p>
      <p>&copy; Justin "Hara" Doak; blinking version by Megan "Nanashi-chan" Ballenger</p>
     </div>
     <div class="graphiccopy">
      <p><img src="//saffron.psypets.net/gfx/npcs/fairy.png" /></p>
      <p>&copy; Aileen MacKay &amp; Ben Hendel-Doying</p>
     </div>
     <div class="graphiccopy">
      <p><img src="//saffron.psypets.net/gfx/npcs/petsheltergirl-2.png" /> <img src="//saffron.psypets.net/gfx/npcs/concept/petsheltergirl.jpg" /></p>
      <p>&copy; Ben Hendel-Doying &amp; "pyxis_lynx"</p>
     </div>
     <div class="graphiccopy">
      <p><img src="//saffron.psypets.net/gfx/npcs/advertising.png" /> <img src="//saffron.psypets.net/gfx/npcs/concept/advertising-girl-v2.png" /></p>
      <p>&copy; Ben Hendel-Doying &amp; "pyxis_lynx"</p>
     </div>
     <div class="graphiccopy">
      <p><img src="//saffron.psypets.net/gfx/npcs/octo.png" width="150" height="200" /></p>
      <p>&copy; "Repression"</p>
     </div>
     <div class="graphiccopy">
      <p><img src="//saffron.psypets.net/gfx/npcs/icecreamtruck.png" width="300" height="200" /></p>
      <p>&copy; Aileen MacKay &amp; Ben Hendel-Doying</p>
     </div>
     <div class="graphiccopy">
      <p><img src="//saffron.psypets.net/gfx/backdrops/gypsygarden.png" width="609" height="321" /></p>
      <p>Gypsy Garden &copy; Jen Furlotte (<a href="http://www.pixelsandicecream.com/">www.pixelsandicecream.com</a>)</p>
     </div>
     <div class="graphiccopy">
      <p><img src="//saffron.psypets.net/gfx/npcs/museum.png" /></p>
      <p>Museum Curator &copy; Bethany MacKay (<a href="http://www.bethanymackay.com/">www.bethanymackay.com</a>)</p>
     </div>
     <div class="graphiccopy">
      <p><img src="//saffron.psypets.net/gfx/npcs/tony.png" /></p>
      <p>Tony &copy; Bethany MacKay (<a href="http://www.bethanymackay.com/">www.bethanymackay.com</a>)</p>
     </div>
     <div class="graphiccopy">
      <p><img src="//saffron.psypets.net/gfx/npcs/volcanospirit.png" /></p>
      <p>Volcano Spirit &copy; Bethany MacKay (<a href="http://www.bethanymackay.com/">www.bethanymackay.com</a>)</p>
     </div>
     <div class="graphiccopy">
      <p><img src="//saffron.psypets.net/gfx/npcs/adventurer.png" /></p>
      <p>Adventurer &copy; Bethany MacKay (<a href="http://www.bethanymackay.com/">www.bethanymackay.com</a>)</p>
     </div>
     <div class="graphiccopy">
      <p><img src="//saffron.psypets.net/gfx/npcs/fbi.png" /></p>
      <p>FBI Agents Sculder and Mully &copy; Bethany MacKay (<a href="http://www.bethanymackay.com/">www.bethanymackay.com</a>)</p>
     </div>
     <div class="graphiccopy">
      <p><img src="//saffron.psypets.net/gfx/npcs/marian-the-librarian.png" /></p>
      <p>Marian the Librarian &copy; Bethany MacKay (<a href="http://www.bethanymackay.com/">www.bethanymackay.com</a>)</p>
     </div>
     <div class="graphiccopy">
      <p><img src="//saffron.psypets.net/gfx/npcs/auctioneer2.png" /></p>
      <p>Auctioneer &copy; Bethany MacKay (<a href="http://www.bethanymackay.com/">www.bethanymackay.com</a>)</p>
     </div>
     <div class="graphiccopy">
      <p><img src="//saffron.psypets.net/gfx/npcs/aeronautical.png" /></p>
      <p>&copy; Bethany MacKay (<a href="http://www.bethanymackay.com/">www.bethanymackay.com</a>)</p>
     </div>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
