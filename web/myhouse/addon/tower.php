<?php
require_once 'commons/init.php';

$whereat = "home";
$wiki = "Tower#Balcony";
$require_petload = 'no';

$THIS_ROOM = 'Tower';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/towerlib.php';

if(!addon_exists($house, 'Tower'))
{
  header('Location: /myhouse.php');
  exit();
}

$tower = get_tower_byuser($user['idnum']);
if($tower === false)
{
  create_tower($user['idnum']);
  $tower = array('userid' => $user['idnum'], 'nextsearch' => 0);
}

// GENERATE MONKEY DATA
if($tower['monkeyname'] == '')
{
  $defects = array('color-blind', 'game-legged', 'one-eyed', 'spaced-out',
    'bald', 'wizened', 'decrepit', 'witless', 'hump-backed', 'drooling',
    'ungainly');

  $i = array_rand($defects, 2);
  $monkey_defect_1 = $defects[$i[0]];
  $monkey_defect_2 = $defects[$i[1]];

  $monkey_name = random_name('male');
  
  fetch_none('
    UPDATE psypets_towers
    SET
      monkeyname=' . quote_smart($monkey_name) . ',
      monkeydesc=' . quote_smart($monkey_defect_1 . ', ' . $monkey_defect_2) . '
    WHERE userid=' . $user['idnum'] . '
    LIMIT 1
  ');

  $tower['monkeyname'] = $monkey_name;
  $tower['monkeydesc'] = $monkey_defect_1 . ', ' . $monkey_defect_2;
  
  $message .= '<p class="failure">A passing monkey, seeing your tower unoccupied, swoops in and takes up residence regardless of your will.</p>';
}

// ---

$verbs = array(
  'losing a game of tic-tac-toe against ' . $userpets[array_rand($userpets)]['petname'],
  'just standing here', 'admiring himself in a mirror', 'picking at his teeth with a Talon',
  'stuck in a chinese finger trap', 'stalking the drifters in his eyes', 'trying his best to look innocent',
  'losing a thumb-wrestling match against ' . $userpets[array_rand($userpets)]['petname'],
  'chasing his own tail', 'gnawing his left arm', 'staring vacantly at an empty wall'
);

$inventory = get_houseinventory_byuser_forpets($user["user"]);

$foodstuffs = array();

foreach($inventory as $item)
{
  $details = get_item_byname($item['itemname']);

  if(substr($details['itemtype'], 0, 4) == 'food' && $details['ediblefood'] > 7)
    $foodstuffs[$item['itemname']]++;
}

if($_POST['action'] == 'fly')
{
  if($now > $tower['nextsearch'])
  {
    $details = get_item_byname($_POST['itemname']);

    if(substr($details['itemtype'], 0, 4) == 'food' && $details['ediblefood'] > 7)
    {
      $success = successes($details['ediblefood']);
      $fed_monkey = true;

      $items_deleted = delete_inventory_fromhome($user['user'], $details['itemname'], 1);
      
      if($items_deleted == 0)
      {
        $message = '<p>You don\'t have that item.  Maybe a pet ate it, or you moved the item from another window or tab?</p>';
        $fed_monkey = false;
      }
      else if($success <= 7)
      {
        $message = '<p class="failure">' . $tower['monkeyname'] . ' comes back with a ' . $inventory[array_rand($inventory)]['itemname'] . ' that he found... in your house.  You take it from him and put it back where it came from.</p>';
        add_monkey_log($user['idnum'], $tower['monkeyname'], $details['itemname'], '');
      }
      else
      {
        if($success >= 8 && $success <= 10)
          $items = array('Copper', 'Carrot', 'White Cloth', 'Paper Hat');
        else if($success >= 11 && $success <= 13)
          $items = array('Coconut Milk', 'Coal', 'Paper Airplane');
        else if($success >= 14 && $success <= 16)
          $items = array('Carrot', 'Carrot', 'Venom', 'Feather', 'Ether Condensate', 'Wood');
        else if($success >= 17 && $success <= 19)
          $items = array('Blood', 'Venom', 'Blood', 'Talon', 'Ether Condensate');
        else if($success >= 20)
          $items = array('Abraxas Stone');

        if(mt_rand(1, 1000 - $details['ediblefood'] * 2) == 1)
          $items = array('Wand of Polymorph Self');
        else if(mt_rand(1, 20) == 1)
          $items = array('Grinning Totem');

        $item = $items[array_rand($items)];

        add_inventory($user['user'], '', $item, 'Fetched by ' . $user['display'] . '\'s flying monkey, ' . $tower['monkeyname'], 'home');
        
        add_monkey_log($user['idnum'], $tower['monkeyname'], $details['itemname'], $item);
        
        $place = array('stuck to the bottom of a balcony', 'on the other side of a signboard', 'amidst some trestlework', 'in a fountain', 'in an arcade', 'on an altar', 'in the back of a hangar', 'behind a partition');

        $message = '<p class="success">' . $tower['monkeyname'] . ' pockets the ' . $details['itemname'] . ' and flies off, returning a short while later with something he found ' . $place[array_rand($place)] . '.  You cautiously take what reveals itself to be a ' . $item . ' and deposit it in your house.</p>';
      }

      if($fed_monkey)
      {
        $tower['nextsearch'] = $now + (mt_rand(25, 35) * 60);
        $tower['lastfood'] = $details['itemname'];
      
        $command = 'UPDATE psypets_towers SET nextsearch=' . $tower['nextsearch'] . ',lastfood=' . quote_smart($details['itemname']) . ' WHERE userid=' . $tower['userid'] . ' LIMIT 1';
        fetch_none($command, 'addon_tower.php');
      }
    }
    else
      $message = '<p><span class="failure">Eh?  What food item?</span></p>';
  }
  else
    $message = '<p><span class="failure">' . $tower['monkeyname'] . ' is not in the mood to go flying anywhere right now.</span></p>';
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Tower &gt; Balcony</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Tower &gt; Balcony</h4>
<?php
room_display($house);
?>
<ul class="tabbed">
 <li class="activetab"><a href="/myhouse/addon/tower.php">Balcony</a></li>
 <li><a href="/myhouse/addon/tower_laboratory.php">Laboratory</a></li>
<?php
if($tower['bell'] == 'yes')
  echo '<li><a href="/myhouse/addon/tower_bell.php">Bell Tower</a></li>';
?>
</ul>
<?= $message ?>
     <h5><?= $tower['monkeyname'] ?> the Tower Monkey</h5>
<?php
if($now > $tower['nextsearch'])
{
?>
     <p><?= $tower['monkeyname'] ?>, a flying, <?= $tower['monkeydesc'] ?> monkey, is <?= $verbs[array_rand($verbs)] ?>.</p>
     <p>With a bit of food, you could probably convince him to go pick up some alchemy equipment for you.</p>
<?php
  if(count($foodstuffs) > 0)
  {
?>
     <p><i>(<?= $tower['monkeyname'] ?> will not eat unfilling foods, therefore not all the food in your house is listed here.  Also, the quantity listed is the number available in your house, not the number you will feed.  You will always only give <?= $tower['monkeyname'] ?> 1 piece of food.)</i></p>
     <form method="post">
     <table>
      <tr class="titlerow">
       <th></th>
       <th></th>
       <th>Food</th>
       <th>Quantity</th>
      </tr>
<?php
    $rowstyle = begin_row_class();

    foreach($foodstuffs as $food=>$quantity)
    {
      $item = get_item_byname($food);
?>
     <tr class="<?= $rowstyle ?>">
      <td><input type="radio" name="itemname" value="<?= $food ?>" /></td>
      <td class="centered"><?= item_display_extra($item) ?></td>
      <td><?= $food ?></td>
      <td class="centered"><?= $quantity ?></td>
     </tr>
<?php
      $rowstyle = alt_row_class($rowstyle);
    }
?>
     </table>
     <p><input type="hidden" name="action" value="fly" /><input type="submit" value="Fly, my pretty!" class="bigbutton" /></p>
     </form>
<?php
  }
  else
    echo '<p><i>(' . $tower['monkeyname'] . ' will not eat unfilling foods.  You need to stock your house with something more filling to feed him with.)</i></p>';
}
else
  echo '<p>' . $tower['monkeyname'] . ' is still savoring the ' . $tower['lastfood'] . ' you gave him, and refuses to fly anywhere while he digests.</p>';

$monkey_logs = get_monkey_logs($user['idnum']);

if(count($monkey_logs) > 0)
{
?>
<h5>Monkey Logs</h5>
<table>
 <tr class="titlerow">
  <th>Timestamp</th>
  <th>Log</th>
 </tr>
<?php
  $rowclass = begin_row_class();

  foreach($monkey_logs as $log)
  {
    if($log['prize'] == '')
      $span = '<span class="failure">';
    else
      $span = '<span class="success">';
?>
 <tr class="<?= $rowclass ?>">
  <td><?= Duration($now - $log['timestamp'], 2) ?> ago</td>
  <td><?= $span ?>You fed <?= $log['food'] ?> to <?= $log['monkeyname'] ?>, and got <?= $log['prize'] == '' ? 'nothing' : $log['prize'] ?>.</span></td>
 </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
</table>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
