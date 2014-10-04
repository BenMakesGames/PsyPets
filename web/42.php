<?php
namespace PsyPets;

$whereat = 'home';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/love.php';
require_once 'commons/messages.php';
require_once 'commons/questlib.php';

if($now_month != 10 || $now_day != 10 || $now_year != 2010)
{
  header('Location: /');
  exit();
}

$quest = get_quest_value($user['idnum'], 'fourty-two');

if($quest === false)
{
  add_inventory($user['user'], '', 'Life, the Universe and Everything', '', 'home');
  add_quest_value($user['idnum'], 'fourty-two', 1);
  
  $dialog = '
    <p>"Today\'s the day, don\'t you think?  10/10/10?  Why, that\'s 42 in binary!</p>
    <p>"Here, take this.  You never can be too prepared..."</p>
    <p><i>(You received Life, the Universe, and Everything!  Find it in your common room.)</i></p>
    <p>"Now, if you\'ll excuse me, there\'s somewhere I have to be!"</p>
  ';
}
else
{
  $dialog = '
    <p>"Today\'s the day, don\'t you think?  10/10/10?  Why, that\'s--</p>
    <p>"Hold on a tic... you look kind of...</p>
    <p>"Right!  Never mind all that!  Terribly sorry!  I\'ll just be on my way!"</p>
  ';
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; A Really Hoopy Frood</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
<h4>A Really Hoopy Frood</h4>
<p><i>A strange man is outside your door...</i></p>
<?= $dialog ?>
<p><i>Having said all that, he leaves.</i></p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
