<?php
/* to find costumed pets:

SELECT * FROM `monster_pets`
WHERE (SELECT itemtype FROM monster_items WHERE itemname=
  (SELECT itemname FROM monster_inventory WHERE idnum=monster_pets.toolid))
  LIKE 'clothing/costume%'
*/

$whereat = 'home';
$require_petload = 'no';

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
require_once 'commons/statlib.php';

if(($now_month != 10 || $now_day < 29) && ($now_month != 11 || $now_day > 1))
{
  header('Location: /');
  exit();
}

if($now < $user['tot_time'])
{
  header('Location: /');
  exit();
}

if($user['tot'] == 0 || $_GET['getnew'] == 'yes')
{
  $three_days_ago = $now - (60 * 60 * 24 * 3);

  $command = 'SELECT a.idnum FROM `monster_pets` AS a,monster_inventory AS b WHERE a.costumed=\'yes\' AND a.last_love>' . $three_days_ago . ' AND a.location=\'home\' AND a.toolid=b.idnum AND a.user!=' . quote_smart($user['user']);
  $pets = $database->FetchMultiple($command, 'fetching trick or treating pets');

  $pet = $pets[array_rand($pets)];
  
  $user['tot'] = $pet['idnum'];

  $command = 'UPDATE monster_users SET tot=' . $user['tot'] . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'trick or treating');
}

$tot = get_pet_byid($user['tot']);
$tool = get_inventory_byid($tot['toolid']);
$tool_details = get_item_byname($tool['itemname']);

if($_POST['action'] == 'tot')
{
  $consumed = delete_inventory_fromhome($user['user'], $_POST['itemname'], 1);

  if($_POST['itemname'] == 'Blue Lollipop' || $_POST['itemname'] == 'Green Lollipop' || $_POST['itemname'] == 'Red Lollipop')
    $newitem = 'Classic LOL-lipop';
  else
    $newitem = $_POST['itemname'];

  if($consumed > 0)
  {
    add_inventory($tot['user'], 'p:' . $tot['idnum'], $newitem, 'Collected by ' . $tot['petname'], 'storage/incoming');
    flag_new_incoming_items($tot['user']);

    //if(time('Y') == 2011)
      $user['tot_time'] = $now + mt_rand(5 * 60, 15 * 60);
/*    else
      $user['tot_time'] = $now + mt_rand(10 * 60, 30 * 60);*/

    $user['tot_done']++;

    $command = 'UPDATE monster_users SET tot_done=tot_done+1,tot=0,tot_time=' . $user['tot_time'] . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'trick or treating');

    if($user['tot_done'] % 10 == 0)
    {
      $msg = 72;

      $items = array('Skeleton Key Blade', 'Poisonous Mushroom', 'Gossamer', 'Dark Gossamer', 'Jack-o-Lantern', 'Dark Matter', 'Wand of Wonder', 'Deck of Many Things', 'Eye Patch', 'Royal Jelly');

      $item = $items[floor($user['tot_done'] / 10 - 1) % count($items)];

      add_inventory($user['user'], 'p:' . $tot['idnum'], $item, 'Given to you by ' . $tot['petname'], 'storage/incoming');
      flag_new_incoming_items($user['user']);
    }
    else
      $msg = 71;

    record_stat($user['idnum'], 'Candy Given to Trick-or-Treaters', 1);

    header('Location: /incoming.php?msg=' . $msg . ':' . $tot['petname']);
    exit();
  }
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
    $CONTENT_STYLE = 'background: #fff url(/gfx/postwalls/' . $POST_BACKGROUNDS[$wallpaper] . '.png) repeat;';
  else
    $CONTENT_STYLE = 'background: #fff url(/gfx/walls/' . $wallpaper . '.png) repeat;';
}


$inventory = get_houseinventory_byuser_forpets($user['user']);

$candies = array();

foreach($inventory as $item)
{
  $details = get_item_byname($item['itemname']);
  if(substr($details['itemtype'], 0, 10) == 'food/candy')
    $candies[$item['itemname']]++;
}

asort($candies);

if($user['tot_done'] == 0)
  $tot_message = '<p>You have not given candy to any trick-or-treaters.</p>';
else if($user['tot_done'] == 1)
  $tot_message = '<p>You have given candy to one trick-or-treater.</p>';
else
  $tot_message = '<p>You have given candy to ' . $user['tot_done'] . ' trick-or-treaters.</p>';

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
  echo '<div style="background: url(\'gfx/streamers_yellow.png\'); height: 48px; font-size: 48px;"><center><img src="gfx/happy_birthday.png" width="450" height="48" alt="Happy Birthday!" /></center></div>';
?>
<h4><?= $user['display'] ?>'s House &gt; Front Door <i>(<?= $house['curbulk'] ?>/<?= $house['maxbulk'] ?>; <?= ceil($house['curbulk'] * 100 / $house['maxbulk']) ?>% full)</i></h4>
<table>
 <tr>
  <td><?= item_display_extra($tool_details) ?></td>
  <td><img src="gfx/pets/<?= $tot['graphic'] ?>" /></td>
 </tr>
</table>
     <p><a href="/petprofile.php?petid=<?= $user['tot'] ?>"><?= $tot['petname'] ?></a> is trick-or-treating at your door, wearing a <a href="encyclopedia2.php?i=<?= $tool_details['idnum'] ?>"><?= $tool['itemname'] ?></a>...</p>
<?php
echo $tot_message;

if(count($candies) > 0)
{
?>
     <p>What will you give to the <?= $tool['itemname'] ?>d pet?  <i>(The quantity shown is the number in your house, not the number you will give; you will only give one.)</i></p>
     <form action="trickortreat.php" method="post">
     <table>
      <tr class="titlerow">
       <th></th>
       <th></th>
       <th>Candy</th>
       <th>Quantity</th>
      </tr>
<?php
  $rowstyle = begin_row_class();

  foreach($candies as $candy=>$quantity)
  {
    $item = get_item_byname($candy);
?>
     <tr class="<?= $rowstyle ?>">
      <td><input type="radio" name="itemname" value="<?= $candy ?>" /></td>
      <td class="centered"><?= item_display_extra($item, '', ($user['inventorylink'] == 'yes')) ?></td>
      <td><?= $candy ?></td>
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
  echo '<p><i>You do not have any candy to give ' . ($tot['gender'] == 'male' ? 'him' : 'her') . '.</i></p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
