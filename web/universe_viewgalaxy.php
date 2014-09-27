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

$CONTENT_STYLE = 'background-color: #000;';
$CONTENT_CLASS = 'universe';

$UNIVERSE_MESSAGES = array();

$object_id = (int)$_GET['id'];

$galaxy = get_galaxy($object_id);

$universe = get_universe_by_id($galaxy['universeid']);

if($universe === false || $universe['stage'] != 'gameplay')
{
  header('Location: /multiverse.php');
  exit();
}

$is_owner = ($universe['idnum'] == $user['idnum']);

if($is_owner && $universe['lastupdate'] < $now - (60 * 60 * 12))
  $universe = update_universe($universe);

//$solar_systems = get_universe_solar_systems($galaxy);

$object_type = galaxy_type($galaxy);
$fullname = galaxy_full_name($galaxy);

$central_object_graphic = 'stars/blackhole_1.png';
$central_object_name = 'Black Hole';

$solar_systems = get_solar_systems($galaxy);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Universe &gt; <?= $fullname ?></title>
<?php include 'commons/head.php'; ?>
  <link rel="stylesheet" href="//<?= $SETTINGS['static_domain'] ?>/css/multiverse_1.css" />
  <script type="text/javascript">
   function place_star(event)
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
if($universe['stars'] > 0)
{
  echo '$(\'#objectmenu\').html(\'<ul>';

  if($universe['stars'] > 0)
    echo '<li><a href="myuniverse_placesystem.php?galaxy=' . $object_id . '&x=\' + x + \'&y=\' + y + \'">Place solar system</a></li>';

  echo '</ul>\');';
}
?>
   }
   
   $(document).ready(
     function()
     {
       $('#mahgalaxy').bind('click', place_star);
     }
   );
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="multiverse.php">The Multiverse</a> &gt; <a href="myuniverse.php">My Universe</a> &gt; <?= $fullname ?></h4>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";

if(count($UNIVERSE_MESSAGES))
  echo '<ul><li>' . implode('</li><li>', $UNIVERSE_MESSAGES) . '</li></ul>';

include 'commons/universewealth.php';
?>
     <p style="padding-top:1em;">Click on a star, black hole, or other object to get a closer look, or click on an empty area to create something new.  <strong>Scroll down for more options!</strong></p>
     <div id="mahgalaxy">
<?php
foreach($solar_systems as $object)
{
  $fullname = solar_system_full_name($object);
  echo '<div class="universeobject" style="left:' . ($object['x'] - 24) . 'px; top:' . ($object['y'] - 24) . 'px;"><a href="/universe_viewsystem.php?id=' . $object['idnum'] . '"><img src="//' . $SETTINGS['static_domain'] . '/gfx/universe/' . $object['image'] . '" alt="' . $fullname . '" title="' . $fullname . '" /></a></div>';
}
?>
      <div class="universeobject" style="left:276px; top:276px;"><img src="http://<?= $SETTINGS['static_domain'] ?>/gfx/universe/<?= $central_object_graphic ?>" alt="<?= $central_object_name ?>" title="<?= $central_object_name ?>" /></div>
     </div>
     <h5>Star Nursery</h5>
     <table>
      <tr>
       <th>Stardust</th>
       <td><?php if($galaxy['stardust'] > 0) { ?><div class="universeprogressbar" onmouseover="Tip('<?= floor($galaxy['stardust'] * 100 / 12) ?>%');"><img src="gfx/red_shim.gif" height="12" width="<?= min(50, floor($galaxy['stardust'] * 50 / 12)) ?>" alt="" /></div><?php } else echo '<i class="dim">no progress</i>'; ?></td>
       <td><?php if($galaxy['stardust'] >= 12) echo '<a href="universe_harvestgalaxy.php?id=' . $object_id . '">Harvest stars (' . floor($galaxy['stardust'] / 12) . ')</a>'; ?></td>
      </tr>
     </table>
     <p>You may feed this <?= $object_type ?> Hydrogen in order to birth new stars!</p>
<?php
if($universe['hydrogen'] > 0)
{
?>
<form action="universe_feedgalaxy.php?id=<?= $object_id ?>" method="post">
<p><input type="text" name="hydrogen" size="2" maxlength="<?= strlen($universe['hydrogen']) ?>" class="universeinput" /> / <?= $universe['hydrogen'] ?> <input type="submit" value="Feed" class="universeinput" /></p>
</form>
<?php
}
else
  echo '<p>You have no Hydrogen.</p>';
?>
<h5>Rename <?= ucfirst($object_type) ?></h5>
<form action="universe_renamegalaxy.php?id=<?= $object_id ?>" method="post">
<p><input type="text" name="name" maxlength="60" value="<?= htmlspecialchars($galaxy['name']) ?>" class="universeinput" /> <input type="submit" value="Rename" class="universeinput" /></p>
</form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
