<?php
$whereat = 'home';
$wiki = 'Multiverse';
$THIS_ROOM = 'Multiverse';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/universelib.php';
require_once 'commons/messages.php';

if($user['show_universe'] != 'yes')
{
  header('Location: ./myhouse.php');
  exit();
}

$CONTENT_STYLE = 'background-color: #000;';
$CONTENT_CLASS = 'universe';

$UNIVERSE_MESSAGES = array();

$object_id = (int)$_GET['id'];

$universe = get_universe($user['idnum']);

if($universe === false || $universe['stage'] != 'gameplay')
{
  header('Location: ./myuniverse.php');
  exit();
}

$planet = get_solar_system_object($object_id);

$solar_system = get_solar_system($planet['systemid']);

$galaxy = get_galaxy($solar_system['galaxyid']);

if($galaxy['universeid'] != $universe['idnum'])
{
  header('Location: ./myuniverse.php');
  exit();
}

if($universe['lastupdate'] < $now - (60 * 60 * 12))
  $universe = update_universe($universe);

$galaxy_fullname = galaxy_full_name($galaxy);
$system_fullname = solar_system_full_name($solar_system);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Universe &gt; <?= $galaxy_fullname ?> &gt; <?= $system_fullname ?> &gt; <?= $planet['name'] ?></title>
<?php include 'commons/head.php'; ?>
  <link rel="stylesheet" href="http://<?= $SETTINGS['static_domain'] ?>/css/multiverse_1.css" />
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="multiverse.php">The Multiverse</a> &gt; <a href="myuniverse.php">My Universe</a> &gt; <a href="universe_viewgalaxy.php?id=<?= $galaxy['idnum'] ?>"><?= $galaxy_fullname ?></a> &gt; <a href="universe_viewsystem.php?id=<?= $planet['systemid'] ?>"><?= $system_fullname ?></a> &gt; <?= $planet['name'] ?></h4>
<?php
if(count($UNIVERSE_MESSAGES))
  echo '<ul><li>' . implode('</li><li>', $UNIVERSE_MESSAGES) . '</li></ul>';
?>
<table>
 <tr>
  <th>Habitability</th>
  <td><?= habitability_description($planet['class']) ?></td>
 </tr>
 <tr>
  <th>Life</th>
  <td><?= life_description($planet['life']) ?></td>
 </tr>
<?php
if($planet['life'] > 0)
{
?>
 <tr>
  <th>Population</th>
  <td><?= population_description($planet['population']) ?></td>
 </tr>
<?php
}

if($planet['civilizationid'] > 0)
{
  $civ = get_universe_civilization($planet['civilizationid']);
?>
 <tr>
  <th>Civilization</th>
  <td><?= $civ['name'] ?></td>
 </tr>
<?php
}
?>
</table>
<h5>Rename <?= ucwords(planet_type($planet)) ?></h5>
<form action="universe_renameplanet.php?id=<?= $object_id ?>" method="post">
<p><input type="text" name="name" maxlength="60" value="<?= htmlspecialchars($planet['name']) ?>" class="universeinput" /> <input type="submit" value="Rename" class="universeinput" /></p>
</form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
