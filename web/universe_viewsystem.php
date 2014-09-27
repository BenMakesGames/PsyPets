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

$solar_system = get_solar_system($object_id);

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

$solar_system_style = 'background-image: url(//' . $SETTINGS['static_domain'] . '/gfx/universe/solarsystem.jpg);';

$stars = get_solar_system_stars($solar_system);
$objects = get_solar_system_objects($solar_system);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Universe &gt; <?= $galaxy_fullname ?> &gt; <?= $system_fullname ?></title>
<?php include 'commons/head.php'; ?>
  <link rel="stylesheet" href="//<?= $SETTINGS['static_domain'] ?>/css/multiverse_1.css" />
  <script type="text/javascript">
   function place_object(event)
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
if($universe['rocks'] >= 2 || $universe['gasgiants'] > 0)
{
  echo '$(\'#objectmenu\').html(\'<ul>';

  if($universe['rocks'] >= 2)
    echo '<li><a href="myuniverse_placeplanet.php?system=' . $object_id . '&x=\' + x + \'&y=\' + y + \'">Place planet...</a></li>';

  if($universe['gasgiants'] > 0)
    echo '<li><a href="myuniverse_placegasgiant.php?system=' . $object_id . '&x=\' + x + \'&y=\' + y + \'">Place gas giant</a></li>';

  if($universe['rocks'] >= 20)
    echo '<li><a href="myuniverse_placebelt.php?system=' . $object_id . '&x=\' + x + \'">Place asteroid belt</a></li>';

  echo '</ul>\');';
}
?>
   }
   
   $(document).ready(
     function()
     {
       $('#mahsolarsystem').bind('click', place_object);
     }
   );
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="multiverse.php">The Multiverse</a> &gt; <a href="myuniverse.php">My Universe</a> &gt; <a href="universe_viewgalaxy.php?id=<?= $galaxy['idnum'] ?>"><?= $galaxy_fullname ?></a> &gt; <?= $system_fullname ?></h4>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";

if(count($UNIVERSE_MESSAGES))
  echo '<ul><li>' . implode('</li><li>', $UNIVERSE_MESSAGES) . '</li></ul>';

include 'commons/universewealth.php';
?>
     <p style="padding-top:1em;">Click on a planet, belt, or other object to get a closer look, or click on an empty area to create something new.  <strong>Scroll down for more information!</strong></p>
     <div id="mahsolarsystem" style="<?= $solar_system_style ?>">
     <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/universe/ruler/temp.png" width="600" height="24" alt="temperature ruler" />
<?php
foreach($objects as $object)
{
  $fullname = $object['name'];
  echo '<div class="universeobject" style="left:' . ($object['x'] - $object['image_size'] / 2) . 'px; top:' . ($object['y'] - $object['image_size'] / 2) . 'px;"><a href="/universe_viewobject.php?id=' . $object['idnum'] . '"><img src="//' . $SETTINGS['static_domain'] . '/gfx/universe/planet/' . $object['image'] . '" alt="' . $fullname . '" title="' . $fullname . '" /></a></div>';
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
<h5>Rename System</h5>
<form action="universe_renamesystem.php?id=<?= $object_id ?>" method="post">
<p><input type="text" name="name" maxlength="60" value="<?= htmlspecialchars($solar_system['name']) ?>" class="universeinput" /> <input type="submit" value="Rename" class="universeinput" /></p>
</form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
