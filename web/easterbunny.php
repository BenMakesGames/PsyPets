<?php
$whereat = 'home';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/petblurb.php';
require_once 'commons/love.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';

if($EASTER == 0)
{
  header('Location: /');
  exit();
}

if($now < $user['tot_time'])
{
  header('Location: /');
  exit();
}

$house = get_house_byuser($user['idnum']);

if($house === false)
{
  echo "Failed to load your house.<br />\n";
  exit();
}

$walls = explode(',', $house['wallpapers']);
$wallpaper = $walls[0];

if($wallpaper != 'none')
{
  require_once 'commons/backgrounds.php';

  if(is_numeric($wallpaper))
    $CONTENT_STYLE = 'background: #fff url(//' . $SETTINGS['site_domain'] . '/gfx/postwalls/' . $POST_BACKGROUNDS[$wallpaper] . '.png) repeat;';
  else
    $CONTENT_STYLE = 'background: #fff url(//' . $SETTINGS['site_domain'] . '/gfx/walls/' . $wallpaper . '.png) repeat;';
}

$message = false;

if($_POST['action'] == 'tot')
{
  $itemname = trim($_POST['itemname']);

  $details = get_item_byname($itemname);
  
  if(stripos($itemname, 'chocolate') === false)
  {
    $message = '<p class="failure">The "easter bunny" tilts its head and looks at the ' . $_POST['itemname'] . ' for a moment, then looks back at you.</p>';
  }
  else if($details['custom'] != 'no')
  {
    $message = '<p class="failure">You almost hand over the ' . $itemname . ', but then remember how valuable it is!  (It\'s a limited item, or maybe even a custom!)</p>';
  }
  else
  {
    $consumed = delete_inventory_fromhome($user['user'], $itemname, 1);

    if($consumed > 0)
    {
      $value = $details['edibleenergy'] + $details['ediblefood'] + $details['ediblesafety'] + $details['ediblelove'] + $details['edibleesteem'];
      
      // get a reward-item based on the fed-item's value
      if($value == 0)
        $reward = 'Plastic Egg';
      else
      {
        if(mt_rand(1, 100) < $value / 2)
        {
          $possible_rewards = array('Copper-Dyed Egg', 'Silver-Dyed Egg', 'Gold-Dyed Egg');
          $reward = $possible_rewards[array_rand($possible_rewards)];
        }
        else
          $reward = 'Plastic Egg';
      }
      
      $user['tot_time'] = $now + mt_rand(40 * 60, 50 * 60);

      $command = 'UPDATE monster_users SET tot_time=' . $user['tot_time'] . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'trick or treating (easter)');

      $msg = ($value < 10 ? 133 : 134);

      add_inventory($user['user'], '', $reward, 'Given to you by the "easter bunny".', 'storage/incoming');
      flag_new_incoming_items($user['user']);

      header('Location: ./incoming.php?msg=' . $msg . ':' . $reward);
      exit();
    }
    else
      echo '<p class="failure">You do not have a ' . $_POST['itemname'] . '...</p>';
  }
}

$command = 'SELECT itemname,COUNT(idnum) AS quantity FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location LIKE \'home%\' AND location NOT LIKE \'home/$%\' AND itemname LIKE \'%chocolate%\' GROUP BY itemname ORDER BY itemname';
$inventory = $database->FetchMultiple($command, 'fetching inventory');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Front Door</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<?php
if($its_your_birthday)
{
?>
<div style="background: url('gfx/streamers_yellow.png'); height: 48px; font-size: 48px;"><center><img src="gfx/happy_birthday.png" width="450" height="48" /></center></div>
<?php
}
?>
<?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
<h4><?= $user['display'] ?>'s House &gt; Front Door <i>(<?= $house['curbulk'] ?>/<?= $house['maxbulk'] ?>; <?= ceil($house['curbulk'] * 100 / $house['maxbulk']) ?>% full)</i></h4>
<?php
if($message !== false)
  echo $message;

echo '<img src="gfx/npcs/easterbunny.png" align="right" width="128" height="128" alt="(Easter Bunny?)" />';

include 'commons/dialog_open.php';

echo '<p>Chocolate?  Chocolate-chocolate?  Chocolate-chocolate-chocolate?</p>';

include 'commons/dialog_close.php';

if(count($inventory) > 0)
{
?>
     <p>What will you give to the "easter bunny"?  <i>(The quantity shown is the number in your house, not the number you will give; you will only give one.)</i></p>
     <form action="easterbunny.php" method="post">
     <table>
      <tr class="titlerow">
       <th></th>
       <th></th>
       <th>Chocolate</th>
       <th>Quantity</th>
      </tr>
<?php
  $rowstyle = begin_row_class();

  foreach($inventory as $candy)
  {
    $quantity = $candy['quantity'];
    $item = get_item_byname($candy['itemname']);
?>
     <tr class="<?= $rowstyle ?>">
      <td><input type="radio" name="itemname" value="<?= $candy['itemname'] ?>" /></td>
      <td class="centered"><?= item_display_extra($item, '', ($user['inventorylink'] == 'yes')) ?></td>
      <td><?= $candy['itemname'] ?></td>
      <td class="centered"><?= $quantity ?></td>
     </tr>
<?php
    $rowstyle = alt_row_class($rowstyle);
  }
?>
     </table>
     <p><input type="hidden" name="action" value="tot" /><input type="submit" value="Give" /></p>
     </form>
<?php
}
else
  echo '<p><i>You do not have any chocolate to give the "easter bunny."  (You can only give it items from your house; not storage.)</i></p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
