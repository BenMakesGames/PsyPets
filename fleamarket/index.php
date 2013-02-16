<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$wiki = 'Flea_Market';
$require_petload = 'no';

require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/inventory.php";
require_once "commons/formatting.php";

if($NO_PVP)
{
  header('Location: /lostdata.php');
  exit();
}

if($user['license'] == 'no')
{
  header('Location: /ltc.php?dialog=2');
  exit();
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Flea Market</title>
<?php include "commons/head.php"; ?>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/adrate3.js"></script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<?php include 'commons/bcmessage2.php'; ?>
<?php include 'commons/randomstores.php'; ?>
     <h4>Flea Market</h4>
     <ul class="tabbed">
      <li class="activetab"><a href="/fleamarket/">Flea Market</a></li>
      <li><a href="/favorstores.php">Custom Item Market</a></li>
     </ul>
     <ul>
      <li><a href="/mystore.php">Manage my store</a></li>
      <li><a href="/fleamarket/viewall.php">View all open stores</a></li>
     </ul>
     <h5 id="itemsearch">Item Search</h5>
     <form action="/fleamarket/search.php" method="post">
     <p>Item name: <input name="itemname" /> <input type="submit" value="Search" style="width:100px;" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
