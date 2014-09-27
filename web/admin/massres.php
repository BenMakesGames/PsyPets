<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";

if($admin['massgift'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

die('Hey, wait... think about this: unless something\'s changed, we don\'t do mass resurrections any more!');

if($_GET['step'] == 2)
{
  $command = 'SELECT display,idnum FROM monster_users WHERE (SELECT COUNT(*) FROM monster_pets WHERE monster_pets.user=monster_users.user AND monster_pets.dead=\'no\')=0';
  $users_to_update = $database->FetchMultiple(($command, 'adminmassres.php');
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Admin Tools &gt; Perform Mass Resurrection</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Admin Tools</a> &gt; Perform Mass Resurrection</h4>
<?php
if($_GET['step'] == 2)
{
?>
     <p>The following <?= count($users_to_update) ?> residents contain no living pets.</p>
     <ul>
<?php
  $ids = array();
  foreach($users_to_update as $u)
  {
    echo '<li>' . $u['display'] . ' (' . $u['idnum'] . ')</li>' . "\n";
    $ids[] = $u['idnum'];
  }
?>
     </ul>
     <p>Updating these residents' houses so that they are caught up to today (to prevent further pet death)...</p>
<?php
  $command = 'UPDATE monster_houses SET lasthour=lasthour+FLOOR((' . $now . '-lasthour)/3600)*3600 WHERE userid IN (' . implode(',', $ids) . ') LIMIT ' . count($ids);
  $database->FetchNone(($command, 'adminmassres.php');
?>
     <ul><li><?= $database->AffectedRows() ?> residents' houses were caught up.</li></ul>
     <p>Resurrecting pets...</p>
<?php
  $command = 'UPDATE monster_pets SET dead=\'no\',energy=12,food=12,safety=12,love=12,esteem=12 WHERE dead!=\'no\'';
  $database->FetchNone(($command, 'adminmassres.php');
  $res_count = $database->AffectedRows();
?>
     <ul><li><?= $res_count ?> pets were resurrected.</li></ul>
<?php

  $author = get_user_byuser($SETTINGS['site_ingame_mailer']);
  news_post($author['idnum'], 'important', 'A mass resurrection has taken place!', '{r ' . $user['display'] . '} forced a mass resurrection, bringing back a total of ' . $res_count . ' pets!  Remember, if you clicked "move on" for any pets, they will not have been revived in this way.  The game warns you about clicking that button for a reason!');
}
else
{
  $command = 'SELECT COUNT(*) AS c FROM monster_pets WHERE dead!=\'no\'';
  $count = $database->FetchSingle($command, 'adminmassres.php');
?>
     <p>There are <?= $count['c'] ?> dead pets.  Resurrect them all!?!</p>
<?php
  if($count['c'] > 0)
  {
?>
     <ul><li><a href="adminmassres.php?step=2">Indeed!</a></li></ul>
<?php
  }
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
