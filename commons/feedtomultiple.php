<?php
// included by moveinventory2.php...

require_once 'commons/petblurb.php';
require_once 'commons/love.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/rpgfunctions.php';

load_user_pets($user, $userpets);

if(count($userpets) == 0)
{
  header('Location: /myhouse.php');
  exit();
}

$food_list = array();

foreach($items as $id)
{
  $item = get_inventory_byid($id);
  $this_item = get_item_byname($item['itemname']);

  if($this_item['is_edible'] == 'yes')
    $food_list[] = array($item, $this_item);
}

function feed_to_sort($i1, $i2)
{
  $v1 = strtolower($i1[0]['itemname']);
  $v2 = strtolower($i2[0]['itemname']);

  if($v1 > $v2)
    return 1;
  else if($v1 < $v2)
    return -1;
  else
    return 0;
}

uasort($food_list, 'feed_to_sort');

if(count($food_list) == 0)
{
  header('Location: ./myhouse.php?msg=111');
  exit();
}

// already have $house from moveinventory2.php

if(strlen($house['curroom']) > 0)
{
  $room = 'home/' . $house['curroom'];

  if($house['curroom']{0} == '$')
    $sayroom = substr($house['curroom'], 1);
  else
    $sayroom = $house['curroom'];

  $THIS_ROOM = $house['curroom'];
}
else
{
  $room = 'home';
  $sayroom = 'Common';
  $THIS_ROOM = 'Common';
}

$total_size = 0;

if($house['curroom'] == '')
{
  $offset = 0;
}
else
{
  $wall_rooms = explode(',', $house['rooms']);
  $offset = array_search($house['curroom'], $wall_rooms) + 1;
}

$walls = explode(',', $house['wallpapers']);
$wallpaper = $walls[$offset];

if($wallpaper != 'none')
  $CONTENT_STYLE = "background: #fff url(//" . $SETTINGS['site_domain'] . "/gfx/walls/$wallpaper.png) repeat;";

$effective_max_bulk = min(max_house_size(), $house['maxbulk']);
if($effective_max_bulk < $house['maxbulk'])
  $house_note = '<a href="/realestate.php">*</a>';
else
  $house_note = '';

$max_pets = max_active_pets($user, $house);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; <?= $sayroom ?> Room</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <div class="shadowed-box" id="kitchen" style="display: none;"><div><center>
      <br /><br /><img src="/gfx/throbber.gif" width="16" height="16" /><br /><br /><br />
     </center></div></div>
<?php
if($its_your_birthday)
{
?>
<div style="background:url('/gfx/streamers_yellow.png'); height:48px; font-size:48px;"><center><img src="gfx/happy_birthday.png" width="450" height="48" /></center></div>
<?php
}
?>
<h4><?= $user['display'] ?>'s House &gt; <?= $sayroom ?> Room <i>(<?= ($house['curbulk'] / 10) ?>/<?= ($effective_max_bulk / 10) . $house_note ?>; <?= ceil($house['curbulk'] * 100 / $effective_max_bulk) ?>% full <a href="/housesummary.php"><img src="/gfx/summary.png" width="18" height="16" alt="(summary)" border="0" /></a>)</i></h4>
<?php
if($now >= $user['tot_time'] && date('M d') == 'Oct 31')
  echo '<ul><li><a href="/trickortreat.php">*knock, knock, knock*</a></li></ul>';

$half_hour_ready = 0;
?>
<ul class="inventory pets">
<?php
  $colstart = begin_row_class();
  $pet_count = 0;
  $maxpets = count($userpets);

  foreach($userpets as $petnum=>$pet)
  {
    echo '<li>';

    if(pet_blurb($user, $house, $petnum, $maxpets, $pet, false))
      $half_hour_ready++;

    echo '</li>';

    $pet_count++;
    
    $pet_max_food[$pet['idnum']] = max_food($pet);

    if($pet['food'] <= 0)
      $food_state[$pet['idnum']] = 'is starving';
    else if($pet['food'] <= 3)
      $food_state[$pet['idnum']] = 'is very hungry';
    else if($pet['food'] <= 6)
      $food_state[$pet['idnum']] = 'is hungry';
    else if($pet['food'] >= max_food($pet) * .9)
      $food_state[$pet['idnum']] = 'is stuffed';
    else if($pet['food'] >= max_food($pet) * .75)
      $food_state[$pet['idnum']] = 'is full';
    else
      $food_state[$pet['idnum']] = '...';
  }
?>
</ul>
<div class="endinventory"></div>
<h5>Feed to Multiple...</h5>
<form action="/feedmultiple.php" method="post">
<table>
 <tr class="titlerow">
  <th></th>
  <th>Item</th>
  <th>Maker</th>
  <th>Feed to</th>
 </tr>
<?php
$rowclass = begin_row_class();

foreach($food_list as $pair)
{
  $item = $pair[0];
  $details = $pair[1];

  $itemmaker = item_maker_display($item['creator']);
?>
 <tr class="<?= $rowclass ?>">
  <td class="centered"><?= item_display($details, '') ?></td>
  <td><?= $item['itemname'] ?></td>
  <td><?= $itemmaker ?></td>
  <td>
   <select name="i<?= $item['idnum'] ?>">
    <option value="0">none</option>
<?php
  foreach($userpets as $pet)
  {
    if($pet['sleeping'] == 'yes' || $pet['dead'] != 'no')
      continue;
  
    if($details['ediblefood'] > 0)
    {
      $ratio = $pet_max_food[$pet['idnum']] / $details['ediblefood'];

      if($ratio < .80)
        $food_size = 'too much';
      else if($ratio < 1.5)
        $food_size = 'a full meal';
      else if($ratio < 3)
        $food_size = 'a light meal';
      else
        $food_size = 'a snack';
    }
    else
      $food_size = 'unfilling';
?>
    <option value="<?= $pet['idnum'] ?>"><?= $pet['petname'] ?> (<?= $food_state[$pet['idnum']] . '; ' . $food_size ?>)</option>
<?php
  }
?>
   </select>
  </td>
 </tr>
<?php

  $rowclass = alt_row_class($rowclass);
}
?>
</table>
<p><input type="submit" value="Go" style="width: 100px;" /></p>
</form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
