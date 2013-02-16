<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

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
  <title><?= $SETTINGS['site_name'] ?> &gt; Help &gt; Copyright Information</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help</a> &gt; Copyright Information &gt; General Copyright Information</h4>
<ul class="tabbed">
 <li class="activetab"><a href="/meta/copyright.php">General Copyright Information</a></li>
 <li><a href="/meta/copyright_smallgfx.php">Item, Pet and Avatar Graphics</a></li>
 <li><a href="/meta/copyright_largegfx.php">NPC Graphics</a></li>
 <li><a href="/meta/copyright_code.php">Code Libraries</a></li>
</ul>
<p><?= $SETTINGS['site_name'] ?>, by <?= $SETTINGS['author_real_name'] ?>, is based on PsyPets, copyright 2004-2013 <a href="http://www.telkoth.net/">Ben Hendel-Doying</a>, which is licensed under the <a href="/psypets.license.txt">MIT License</a>.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
