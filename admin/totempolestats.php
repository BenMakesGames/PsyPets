<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_login = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/formatting.php';
require_once 'commons/totemlib.php';

if($admin['manageitems'] != 'yes' || $admin['clairvoyant'] != 'yes')
{
  header('Location: /n404/');
  exit();
}

$userid = (int)$_GET['userid'];

$totem_user = get_user_byid($userid, 'display,idnum,user');

$totem_pole = $database->FetchSingle('
  SELECT * FROM psypets_totempoles
  WHERE userid=' . $userid . '
  LIMIT 1
');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Admin Tools &gt; Totem Pole Breakdown</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Totem Pole Breakdown</h4>
<?php
if($totem_user === false)
  echo '<p>There is no such resident.</p>';
else if($totem_pole === false)
  echo '<p>This resident does not have a Totem Pole.</p>';
else
{
  $totems = take_apart(',', $totem_pole['totem']);
  
  if(count($totems) == 0)
    echo '<p>This Totem Pole has no totems on it.</p>';
  else
  {
    foreach($totems as $totem)
      $totem_count[$totem]++;
?>
  <p><?= totem_rating($totem_pole['rating']) ?> (<?= $totem_pole['rating'] ?></p>
  <table>
   <thead>
    <tr><th></th><th>Totem</th><th>Quantity</th></tr>
   </thead>
   <tbody>
<?php
    $rowclass = begin_row_class();

    foreach($totem_count as $totem=>$count)
    {
      $graphic = 'totem_x' . $totem . '.png';
      $item = $database->FetchSingle('SELECT * FROM monster_items WHERE graphic=' . quote_smart($graphic) . ' LIMIT 1');
      
      echo '<tr class="', $rowclass, '"><td class="centered">', item_display($item), '</td><td>', $item['itemname'], '</td><td class="centered">', $count, '</td></tr>';
      
      $rowclass = alt_row_class($rowclass);
    }

    echo '</tbody></table>';
  }
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
