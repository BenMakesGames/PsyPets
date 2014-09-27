<?php
require_once 'commons/init.php';

$require_login = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Help Desk &gt; Pet Care &gt; Wounds</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <h4><a href="/help/">Help Desk</a> &gt; <a href="/help/petcare.php">Pet Care</a> &gt; Wounds</h4>
  <h5>How Pets Get Wounded</h5>
  <p>Pets can get wounded from any hourly activity, from adventuring and hunting - some of the riskier activities - to carpentry and sewing.</p>
  <p>Pets get far fewer wounds from indoorsy activities, and the wounds they do suffer from these tend to be lighter.</p>
  <h5>The Effects of Wounds</h5>
  <p><strong>Rest assured: your wounded pet is not at risk of dying!</strong></p>
  <p>A wound reduces a pet's skills, and a wounded pet may spend time resting up rather than doing anything else.</p>
  <h5>Healing Wounds</h5>
  <p>Wounds heal naturally over time, and pets that rest up heal their wounds even faster.</p>
  <p>You can also equip your pet with bandaging items such as <?= item_text_link('Mummy Wrap') ?> to speed the healing the process.  Finally, giving a pet medicines such as <?= item_text_link('Diluted Medicine') ?> can heal a wound significantly, and increases a pet's affection toward you.</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
