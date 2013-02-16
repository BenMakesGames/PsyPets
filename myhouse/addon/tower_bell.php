<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$whereat = "home";
$wiki = "Tower#Balcony";
$require_petload = 'no';

$THIS_ROOM = 'Tower';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/towerlib.php';

if(!addon_exists($house, 'Tower'))
{
  header('Location: /myhouse.php');
  exit();
}

$tower = get_tower_byuser($user['idnum']);

if($tower['bell'] != 'yes')
{
  header('Location: /myhouse/addon/tower.php');
  exit();
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Tower &gt; Bell Tower</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Tower &gt; Bell Tower</h4>
<?php
room_display($house);
?>
<ul class="tabbed">
 <li><a href="/myhouse/addon/tower.php">Balcony</a></li>
 <li><a href="/myhouse/addon/tower_laboratory.php">Laboratory</a></li>
 <li class="activetab"><a href="/myhouse/addon/tower_bell.php">Bell Tower</a></li>
</ul>
<?= $message ?>
<p>It looks great!</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
