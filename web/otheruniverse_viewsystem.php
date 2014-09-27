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

$solar_system = get_solar_system($object_id);

if($solar_system === false)
{
  header('Location: ./multiverse.php');
  exit();
}

$galaxy = get_galaxy($solar_system['galaxyid']);

$universe = get_universe_by_id($galaxy['universeid']);

if($universe === false || $universe['stage'] != 'gameplay')
{
  header('Location: ./multiverse.php');
  exit();
}

$owner = get_user_byid($universe['ownerid'], 'display');

$galaxy_fullname = galaxy_full_name($galaxy);
$system_fullname = solar_system_full_name($solar_system);

$solar_system_style = 'background-image: url(//' . $SETTINGS['static_domain'] . '/gfx/universe/solarsystem.jpg);';

$stars = get_solar_system_stars($solar_system);
$objects = get_solar_system_objects($solar_system);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $owner['display'] ?>'s Universe &gt; <?= $galaxy_fullname ?> &gt; <?= $system_fullname ?></title>
<?php include 'commons/head.php'; ?>
  <link rel="stylesheet" href="//<?= $SETTINGS['static_domain'] ?>/css/multiverse_1.css" />
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="multiverse.php">The Multiverse</a> &gt; <a href="viewuniverse.php?id=<?= $universe['idnum'] ?>"><?= $owner['display'] ?>'s Universe</a> &gt; <a href="otheruniverse_viewgalaxy.php?id=<?= $galaxy['idnum'] ?>"><?= $galaxy_fullname ?></a> &gt; <?= $system_fullname ?></h4>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";
?>
     <div id="mahsolarsystem" style="<?= $solar_system_style ?>">
     <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/universe/ruler/temp.png" width="600" height="10" alt="temperature ruler" />
<?php
foreach($objects as $object)
{
  $fullname = $object['name'];
  echo '<div class="universeobject" style="left:' . ($object['x'] - $object['image_size'] / 2) . 'px; top:' . ($object['y'] - $object['image_size'] / 2) . 'px;"><a href="otheruniverse_viewobject.php?id=' . $object['idnum'] . '"><img src="//' . $SETTINGS['static_domain'] . '/gfx/universe/planet/' . $object['image'] . '" alt="' . $fullname . '" title="' . $fullname . '" /></a></div>';
}
?>
     </div>
<h5>Star Information</h5>
<table>
 <thead>
  <tr><th>Name</th><th>Type</th><th>Size</th></tr>
 </thead>
 <tbody>
<?php
foreach($stars as $star)
{
  echo '
    <tr>
     <th valign="top">' . $star['name'] . '</th>
     <td valign="top">' . $STAR_TYPE_NAMES[$star['type']] . '</th>
     <td valign="top">' . round($star['mass'] / 100, 2) . 'M<sub>&#9737;</sub></td>
    </tr>
  ';
}
?>
 </tbody>
</table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
