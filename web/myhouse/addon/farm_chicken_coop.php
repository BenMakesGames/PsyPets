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

if($farm['coop_has_timer'] == 'yes')
  $minutes = 15;
else
  $minutes = 20;

$next_feeding = $farm['coop_feed_time'] + $minutes * 60;

if($now >= $next_feeding)
{
	$chicken_feed = $database->FetchMultipleBy('
    SELECT COUNT(idnum) AS qty,itemname
    FROM monster_inventory
    WHERE
      user=' . $database->Quote($user['user']) . '
      AND itemname ' . $database->In(array_keys($ALLOWED_CHICKEN_FEED)) . '
      AND location LIKE \'home%\'
			AND location NOT LIKE \'home/$%\'
    GROUP BY itemname
    ORDER BY itemname ASC
  ', 'itemname');

  if($_POST['action'] == 'Feed')
  {
    $itemname = $_POST['feed'];

    if(!array_key_exists($itemname, $ALLOWED_CHICKEN_FEED))
      $message = '<p class="failure">You forgot to pick an item!</p>';
    else if(array_key_exists($itemname, $chicken_feed))
    {
      $deleted = delete_inventory_fromhome($user['user'], $itemname, 1);

      if($deleted > 0)
      {
        feed_chickens_at_farm($farm, $itemname);
        $message = '<p class="success">The chickens greedily devour the ' . $itemname . '.</p>';
        $next_feeding = $farm['coop_feed_time'] + $minutes * 60;
      }
      else
        $message = '<p class="failure">The selected item does not exist!  It must have been moved or used up.</p>';
    }
    else
      $message = '<p class="failure">The selected item does not exist.  It must have been moved or used up.</p>';
  }
}

if($_GET['action'] == 'collect')
{
  $item = $_GET['what'];
  
  if(array_key_exists($item, $chicken_coop_harvestables))
  {
    $itemname = $chicken_coop_harvestables[$item][0];
    $required = $chicken_coop_harvestables[$item][1];

    $progress = $farm['coop_' . $item];

    $quantity = (int)($progress / $required);
    
    if($quantity > 0)
    {
      collect_from_chickens_at_farm($farm, $item, $quantity * $required);

      add_inventory_quantity($user['user'], '', $itemname, 'Collected from ' . $user['display'] . '\'s Chicken Coop', 'home', $quantity);
      
      $message = '<p class="success">IT IS DONE!  You\'ll find it in <a href="/houseaction.php?room=Common">your Common room</a>.</p>';
    }
    else
      $message = '<p class="failure">There are none to collect!</p>';
  }
  else
    $message = '<p class="failure">Oops!  What did you want to collect?</p>';
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Farm &gt; Chicken Coop</title>
<?php include 'commons/head.php'; ?>
  <style type="text/css">
   .progressbar
   {
     width: 50px;
     padding: 4px;
     border: 1px solid #000;
   }
  </style>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Farm &gt; Chicken Coop</h4>
<?php
room_display($house);
?>
<ul class="tabbed">
 <li><a href="/myhouse/addon/farm.php">Fields &amp; Silo</a></li>
 <li class="activetab"><a href="/myhouse/addon/farm_chicken_coop.php">Chicken Coop</a></li>
</ul>
<?php
echo $message;

echo '<h5>Feed Chickens</h5>';

if($now >= $next_feeding)
{
  if(count($chicken_feed) > 0)
  {
    echo '
      <p>Chickens will eat all manner of seeds.  What would you like to feed them?</p>
      <p><i>(The quantity given is the number you have at home, not the number you will feed.  You will only feed 1 of the selected item to the chickens.)</i></p>
      <form method="post">
      <table>
       <thead>
        <tr class="titlerow"><th></th><th></th><th>Item</th><th>Qty.</th></tr>
       </thead>
       <tbody>
    ';

    $rowclass = begin_row_class();
    foreach($chicken_feed as $feed)
    {
      $details = get_item_byname($feed['itemname']);
      echo '
        <tr class="' . $rowclass . '">
         <td><input type="radio" name="feed" value="' . $feed['itemname'] . '" /></td>
         <td class="centered">' . item_display($details) . '</td>
         <td>' . $feed['itemname'] . '</td>
         <td class="centered">' . $feed['qty'] . '</td>
        </tr>
      ';
    
      $rowclass = alt_row_class($rowclass);
    }

    echo '
       </tbody>
      </table>
      <p><input type="submit" name="action" value="Feed" /></p>
      </form>
    ';
  }
  else
    echo '<p>The chickens look at you expectantly.  Unfortunately, you don\'t have anything to give them right now...</p>';
}
else if($farm['coop_has_timer'] == 'yes')
  echo '
    <p>The chickens aren\'t interested in food right now.  They just ate.</p>
    <p>According to the Sour Lime-powered Clock, you fed them ' . duration($now - $farm['coop_feed_time'], 2) . ' ago.  (They can be fed every ' . $minutes . ' minutes.)</p>
  ';
else
  echo '<p>The chickens aren\'t interested in food right now.  They just ate.</p>';

if($farm['coop_has_timer'] == 'no')
  echo '<p>It\'s too bad you don\'t have a timer of some kind.  It\'d probably make feeding them more efficient...</p>';
?>
<h5>Production</h5>
<table>
 <tr class="titlerow">
  <th></th>
  <th>Item</th>
  <th>Progress</th>
  <th></th>
 </tr>
<?php
$rowclass = begin_row_class();

foreach($chicken_coop_harvestables as $row=>$data)
{
  $details = get_item_byname($data[0]);
  
  if($data[2] === true || $farm['coop_' . $row] > 0)
  {
?>
 <tr class="<?= $rowclass ?>">
  <td class="centered"><?= item_display($details, '') ?></td>
  <td><?= $data[0] ?></td>
  <td><?php if($farm['coop_' . $row] > 0) { ?><div class="progressbar" onmouseover="Tip('<?= floor($farm['coop_' . $row] * 100 / $data[1]) ?>%');"><img src="/gfx/red_shim.gif" height="12" width="<?= min(50, ceil($farm['coop_' . $row] * 50 / $data[1])) ?>" alt="" /></div><?php } else echo '<i class="dim">no progress</i>'; ?></td>
  <td><?php
    if($farm['coop_' . $row] >= $data[1])
    {
      $number_harvested = (int)($farm['coop_' . $row] / $data[1]);

      if($number_harvested >= 2)
        echo '<a href="/myhouse/addon/farm_chicken_coop.php?action=collect&what=' . $row . '">Collect &times;' . $number_harvested . '</a>';
      else
        echo '<a href="/myhouse/addon/farm_chicken_coop.php?action=collect&what=' . $row . '">Collect</a>';
    }
    
    echo '</td></tr>';

    $rowclass = alt_row_class($rowclass);
  }
}

echo '</table>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
