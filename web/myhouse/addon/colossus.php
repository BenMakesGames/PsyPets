<?php
require_once 'commons/init.php';

$whereat = 'home';
$wiki = 'Colossus';
$THIS_ROOM = 'Colossus';

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

if(!addon_exists($house, 'Colossus'))
{
  header('Location: /myhouse.php');
  exit();
}

$badges = get_badges_byuserid($user['idnum']);
if($badges['colossus'] == 'no')
{
  set_badge($user['idnum'], 'colossus');
  $message = '<ul><li class="success">Congratulations!  You earned The Colossus badge!</li></ul>';
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user["display"] ?>'s House &gt; Colossus</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Colossus</h4>
<?php
echo $message;
room_display($house);
?>
     <p>The Colossus reduces daily storage fees by 20%.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
