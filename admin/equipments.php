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
require_once 'commons/equiplib.php';

if($admin['clairvoyant'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$stat = $_GET['stat'];

if(!in_array($stat, $EQUIP_FIELDS))
  $stat = $EQUIP_FIELDS[array_rand($EQUIP_FIELDS)];

$command = 'SELECT * FROM monster_items WHERE custom=\'no\' AND equip_' . $stat . '>0 ORDER BY equip_' . $stat . ' DESC';
$items = $database->FetchMultiple(($command, 'fetching items');

foreach($items as $item)
  $plusses[$item['equip_' . $stat]]++;

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Equipment by Stat</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Equipment by Stat</h4>
<ul class="tabbed">
<?php
foreach($EQUIP_FIELDS as $this_stat)
{
  if($this_stat == $stat)
    echo '<li class="activetab">';
  else
    echo '<li>';

  echo '<a href="/admin/equipments.php?stat=' . $this_stat . '">' . $this_stat . '</a></li> ';
}
?>
</ul>
<h5>Let Me Break It Down For You</h5>
<p><?= count($items) ?> equipment that boost <?= $stat ?>.</p>
<ul>
<?php
foreach($plusses as $plus=>$quantity)
  echo '<li>' . ($plus >= 0 ? '+' . $plus : $plus) . ' &times;' . $quantity . '</li>';
?>
</ul>
<h5>Details</h5>
<div style="overflow:auto; margin-bottom: 1em;">
<table class="nomargin">
<thead>
 <tr class="titlerow">
  <th></th><th>Item</th>
<?php
foreach($EQUIP_FIELDS as $this_stat)
  echo '<th>' . $this_stat . '</th>';
?>
 </tr>
</thead>
<?php
$rowclass = begin_row_class();

foreach($items as $this_item)
{
  echo '
    <tr class="' . $rowclass . '">
     <td class="centered">' . item_display($this_item) . '</td>
     <td>' . $this_item['itemname'] . '</td>
  ';

  foreach($EQUIP_FIELDS as $this_stat)
  {
    $bonus = $this_item['equip_' . $this_stat];
    echo '<td class="centered">' . ($bonus < 0 ? $bonus : ($bonus > 0 ? '+' . $bonus : '&mdash;')) . '</td>';
  }

  echo '
    </tr>
  ';
  
  $rowclass = alt_row_class($rowclass);
}
?>
</table>
</div>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
