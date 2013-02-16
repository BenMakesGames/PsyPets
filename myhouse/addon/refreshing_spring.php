<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$whereat = 'home';
$wiki = 'Refreshing Spring';
$THIS_ROOM = 'Refreshing Spring';

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

if(!addon_exists($house, 'Refreshing Spring'))
{
  header('Location: /myhouse.php');
  exit();
}

$titles = array(
  1 => 'Adventuring Archaeologist',
  'Animal Trainer',
  'Chevalier',
  'Critic',
  'Equerry',
  'Extra',
  'Fishmonger',
  'Flower-bomber',
  'Fluffmonger',
  'Gentleman',
  'Gentlewoman',
  'Herb Strewer',
  'Herbivore',
  'Inquisitor',
  'Jester',
  'n00b-slayer',
  'Plaza-lurker',
  'Powder Monkey',
  'Powerful Wizard',
  'PsyChef',
  'Reader',
  'Resident',
  'Spy',
  'Visionary',
  'Writer',
  'Your Worst Nightmare',
);

if(array_key_exists('title', $_GET))
{
  if(array_key_exists($_GET['title'], $titles))
  {
    $user['title'] = $titles[$_GET['title']];

    $command = 'UPDATE monster_users SET title=' . quote_smart($user['title']) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    fetch_none($command, 'updating resident\'s title');

    $message .= '<p class="success">Your title was changed successfully!  You are now <b>' . $user['display'] . ', ' . $user['title'] . '</b>.</p>';
  }
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user["display"] ?>'s House &gt; Refreshing Spring</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Refreshing Spring</h4>
<?php
echo $message;
room_display($house);
?>
<p>The Refreshing Spring inexplicably offers you several new titles...</p>
<p>You are currently known as <b><?= $user['display'] . ', ' . $user['title'] ?></b>.  Would you like to change your title?</p>
<ul>
<?php
foreach($titles as $i=>$title)
  echo ' <li><a href="/myhouse/addon/refreshing_spring.php?title=' . $i . '">' . $title . '</a></li>';
?>
</ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
