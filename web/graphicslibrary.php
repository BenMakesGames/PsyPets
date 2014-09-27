<?php
// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; Graphics Library</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Graphics Library</h4>
     <p>The Graphics Library contains player-made graphics which are available for use for custom pets, avatars, and even custom items.</p>
     <ul class="spacedlist">
      <li><a href="gl_browse.php">Browse the Graphics Library</a></li>
      <li>
       <a href="gl_upload.php">Upload a graphic</a><br />
       You can even restrict use of your graphic to a single resident, in case you've worked out a deal with that person specifically.<br />
      </li>
      <li><a href="af_custompetgraphic2.php">Pet Graphic Customizer</a></li>
      <li>
       <a href="af_customavataritem2.php">Custom Avatar Item Builder Plus!</a><br />
       This is actually a process which requires use of two graphics: a graphic for your avatar, and a graphic for the item which will give you that avatar upon use (in case you want to temporarily use a different avatar).  It still only costs you one favor :)<br />
      </li>
      <li>
       <a href="af_combinationstation3.php">Combination Station</a><br />
       Take the stats of two items you already have (some limitations may apply :P) and cram them together into a new item with the graphic of your choice.<br />
      </li>
     </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
