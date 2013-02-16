<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

// DISABLED
// Header("Location: /");

$require_petload = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

if($admin['clairvoyant'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$possible_items = $database->FetchMultiple(('
  SELECT idnum,itemname,graphic,graphictype,value
  FROM monster_items
  WHERE
    can_pawn_with=\'yes\' AND
    noexchange=\'no\'
  ORDER BY RAND()
  LIMIT 10
');

$first_pick = false;
$second_pick = false;

foreach($possible_items as $i=>$item)
{
  $fm_item = $database->FetchSingle('SELECT monster_inventory.forsale AS min_price,monster_users.display AS display FROM monster_inventory JOIN monster_users WHERE monster_inventory.user=monster_users.user AND monster_inventory.forsale>0 AND monster_users.openstore=\'yes\' AND monster_inventory.itemname=' . quote_smart($item['itemname']) . ' ORDER BY min_price ASC LIMIT 1');

  if($fm_item === false)
    $possible_items[$i]['fm_value'] = false;
  else
  {
    $possible_items[$i]['fm_value'] = $fm_item['min_price'];

    if($first_pick === false || $first_pick['value'] > $fm_item['min_price'])
      $first_pick = array('value' => $fm_item['min_price'], 'item' => $item['itemname']);
  }
  
  if($second_pick === false || $second_pick['value'] > $item['value'])
    $second_pick = array('value' => $item['value'], 'item' => $item['itemname']);
}

$pick = ($first_pick === false ? $second_pick['item'] : $first_pick['item']);

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Items Asked For In The Pattern</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Items Asked For In The Pattern</h4>
  <table>
<?php
foreach($possible_items as $item)
{
?>
   <tr>
    <td class="centered"><?= item_display($item) ?></td>
    <td><?= $item['itemname'] ?></td>
    <td class="righted"><?= $item['fm_value'] === false ? 0 : $item['fm_value'] ?>m</td>
   </tr>
<?php
}
?>
  </table>
  <p>First pick: <?= $first_pick === false ? 'none' : $first_pick['item'] ?></p>
  <p>Second pick: <?= $second_pick === false ? 'none' : $second_pick['item'] ?></p>
  <p>FOR-REAL PICK: <?= $pick ?></p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
