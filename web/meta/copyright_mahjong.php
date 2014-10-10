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
    <title>PsyPets &gt; Help &gt; Copyright Information &gt; Mahjong Graphics</title>
    <?php include "commons/head.php"; ?>
    <style type="text/css">
        .graphiccopy { border-top: 1px solid black; padding: 1em; }
        .graphiccopy img { border: 0; margin: 0; }
    </style>
</head>
<body>
    <?php include 'commons/header_2.php'; ?>
    <h4><a href="/help/">Help</a> &gt; Copyright Information &gt; Mahjong Graphics</h4>
    <ul class="tabbed">
        <li><a href="/meta/copyright.php">General Copyright Information</a></li>
        <li><a href="/meta/copyright_smallgfx.php">Item, Pet and Avatar Graphics</a></li>
        <li class="activetab"><a href="/meta/copyright_mahjong.php">Mahjong Graphics</a></li>
        <li><a href="/meta/copyright_largegfx.php">NPC Graphics</a></li>
        <li><a href="/meta/copyright_code.php">Code Libraries</a></li>
    </ul>
    <p>The following graphics were created by Jerry Crimson Mann, and released for public use under the <a href="http://www.gnu.org/copyleft/fdl.html">GFDL, version 1.2</a>, with no Invariant Sections, no Front-Cover Texts, and no Back-Cover Texts.</p>
    <p>You can find his original graphics (which are larger) on <a href="http://www.wikipedia.org/">Wikipedia</a> articles about Mahjong.</p>
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJd1.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJd2.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJd3.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJf1.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJf2.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJf3.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJf4.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJh1.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJh2.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJh3.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJh4.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJh5.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJh6.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJh7.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJh8.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJs1.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJs2.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJs3.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJs4.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJs5.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJs6.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJs7.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJs8.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJs9.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJt1.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJt2.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJt3.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJt4.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJt5.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJt6.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJt7.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJt8.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJt9.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJw1.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJw2.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJw3.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJw4.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJw5.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJw6.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJw7.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJw8.png" width="27" height="32" alt="" />
    <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/items/mahjong/MJw9.png" width="27" height="32" alt="" />
    <?php include 'commons/footer_2.php'; ?>
</body>
</html>
