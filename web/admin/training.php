<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

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

$TRAINING_STATS = array(
  'str',
  'dex',
  'sta',
  'per',
  'int',
  'wit',
  'bra',
  'athletics',
  'stealth',
  'sur',
  'gathering',
  'fishing',
  'mining',
  'cra',
  'painting',
  'carpentry',
  'jeweling',
  'sculpting',
  'eng',
  'mechanics',
  'chemistry',
  'smi',
  'tai',
  'leather',
  'binding',
  'pil'
);

$stat = $_GET['stat'];

if(!in_array($stat, $TRAINING_STATS))
  $stat = reset($TRAINING_STATS);

$command = 'SELECT * FROM monster_items WHERE custom!=\'yes\' AND playstat=' . quote_smart($stat) . ' ORDER BY itemname ASC';
$items = $database->FetchMultiple($command, 'fetching items');

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Training Items by Stat</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Training Items by Stat</h4>
<ul class="tabbed">
<?php
foreach($TRAINING_STATS as $this_stat)
{
  if($this_stat == $stat)
    echo '<li class="activetab">';
  else
    echo '<li>';

  echo '<a href="/admin/training.php?stat=' . $this_stat . '">' . $this_stat . '</a></li> ';
}
?>
</ul>
<div style="overflow:auto; margin-bottom: 1em;">
<table class="nomargin">
<thead>
 <tr class="titlerow">
  <th></th><th>Item</th><th>Play Description</th><th>Note</th>
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
     <td>"' . $this_item['playdesc'] . '"</td>
     <td>' . ($this_item['custom'] != 'no' ? $this_item['custom'] : '') . '</td>
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
