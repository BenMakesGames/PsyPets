<?php
require_once 'commons/init.php';

$require_login = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/formatting.php";
require_once 'commons/bannedurls.php';

include 'commons/html.php';
?>
 <head>
  <title>City Hall &gt; Help Desk &gt; Design Philosophies</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <h4><a href="/cityhall.php">City Hall</a> &gt; <a href="/help/">Help Desk</a> &gt; Design Philosophies</h4>
  <p>If a feature is to be added, it should be compared against these philosophies.  If a feature does not fit within these philosophies, it should not be added.</p>
  <p>If a feature is to be removed (or changed), the same question should be asked: will <em>removing</em> this feature serve these philosophies?</p>
  <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/help/pillar_pets_first.png" width="75" height="150" alt="" align="right" />
  <h5>The Pets Are The Primary Agents</h5>
  <p>In some games, the player takes on the role of a hero that grows in power as the game progresses, and it's alright for PsyPets to have player-advancement as well, but the <em>main</em> focus is on pet-advancement: you can't build supercomputers, your pets can; you can't defeat the mighty Kundrav - a dragon from Persian mythology - your pets can.</p>
  <p>As the player, you provide the pets a good environment and home, giving them the freedom to do these things.</p>
  <p>Experiences created:</p>
  <ul>
   <li>Gives the player a care-taker/parenting role</li>
   <li>The player experiences success vicariously - when the pets succeed, the player succeeds</li>
  </ul>
  <h5>The Pets Are Alive</h5>
  <p>Well, not really, of course, but they should <em>seem</em> alive - as alive as possible!  (From a super-philosophical point of view, you might even argue that seeming alive is as good as anything gets, anyway, so there you go :P)</p>
  <p>I try to look to theories in psychology, and apply those to PsyPets.  The theory that got PsyPets started was Maslow's Heirarchy of Needs, but we're not limited to that, or even to merely theories in psychology.  If some day we find a cool way to simulate physics to make pets seem more alive, awesome.</p>
  <p>Experiences created:</p>
  <ul>
   <li>Allows players to sympathize and relate to pets (enhances the care-taking role, and vicarious success from pets)</li>
   <li>Creates a sense of responsibility for the pets' well-being</li>
  </ul>
  <h5>The Players Are Part of a Small Community</h5>
  <p>Whether there's a thousand or there's hundreds of thousands of players, players should feel like they're part of a small community.</p>
  <p>Experiences created:</p>
  <ul>
   <li>Allows players to sympathize with other players; to help others succeed, and be happy for the success of others</li>
   <li>The players' successes - and the successes of their pets - feels more important</li>
  </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
