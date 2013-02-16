<?php
$whereat = 'pawnshop';
$wiki = 'Pawn_Shop';
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
  <title><?= $SETTINGS['site_name'] ?> &gt; Pawn Shop</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4>Pawn Shop</h4>
<?php
echo '<a href="/npcprofile.php?npc=Tony+Cables"><img src="gfx/npcs/tony.png" align="right" width="350" height="305" alt="(Tony "Shady" Cables)" /></a>';
?>
     <p>"You don't seem to have any inventory in your storage.  Come back when you have something to trade."</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
