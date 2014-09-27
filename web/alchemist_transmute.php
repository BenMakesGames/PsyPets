<?php
$wiki = 'The_Alchemist\'s';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; The Alchemist's &gt; Pet Transmutations</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4>The Alchemist's &gt; Pet Transmutations</h4>
     <ul class="tabbed">
      <li><a href="alchemist.php">General Shop</a></li>
      <li><a href="alchemist_potions.php">Potion Shop</a></li>
      <li><a href="af_trinkets.php">Rare Trinkets</a></li>
      <li><a href="alchemist_pool.php">Cursed Pool</a></li>
      <li class="activetab"><a href="alchemist_transmute.php">Pet Transmutations</a></li>
     </ul>
<?php
echo '<a href="npcprofile.php?npc=Thaddeus"><img src="' . $SETTINGS['protocol'] . '://saffron.psypets.net/gfx/npcs/thaddeus.png" align="right" width="350" height="250" alt="(Thaddeus the Alchemist)" /></a>';

include 'commons/dialog_open.php';

echo '
  <p>Not satisfied with your pet\'s appearance?  Hm...</p>
  <p>Well there <em>is</em> something I can do about that... a little magic...</p>
  <p>I can do one of two things for you:</p>
  <ol>
   <li><p>I can turn your pet into some other, common PsyPet.  It\'s relatively cheap and easy to do.</p></li>
   <li><p>I can turn your pet into anything you like... any appearance you can imagine - well, any you can upload, anyway.  This option is a little more costly.</p></li>
  </ol>
';

include 'commons/dialog_close.php';
?>
<ul>
 <li><a href="af_regraphik2.php">Ask to transmute a pet into some other, common PsyPet.</a></li>
 <li><a href="af_custompetgraphic2.php">Ask to transmuate a pet into something completely different!</a></li>
</ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
