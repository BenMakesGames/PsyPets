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

$galaxy = get_galaxy($object_id);

if($galaxy === false)
{
  header('Location: ./multiverse.php');
  exit();
}

$universe = get_universe_by_id($galaxy['universeid']);

if($universe === false || $universe['stage'] != 'gameplay')
{
  header('Location: ./multiverse.php');
  exit();
}

$owner = get_user_byid($universe['ownerid'], 'display');

//$solar_systems = get_universe_solar_systems($galaxy);

$object_type = galaxy_type($galaxy);
$fullname = galaxy_full_name($galaxy);

$central_object_graphic = 'stars/blackhole_1.png';
$central_object_name = 'Black Hole';

$solar_systems = get_solar_systems($galaxy);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $owner['display'] ?>'s Universe &gt; <?= $fullname ?></title>
<?php include 'commons/head.php'; ?>
  <link rel="stylesheet" href="//<?= $SETTINGS['static_domain'] ?>/css/multiverse_1.css" />
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="multiverse.php">The Multiverse</a> &gt; <a href="viewuniverse.php?id=<?= $universe['idnum'] ?>"><?= $owner['display'] ?>'s Universe</a> &gt; <?= $fullname ?></h4>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";

if(count($UNIVERSE_MESSAGES))
  echo '<ul><li>' . implode('</li><li>', $UNIVERSE_MESSAGES) . '</li></ul>';

echo '<div id="mahgalaxy">';

foreach($solar_systems as $object)
{
  $fullname = solar_system_full_name($object);
  echo '<div class="universeobject" style="left:' . ($object['x'] - 24) . 'px; top:' . ($object['y'] - 24) . 'px;"><a href="otheruniverse_viewsystem.php?id=' . $object['idnum'] . '"><img src="http://' . $SETTINGS['static_domain'] . '/gfx/universe/' . $object['image'] . '" alt="' . $fullname . '" title="' . $fullname . '" /></a></div>';
}
?>
      <div class="universeobject" style="left:276px; top:276px;"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/universe/<?= $central_object_graphic ?>" alt="<?= $central_object_name ?>" title="<?= $central_object_name ?>" /></div>
     </div>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
