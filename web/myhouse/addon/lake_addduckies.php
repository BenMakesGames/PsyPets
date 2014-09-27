<?php
require_once 'commons/init.php';

$whereat = 'home';
$wiki = 'Lake';
$THIS_ROOM = 'Lake';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/checkpet.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/lakelib.php';
require_once 'commons/utility.php';
require_once 'commons/moonphase.php';

if(!addon_exists($house, 'Lake'))
{
  header('Location: /myhouse.php');
  exit();
}

$lake = get_lake_byuser($user['idnum']);
if($lake === false)
{
  header('Location: /myhouse/addon/lake.php');
  exit();
}

$command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE ' .
             'user=' . quote_smart($user['user']) . ' ' .
               'AND location LIKE \'home%\' AND location NOT LIKE \'home/$%\' ' .
               'AND itemname=\'Rubber Duck\'';
$data = fetch_single($command, 'fetching rubber ducks at home');

$ducks_available = (int)$data['c'];

if($_POST['submit'] == 'Add')
{
  $num_ducks = (int)$_POST['numducks'];
  
  if($num_ducks < 1)
    $message .= '<p class="failure">You cannot add fewer than 1 Rubber Ducky...</p>';
  else if($num_ducks > $ducks_available)
    $message .= '<p class="failure">You do not have that many Rubber Duckies!</p>';
  else
  {
    $deleted = delete_inventory_fromhome($user['user'], 'Rubber Duck', $num_ducks);

    if($deleted > 0)
    {
      $command = 'UPDATE psypets_lakes SET duckies=duckies+' . $deleted . ' WHERE userid=' . $user['idnum'] . ' LIMIT 1';
      fetch_none($command, 'updating lake');
    
      header('Location: /myhouse/addon/lake.php?duckies=' . $deleted);
      exit();
    }
  }
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user["display"] ?>'s House &gt; Lake &gt; Add Boat</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; <a href="/myhouse/addon/lake.php">Lake</a> &gt; Add Rubber Duckies</h4>
<?php
echo $message;

room_display($house);

if($ducks_available > 0)
{
?>
<img src="//<?= $SETTINGS['static_domain'] ?>/gfx/npcs/duckies.png" width="48" height="76" alt="" align="left" style="padding-right: 4px;" />
<p>You have <?= $ducks_available ?> Rubber Duck<?= $ducks_available == 1 ? 'y' : 'ies' ?> in your house.</p>
<p>How many would you like to add to your Lake?</p>
<form method="post">
<p><input name="numducks" maxlength="4" size="4" /> <input type="submit" name="submit" value="Add" /></p>
</form>
<?php
}
else
  echo '<p>You have no Rubber Duckies in your house.  (Or at least none that aren\'t in protected rooms.)</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
