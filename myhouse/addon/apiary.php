<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$whereat = 'home';
$wiki = 'Apiary';

$THIS_ROOM = 'Apiary';

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
require_once 'commons/apiarylib.php';

if(!addon_exists($house, 'Apiary'))
{
  header('Location: /myhouse.php');
  exit();
}

$apiary = get_apiary_byuser($user['idnum']);
if($apiary === false)
{
  create_apiary($user['idnum']);

  $apiary = get_apiary_byuser($user['idnum']);
  if($apiary === false)
    die('Error initializing apiary.  How obnoxious.  If the entire site isn\'t down, something else is wrong; please notify an administrator.');
}

$valid_items = array(
  '3-Leaf Clover', '4-Leaf Clover', '5-Leaf Clover',
  'Amethyst Rose', 'Arbutus', 'Black Lotus', 'Narcissus', 'Pansy', 'Periwinkle', 'Primrose', 'Purple Lilac', 'Scabious', 'Yellow Acacia', 'Poinsettia', 'White Lotus',
  'Common Dandelion', 'Honeysuckle', 'White Lily', 'May Flower',
  'Poppy Flower', 'Sesame Flower', 'Hyacinth', 'Cactus Flower', 'Sunflower',
  'Camomile Flowers', 'Fire Spice Flower',
);

if($_GET['action'] == 'harvestall')
{
  $items = array();

  foreach($apiary_harvestables as $item=>$data)
  {
    while($apiary['progress_' . $item] >= $data[2])
    {
      $apiary['progress_' . $item] -= $data[2];
      $updates[$item] = 'progress_' . $item . '=' . $apiary['progress_' . $item];

      $items[$data[0]]++;
    }
  }

  if(count($items) == 0)
    $message = '<p class="failure">But there\'s nothing to harvest...</p>';
  else
  {
    $command = 'UPDATE psypets_apiaries SET ' . implode(', ', $updates) . ' WHERE userid=' . $user['idnum'] . ' LIMIT 1';
    fetch_none($command, 'updating apiary');

    foreach($items as $item=>$quantity)
    {
      if($item == 'Sugar')
        $message .= '<p class="success">You harvested ' . $quantity . ' Honey from your Apiary!</p><p class="failure">Oh, wait, there\'s no such thing as Honey in ' . $SETTINGS['site_name'] . '...</p><p class="success">You harvested ' . $quantity . ' Sugar from your Apiary!</p>';
      else
        $message .= '<p class="success">You harvested ' . $quantity . ' ' . $item . ' from your Apiary!</p>';

      add_inventory_quantity($user['user'], 'u:' . $user['idnum'], $item, 'Harvested from ' . $user['display'] . '\'s Apiary', 'home', $quantity);

      require_once 'commons/statlib.php';
      record_stat($user['idnum'], 'Harvested Something From an Apiary', $quantity);
    }
  }
}
else if(array_key_exists('harvest', $_GET))
{
  if(array_key_exists($_GET['harvest'], $apiary_harvestables))
  {
    $data = $apiary_harvestables[$_GET['harvest']];
    
    if($apiary['progress_' . $_GET['harvest']] >= $data[2])
    {
      $command = 'UPDATE psypets_apiaries SET progress_' . $_GET['harvest'] . '=progress_' . $_GET['harvest'] . '-' . $data[2] . ' WHERE userid=' . $user['idnum'] . ' LIMIT 1';
      fetch_none($command, 'harvesting apiary');

      $apiary['progress_' . $_GET['harvest']] -= $data[2];

      add_inventory($user['user'], 'u:' . $user['idnum'], $data[0], 'Harvested from ' . $user['display'] . '\'s Apiary', 'home');

      if($data[0] == 'Sugar')
        $message = '<p class="success">You harvested Honey from your Apiary!</p><p class="failure">Oh, wait, there\'s no such thing as Honey in ' . $SETTINGS['site_name'] . '...</p><p class="success">You harvested Sugar from your Apiary!</p>';
      else
        $message = '<p class="success">You harvested ' . $data[0] . ' from your Apiary!</p>';

      require_once 'commons/statlib.php';
      record_stat($user['idnum'], 'Harvested Something From an Apiary', 1);
    }
  }
}
else if($_POST['action'] == 'feed' && $now >= $apiary['nextfeeding'])
{
  if(in_array($_POST['item'], $valid_items))
  {
    if(delete_inventory_fromhome($user['user'], $_POST['item'], 1) > 0)
    {
      feed_apiary($apiary, $_POST['item']);

      require_once 'commons/statlib.php';
      record_stat($user['idnum'], 'Fed Something to an Apiary', 1);
    }
    else
    {
      $message = '<p class="failure">You do not have any ' . $_POST['item'] . 's.  Did a pet get to them first?</p>';
    }
  }
}

$command = 'SELECT itemname,COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location LIKE \'home%\' AND location NOT LIKE \'home/$%\' AND itemname IN (\'' . implode('\', \'', $valid_items) . '\') GROUP BY(itemname)';
$clovers = fetch_multiple($command, 'fetching clovers from house');

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Apiary</title>
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
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Apiary (level <?= $apiary['level'] ?>)</h4>
<?php
echo $message;

room_display($house);

$badges = get_badges_byuserid($user['idnum']);

if($apiary['level'] >= 5 && $badges['beekeeper'] == 'no')
{
  set_badge($apiary['userid'], 'beekeeper');
  echo '<p class="successs"><i>(You received the Bee Keeper badge!)</i></p>';
}


if($now > $apiary['nextfeeding'])
{
?>
     <p>You may provide your bees with Clovers in order to get Sugar, Wax, and other things.</p>
     <p><i>(The quantity listed is the number available in your house, not the number you will feed.  You will always only give the Apiary 1 item per feeding.)</i></p>
<?php
  if(count($clovers) > 0)
  {
?>
     <form method="post">
     <table>
      <tr class="titlerow">
       <th></th>
       <th></th>
       <th>Clover</th>
       <th>Quantity</th>
      </tr>
<?php
  $rowstyle = begin_row_class();
  foreach($clovers as $clover)
  {
    $item = get_item_byname($clover['itemname']);
?>
     <tr class="<?= $rowstyle ?>">
      <td><input type="radio" name="item" value="<?= $item['itemname'] ?>" /></td>
      <td class="centered"><?= item_display_extra($item, '', ($user['inventorylink'] == 'yes')) ?></td>
      <td><?= $clover['itemname'] ?></td>
      <td class="centered"><?= $clover['c'] ?></td>
     </tr>
<?php
    $rowstyle = alt_row_class($rowstyle);
  }
?>
     </table>
     <p><input type="hidden" name="action" value="feed" /><input type="submit" value="Feed" /></p>
     </form>
<?php
  }
  else
    echo '<p>You have no Clovers in your house.  (Clovers in protected rooms are not listed here.)</p>';
}
else
{
  echo '<p>It is too soon to feed your bees again.</p>';
  if($badges['beekeeper'] == 'yes')
    echo '<p>Based on your experience as a Bee Keeper, you estimate you\'ll have to wait ' . duration($apiary['nextfeeding'] - $now, 2) . '.</p>';
}

$rowclass = begin_row_class();
?>
<h5>Production Progress</h5>
<table>
 <tr class="titlerow">
  <th></th>
  <th>Item</th>
  <th>Progress</th>
  <th></th>
 </tr>
<?php
if($apiary['level'] < 10)
{
?>
 <tr class="<?= $rowclass ?>">
  <td class="centered"><img src="/gfx/homeimprovement.png" /></td>
  <td>Level <?= $apiary['level'] + 1 ?> Apiary</td>
  <td><?php if($apiary['experience'] > 0) { ?><div class="progressbar" onmouseover="Tip('<?= floor($apiary['experience'] * 100 / apairy_exp_needed($apiary['level'])) ?>%');"><img src="/gfx/red_shim.gif" height="12" width="<?= ceil($apiary['experience'] * 50 / apairy_exp_needed($apiary['level'])) ?>" alt="" /></div><?php } else echo '<i class="dim">no progress</i>'; ?></td>
  <td></td>
 </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}

foreach($apiary_harvestables as $row=>$data)
{
  if($apiary['level'] >= $data[1])
  {
    if($data[0] == 'Sugar')
    {
      $data[0] = 'Honey';
      $details['itemname'] = 'Sugar';
      $details['graphictype'] = 'bitmap';
      $details['graphic'] = 'honey.png';
    }
    else
      $details = get_item_byname($data[0]);
?>
 <tr class="<?= $rowclass ?>">
  <td class="centered"><?= item_display($details, '') ?></td>
  <td><?= $data[0] ?></td>
  <td><?php if($apiary['progress_' . $row] > 0) { ?><div class="progressbar" onmouseover="Tip('<?= floor($apiary['progress_' . $row] * 100 / $data[2]) ?>%');"><img src="/gfx/red_shim.gif" height="12" width="<?= min(50, ceil($apiary['progress_' . $row] * 50 / $data[2])) ?>" alt="" /></div><?php } else echo '<i class="dim">no progress</i>'; ?></td>
  <td><?php
    if($apiary['progress_' . $row] >= $data[2])
    {
      echo '<a href="/myhouse/addon/apiary.php?harvest=' . $row . '">harvest</a>';
      $harvest_all = true;
    }
?></td>
 </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
}

echo '</table>';

if($harvest_all)
  echo '<ul><li><a href="/myhouse/addon/apiary.php?action=harvestall">Harvest all</a></li></ul>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
