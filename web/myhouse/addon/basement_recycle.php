<?php
require_once 'commons/init.php';

$whereat = 'home';
$wiki = 'Basement';
$THIS_ROOM = 'Basement';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/checkpet.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/basementlib.php';
require_once 'commons/utility.php';

if(!addon_exists($house, 'Basement'))
{
  header('Location: /myhouse.php');
  exit();
}

$percent = floor($house['curbasement'] * 100 / $house['maxbasement']);

$num_floors = $house['maxbasement'] / 100;
$max_take_apart = floor((max($house['maxbasement'] - 100, 0) - ($house['curbasement'] - 100)) / 100);

if($_POST['action'] == 'Take Apart')
{
	$take_apart = (int)$_POST['floors'];

	if($num_floors >= 2 && $take_apart <= $max_take_apart && $take_apart > 0)
	{
		leveldown_basement($user['idnum'], $take_apart);

		add_inventory_quantity($user['user'], 'u:' . $user['idnum'], 'Deed to 500 Units', 'Recovered from taking apart a Basement', 'storage/incoming', $take_apart);

		require_once 'commons/statlib.php';
		record_stat($user['idnum'], 'Took Apart a Basement Floor', $take_apart);

		if($take_apart == 1)
			header('Location: /myhouse/addon/basement_recycle.php?msg=144:Deed to 500 Units');
		else
			header('Location: /myhouse/addon/basement_recycle.php?msg=144:' . $take_apart . ' Deeds to 500 Units');

		exit();
	}
	else
		$error_message = '<span class="failure">Hm...</span>';
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Basement</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Basement <i>(<?= $house['curbasement'] . '/' . $house['maxbasement'] . '; ' . $percent ?>% full <a href="/basementsummary.php"><img src="/gfx/summary.png" width="18" height="16" alt="(summary)" border="0" /></a>)</i></h4>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";

room_display($house);
?>
<ul class="tabbed">
 <li><a href="/myhouse/addon/basement.php">View Basement</a></li>
 <li class="activetab"><a href="/myhouse/addon/basement_recycle.php">Take Apart Basement</a></li>
</ul>
<p>You may take entire floors of your basement apart (100 items worth of space) to get <a href="/encyclopedia2.php?i=1025">Deeds to 500 Units</a>.  <i>(Note: it takes a <a href="/encyclopedia2.php?i=1026">Deed to 1000 Units</a> to </i>add<i> a floor, but you only get a <a href="/encyclopedia2.php?i=1025">Deed to 500 Units</a> for </i>removing<i> one.)</i></p>
<p>You may not take apart the first floor.  (Or... the last floor.  However you wanna look at it.)</p>
<?php
if($num_floors >= 2)
{
	if($max_take_apart == 0)
		echo '<p>Your basement has too much stuff in it.  You\'ll have to empty some of that stuff out before you can start taking it apart.  <i>(You must have at least 100 items worth of <i>empty space</i>.)</i></p>';
	else
	{
		echo '
			<p>Of your basement\'s ' . $num_floors . ' floors, you may take apart up to ' . $max_take_apart . '.  How many will you take apart?</p>
			<form method="post" onsubmit="return confirm(\'Really?  Really-really?\');">
			<p><b>Floors:</b> <input type="text" name="floors" value="1" maxlength="' . strlen($max_take_apart) . '" size="' . strlen($max_take_apart) . '" /> <input type="submit" name="action" value="Take Apart" /></p>
			</form>
		';
	}
}
else
	echo '<p class="failure">Your basement must be at least 2 floors!  (You cannot take apart the first floor.)</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
