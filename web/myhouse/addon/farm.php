<?php
require_once 'commons/init.php';

$whereat = 'home';
$wiki = 'Farm';
$THIS_ROOM = 'Farm';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/checkpet.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/farmlib.php';

if(!addon_exists($house, 'Farm'))
{
  header('Location: /myhouse.php');
  exit();
}

$farm = get_farm($user['idnum']);

$rooms['storage'] = 'Storage';

if($user['license'] == 'yes')
{
  $rooms['storage/locked'] = 'Locked Storage';
  $rooms['storage/mystore'] = 'My Store';
}

$rooms['home'] = 'Common';

if(strlen($house['rooms']) > 0)
{
  $house_rooms = explode(',', $house['rooms']);
  
  foreach($house_rooms as $room)
    $rooms['home/' . $room] = $room;
}

if($_GET['action'] == 'activate')
  allow_farming_in_farm($farm);
else if($_GET['action'] == 'deactivate')
  disallow_farming_in_farm($farm);
else if($_POST['action'] == 'move' && $farm['silo_quantity'] > 0)
{
  $quantity = (int)$_POST['quantity'];
  $destination = $_POST['destination'];
  
  if($quantity <= 0)
    $message = '<p class="failure">Of course, you can\'t move less than 1 ' . $farm['field_crop'] . '.  I mean, we can pretend you did, I guess, if you like.</p>';
  else if($quantity > $farm['silo_quantity'])
    $message = '<p class="failure">You don\'t have that much ' . $farm['field_crop'] . '!</p>';
  else if(!array_key_exists($destination, $rooms))
    $message = '<p class="failure">No such room exists!</p>';
  else
  {
    take_grain_from_farm($farm, $quantity);
    add_inventory_quantity($user['user'], '', $farm['field_crop'], 'Grown at ' . $user['display'] . '\'s Farm', $destination, $quantity);

    if($quantity == 1)
      $message = '<p class="success">1 ' . $farm['field_crop'] . ' has been moved to ' . $rooms[$destination] . '.</p>';
    else
      $message = '<p class="success">' . $quantity . ' ' . $farm['field_crop'] . ' have been moved to ' . $rooms[$destination] . '.</p>';
  }
}
else if($_POST['action'] == 'Change' && $farm['silo_quantity'] == 0)
{
  $crop = $_POST['crop'];
  
  if(!in_array($crop, $FARM_CROPS))
    $message = '<p class="failure">Oops!  You forgot to pick what you want to grow!</p>';
  else
  {
    change_crop_in_farm($farm, $crop);
    
    $message = '<p class="success">You are now growing ' . $crop . '!</p>';
  }
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Farm &gt; Fields &amp; Silo</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Farm &gt; Fields &amp; Silo</h4>
<?php
room_display($house);
?>
<ul class="tabbed">
 <li class="activetab"><a href="/myhouse/addon/farm.php">Fields &amp; Silo</a></li>
 <li><a href="/myhouse/addon/farm_chicken_coop.php">Chicken Coop</a></li>
</ul>
<?php
echo $message;
?>
<h5>The Fields</h5>
<p>Gardening pets may work in the Fields as an hourly activity.  When they do, they'll deposit the produce into the Silo.</p>
<?php
if($farm['field_active'] == 'yes')
  echo '
    <p class="success">Working in the Fields is currently allowed.</p>
    <ul><li><a href="/myhouse/addon/farm.php?action=deactivate">Disallow working in the fields</a></li></ul>
  ';
else
  echo '
    <p class="failure">Working in the Fields is currently not allowed.</p>
    <ul><li><a href="/myhouse/addon/farm.php?action=activate">Allow working in the fields</a></li></ul>
  ';

if($farm['silo_quantity'] > 0)
{
  $crop_disabled = ' disabled="disabled"';
  $crop_message = ' <i>(You must empty the Silo to change the crop.)</i>';
}
else
  $crop_disabled = '';

echo '<form method="post"><p><select name="crop"' . $crop_disabled . '>';

foreach($FARM_CROPS as $crop)
{
  if($crop == $farm['field_crop'])
    echo '<option value="' . $crop . '" selected="selected">';
  else
    echo '<option value="' . $crop . '">';

  echo $crop . '</option>';
}

echo '</select> <input type="submit" name="action" value="Change"' . $crop_disabled . '/>' . $crop_message . '</p></form>';

echo '
  <h5>The Silo</h5>
  <p>There is ' . $farm['silo_quantity'] . ' ' . $farm['field_crop'] . ' in the Silo; it can store up to ' . $FARM_SILO_SIZE . '.</p>
';

if($farm['silo_quantity'] > 0)
{
?>
<form method="post">
<p>Move <input type="text" name="quantity" size="2" maxlength="<?= strlen($farm['silo_quantity']) ?>" /> to <select name="destination">
<?php
  foreach($rooms as $room=>$desc)
    echo '<option value="' . $room . '">' . $desc . '</option>';
?>
</select><input type="hidden" name="action" value="move" /> <input type="submit" value="Go" /></p>
</form>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
