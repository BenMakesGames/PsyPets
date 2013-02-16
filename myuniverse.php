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

$universe = get_universe($user['idnum']);

if($universe === false)
  $universe = create_universe($user['idnum']);

if($universe['lastupdate'] < $now - (60 * 60 * 12) || $_POST['action'] == 'Force Event')
{
  $universe = update_universe($universe);
}

$galactic_objects = get_universe_galactic_objects($universe);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Universe</title>
<?php include 'commons/head.php'; ?>
  <link rel="stylesheet" href="//<?= $SETTINGS['static_domain'] ?>/css/multiverse_1.css" />
  <script type="text/javascript">
   function place_galactic_object(event)
   {
     var x = event.pageX - this.offsetLeft;
     var y = event.pageY - this.offsetTop;
   
     if(x < 24 || x >= 600 - 24 || y < 24 || y >= 600 - 24)
       return;
   
     $('#objectmenu')
       .css('left', event.pageX + 'px')
       .css('top', event.pageY + 'px')
       .css('display', 'block');
       
<?php
if($universe['galaxies'] > 0 || $universe['clouds'] > 0)
{
  echo '$(\'#objectmenu\').html(\'<ul>';

  if($universe['galaxies'] > 0)
    echo '<li><a href="myuniverse_placegalaxy.php?x=\' + x + \'&y=\' + y + \'">Place Galaxy</a></li>';

  if($universe['clouds'] > 0)
    echo '<li><a href="myuniverse_placecloud.php?x=\' + x + \'&y=\' + y + \'">Place Cloud</a></li>';

  if($universe['hydrogen'] >= 20)
    echo '<li><a href="myuniverse_buildcloud.php?x=\' + x + \'&y=\' + y + \'">Build Cloud from 20 Hydrogen</a></li>';

  echo '</ul>\');';
}
?>
   }
   
   $(document).ready(
     function()
     {
       $('#mahuniverse').bind('click', place_galactic_object);
     }
   );
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="multiverse.php">The Multiverse</a> &gt; My Universe</h4>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";

if(count($UNIVERSE_MESSAGES))
  echo '<ul><li>' . implode('</li><li>', $UNIVERSE_MESSAGES) . '</li></ul>';

include 'commons/universewealth.php';

if($universe['stage'] == 'inflation')
  echo '<p style="padding-top:1em;">Your universe is undergoing a period of rapid expansion during which time many exotic particles are continually created and destroyed.  Further, the entire universe is opaque: light cannot pass through it, since it is quickly absorbed and randomly re-emitted.</p><p>It is, in short, a place inhospitable to atoms, let alone stars or life.  But not to worry: things will sort themselves out before <em>too</em> long!</p>';
else if($universe['stage'] == 'recombination')
  echo '<p style="padding-top:1em;">Your universe is undergoing a period of recombination, and Hydrogen and Helium atoms are beginning to form!  Soon the universe will become transparent, and, given about a billion years, stars may even begin to form...</p>';
else if($universe['stage'] == 'formation')
  echo '<p style="padding-top:1em;">In your universe, the first stars are beginning to form from pure Hydrogen and Helium.  Without the presence of heavier elements these stars are huge and short-lived.  Soon supernova will create metals and other interesting elements from which future stars, planets, and even entirely galaxies will be built!</p><p>You don\'t have long to wait now!</p>';

if($universe['stage'] == 'gameplay')
{
?>
     <p style="padding-top:1em;">Click on a Galaxy, Cloud, or other galactic object to get a closer look, or click on an empty area to create something new.</p>
     <div id="mahuniverse">
<?php
  foreach($galactic_objects as $object)
  {
    $fullname = galaxy_full_name($object);
    echo '<div class="universeobject" style="left:' . ($object['x'] - 24) . 'px; top:' . ($object['y'] - 24) . 'px;"><a href="universe_viewgalaxy.php?id=' . $object['idnum'] . '"><img src="//' . $SETTINGS['static_domain'] . '/gfx/universe/' . $object['image'] . '" alt="' . $fullname . '" title="' . $fullname . '" /></a></div>';
  }
?>
     </div>
     <ul><li><a href="myuniverse_history.php">View history</a></li></ul>
<?php
}

if($user['idnum'] == 1)
  echo '<form action="myuniverse.php" method="post"><p><input type="submit" name="action" value="Force Event" class="universeinput" /></p></form>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
