<?php
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/formatting.php';
require_once 'commons/checkpet.php';
require_once 'commons/houselib.php';

if(!array_key_exists('informonly', $_GET))
{
  // UPDATE THE PETS
  check_pets($user["idnum"]);
  load_user_pets($user, $userpets);

  if(strlen($_GET['goto']) > 0)
    header('Location: ' . $_GET['goto']);
  else
    header('Location: /myhouse.php');

  exit();
}

$house = $database->FetchSingle('SELECT lasthour FROM monster_houses WHERE userid=' . $user['idnum'] . ' LIMIT 1');

$total_hours = floor(($now - $house['lasthour']) / (60 * 60));

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Too Many Hours to Simulate</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <h4>Too Many Hours to Simulate</h4>
	<p><?= $SETTINGS['site_name'] ?> does a lot of crazy math behind the scenes when you run hours.</p>
	<p>A LOT.</p>
  <p>And your house has sooooo many pets and items, that <?= $SETTINGS['site_name'] ?> was not able to run all the hours you requested in a reasonable amount of time.  (Sorry about that... too much math!)</p>
  <p>Your house still has <?= $total_hours ?> hours remaining.</p>
	<ul>
	 <li><a href="/myhouse.php">Return to My House</a></li>
	 <li><a href="/updatepets.php">Run more hours - as many as possible!</a></li>
	</ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
