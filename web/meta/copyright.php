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
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help</a> &gt; Copyright Information &gt; General Copyright Information</h4>
<ul class="tabbed">
 <li class="activetab"><a href="/meta/copyright.php">General Copyright Information</a></li>
 <li><a href="/meta/copyright_smallgfx.php">Item, Pet and Avatar Graphics</a></li>
 <li><a href="/meta/copyright_mahjong.php">Mahjong Graphics</a></li>
 <li><a href="/meta/copyright_largegfx.php">NPC Graphics</a></li>
 <li><a href="/meta/copyright_code.php">Code Libraries</a></li>
</ul>
     <p>Except where noted otherwise, the PsyPets code, game concept, the name "PsyPets", and all graphics, are the properties of Ben Hendel-Doying, &copy;2004-<?= date('Y') ?>, all rights reserved.</p>
     <p>The <a href="http://www.yahoo.com">Yahoo!</a> (<img src="/gfx/yicon.gif" alt="" width="16" height="16" />), <a href="http://www.aim.com">AIM</a> (<img src="/gfx/aimicon.gif" />), <a href="http://www.skype.com">Skype</a> (<img src="/gfx/skypeicon.png" alt="" />), <a href="http://www.msn.com">MSN</a> (<img src="/gfx/msnicon.png" />), <a href="http://www.facebook.com">Facebook</a> (<img src="/gfx/facebook_icon.png" />), and <a href="http://www.myspace.com">MySpace</a> (<img src="/gfx/myspace_icon.png" />) icons and names are the properties of their respective owners.</p>
     <p>The original PsyPets NPCs (Non-Player Characters) are fictional characters; any similarities to real-life people are coincidental.  (To create the character's names, I typically pull random first and last names from the US Census, and mash them together.)</p>
     <p>If you have other questions, or believe that any of the content here violates copyright law (remember: copyright is automatic!  if someone has posted one of your graphics without your permission, it is a violation of copyright law!), please do not hesitate to contact <a href="/userprofile.php?user=That+Guy+Ben">That Guy Ben</a> in-game, or e-mail <a href="mailto:admin@psypets.net">admin@psypets.net</a>.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
