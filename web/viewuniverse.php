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
  header('Location: /myhouse.php');
  exit();
}

$id = (int)$_GET['id'];

$CONTENT_STYLE = 'background-color: #000;';
$CONTENT_CLASS = 'universe';

$UNIVERSE_MESSAGES = array();

$universe = get_universe_by_id($id);

if($universe === false)
{
  header('Location: /multiverse.php');
  exit();
}

$owner = get_user_byid($universe['ownerid'], 'display');

$galactic_objects = get_universe_galactic_objects($universe);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $owner['display'] ?>'s Universe</title>
<?php include 'commons/head.php'; ?>
  <link rel="stylesheet" href="//<?= $SETTINGS['static_domain'] ?>/css/multiverse_1.css" />
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="multiverse.php">The Multiverse</a> &gt; <?= $owner['display'] ?>'s Universe</h4>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";

if($universe['stage'] == 'inflation')
  echo '<p style="padding-top:1em;">This universe is undergoing a period of rapid expansion during which time many exotic particles are continually created and destroyed.  Further, the entire universe is opaque: light cannot pass through it, since it is quickly absorbed and randomly re-emitted.</p><p>It is, in short, a place inhospitable to atoms, let alone stars or life.  But not to worry: things will sort themselves out before <em>too</em> long!</p>';
else if($universe['stage'] == 'recombination')
  echo '<p style="padding-top:1em;">This universe is undergoing a period of recombination, and Hydrogen and Helium atoms are beginning to form!  Soon the universe will become transparent, and, given about a billion years, stars may even begin to form...</p>';
else if($universe['stage'] == 'formation')
  echo '<p style="padding-top:1em;">In this universe, the first stars are beginning to form from pure Hydrogen and Helium.  Without the presence of heavier elements these stars are huge and short-lived.  Soon supernova will create metals and other interesting elements from which future stars, planets, and even entirely galaxies will be built!</p><p>You don\'t have long to wait now!</p>';

if($universe['stage'] == 'gameplay')
{
  echo '<div id="mahuniverse">';

  foreach($galactic_objects as $object)
  {
    $fullname = galaxy_full_name($object);
    echo '<div class="universeobject" style="left:' . ($object['x'] - 24) . 'px; top:' . ($object['y'] - 24) . 'px;"><a href="otheruniverse_viewgalaxy.php?id=' . $object['idnum'] . '"><img src="//' . $SETTINGS['static_domain'] . '/gfx/universe/' . $object['image'] . '" alt="' . $fullname . '" title="' . $fullname . '" /></a></div>';
  }
  
  echo '</div>';
//  echo '<ul><li><a href="myuniverse_history.php">View history</a></li></ul>';
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
