<?php
require_once 'commons/init.php';

$whereat = 'home';
$wiki = 'Fireplace';
$THIS_ROOM = 'Fireplace';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/checkpet.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/fireplacelib.php';
require_once 'commons/utility.php';
require_once 'commons/badges.php';

$first_visit = false;

$fireplace = get_fireplace_byuser($user['idnum']);
if($fireplace === false)
{
  create_fireplace($user['idnum']);
  $fireplace = get_fireplace_byuser($user['idnum']);
  if($fireplace === false)
  {
    echo "Failed to load your fireplace.  Try reloading this page; if the problem persists, contact <a href=\"admincontact.php\">an administrator</a>.<br />\n";
    exit();
  }

  $first_visit = true;
}

$fire_hours = $fireplace['fire'];

if($fire_hours < 24)
{
  $inventory = get_houseinventory_byuser_forpets($user['user']);
  $itemcount = array();

  foreach($inventory as $item)
  {
    $details = get_item_byname($item['itemname']);
    if($details['flammability'] > 0)
      $itemcount[$details['itemname']]++;
  }

  if($_POST['action'] == 'fuel')
  {
    if($itemcount[$_POST['itemname']] > 0)
    {
      $details = get_item_byname($_POST['itemname']);

      delete_inventory_fromhome($user['user'], $_POST['itemname'], 1);

      if($itemcount[$_POST['itemname']] == 1)
        unset($itemcount[$_POST['itemname']]);
      else
        $itemcount[$_POST['itemname']]--;

      $command = 'UPDATE psypets_fireplaces SET fire=fire+' . $details['flammability'] . ' WHERE idnum=' . $fireplace['idnum'] . ' LIMIT 1';
      fetch_none($command, 'feeding fireplace');

      $fireplace['fire'] += $details['flammability'];

      $message = '<p><span class="success">The fire flares a little at the addition of ' . $_POST['itemname'] . '.</span></p>';

      log_fireplace_event($now, $user['idnum'], 'Added ' . $details['itemname'] . ' to the fire.');

      require_once 'commons/statlib.php';
      record_stat($user['idnum'], 'Burninated an Item in the Fireplace', 1);

      if($_POST['itemname'] == 'Small Greek Trireme')
      {
        $badges = get_badges_byuserid($user['idnum']);
        if($badges['trireme_burner'] == 'no')
        {
          set_badge($user['idnum'], 'trireme_burner');
          $badges['trireme_burner'] = 'yes';
          $message .= '<p class="success"><i>(You received the ' . $BADGE_DESC['trireme_burner'] . ' badge!)</i></p>';
        }
      }
      
      if(mt_rand(1, 20) <= $details['flammability'] && $_POST['itemname'] != 'Pyrium')
      {
        $message .= '<p><span class="failure">Agk, what\'s this?!  Smoke is getting in the house!</span></p>';
        add_inventory($user['user'], 'u:' . $user['idnum'], 'Smoke', 'Produced in ' . $user['display'] . '\'s Fireplace', 'home');

        record_stat($user['idnum'], 'Created Smoke in the Fireplace', 1);
      }
    }
  }
}
/*
$entries_per_page = 20;

$command = 'SELECT COUNT(idnum) AS c FROM psypets_fireplace_log WHERE userid=' . $user['idnum'];
$data = fetch_single($command, 'fetching count of fireplace logs');

$num_entries = (int)$data['c'];
$num_pages = ceil($num_entries / $entries_per_page);

$page = (int)$_GET['page'];
if($page < 1 || $page > $num_pages)
  $page = 1;

$command = 'SELECT * FROM psypets_fireplace_log WHERE userid=' . $user['idnum'] . ' ORDER BY idnum DESC LIMIT ' . (($page - 1) * $entries_per_page) . ',' . $entries_per_page;
$fireplace_log = fetch_multiple($command, 'fetching fireplace logs');
*/
require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Fireplace</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Fireplace</h4>
<?php
echo $message;

room_display($house);

$fire_hours = $fireplace['fire'];
?>
<ul class="tabbed">
 <li class="activetab"><a href="/myhouse/addon/fireplace.php">Fire</a></li>
 <li><a href="/myhouse/addon/fireplace_mantle.php">Mantle</a></li>
</ul>
<p><?php
if($fire_hours >= 24)
  echo 'Your fireplace is roaring, almost terrifying in its intensity.';
else if($fire_hours >= 20)
  echo 'The blaze in your fireplace easily heats the entire house.';
else if($fire_hours >= 16)
  echo 'A strong fire burns in your fireplace.';
else if($fire_hours >= 8)
  echo 'The fire in your fireplace is burning steadily.';
else if($fire_hours >= 4)
  echo 'A faint crackling can be heard coming from the small fire in your fireplace.';
else if($fire_hours >= 2)
  echo 'A waning fire dances in your fireplace.';
else if($fire_hours >= 1)
  echo 'Your fireplace is inhabited by a single, quivering flame.';

if($fire_hours > 0)
  echo '  It\'s been burning for ' . $fireplace['fireduration'] . ' hours now.</p>';
else
  echo 'Your fireplace is without fire.</p>';

if($fire_hours < 24)
{
  if(count($itemcount) > 0)
  {
?>
<h5>Feed the Flames</h5>
<form method="post">
<p><i>(You will not feed the total available amount at once!  Only one item will be fed to the fireplace at a time.)</i></p>
<table>
 <tr class="titlerow">
  <th></th>
  <th></th>
  <th>Item</th>
  <th>Available</th>
 </tr>
<?php
    $rowclass = begin_row_class();

    foreach($itemcount as $itemname=>$quantity)
    {
      $details = get_item_byname($itemname);
?>
 <tr class="<?= $rowclass ?>">
  <td><input type="radio" name="itemname" value="<?= $itemname ?>" /></td>
  <td class="centered"><?= item_display($details) ?></td>
  <td><?= $itemname ?></td>
  <td class="centered"><?= $quantity ?></td>
 </tr>
<?php
      $rowclass = alt_row_class($rowclass);
    }
?>
</table>
<p><input type="hidden" name="action" value="fuel" /><input type="submit" value="Burn" /></p>
</form>
<?php
  }
  else
    echo '<p>You do not have any appropriate kindling in your house.</p>';
}
else
  echo '<p>The fireplace is packed.  You\'ll have to wait before you can add more fuel.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
