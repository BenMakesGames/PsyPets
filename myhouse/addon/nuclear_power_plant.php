<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/grammar.php';
require_once 'commons/utility.php';
require_once 'commons/chemistrylib.php';
require_once 'commons/houselib.php';

$THIS_ROOM = 'Nuclear Power Plant';

$house_object = House::GetByOwnerID($user['idnum']);

if(!$house_object->HasAddOn('Nuclear Power Plant'))
{
  header('Location: /myhouse.php');
  exit();
}

$power_plant = get_powerplant($user['idnum']);

if($power_plant === false)
  die('could not load/create powerplant.  bad error.  please let ' . $SETTINGS['author_resident_name'] . ' know.');

$POWER_BAR_WIDTH = 200;
  
include 'commons/html.php';
?>
 <head>
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Nuclear Power Plant</title>
  <style type="text/css">
  #powerbar
  {
    width: <?= $POWER_BAR_WIDTH ?>px;
    text-align: center;
    height: 20px;
    border: 1px solid #344;
    background-image: url(//<?= $SETTINGS['static_domain'] ?>/gfx/addons/powerplant/powerbar.png);
    background-repeat: no-repeat;
    background-position: <?= -$POWER_BAR_WIDTH + floor($power_plant['power'] * $POWER_BAR_WIDTH / $power_plant['max_power']) ?>px 0;
  }
  </style>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Nuclear Power Plant</h4>
  <?php room_display($house_object->RawData()); ?>
  <div id="powerbar">Power: <?= $power_percent ?>%</div>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
