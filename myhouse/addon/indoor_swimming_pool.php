<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$whereat = 'home';
$wiki = 'Indoor Swimming Pool';
$THIS_ROOM = 'Indoor Swimming Pool';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/checkpet.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';

if(!addon_exists($house, 'Indoor Swimming Pool'))
{
  header('Location: /myhouse.php');
  exit();
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Indoor Swimming Pool</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Indoor Swimming Pool</h4>
<?php
room_display($house);
?>
  <p>The Indoor Swimming Pool allows you to host Swimming Race events at <a href="/park.php">The Park</a>.</p>
  <p>It also provides hourly esteem to your pets, and a small amount of hourly love.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
