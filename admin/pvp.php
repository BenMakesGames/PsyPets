<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/blimplib.php';

if($admin['clairvoyant'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; PvP</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; PvP Tools</h4>
     <ul class="tabbed">
      <li class="activetab"><a href="adminpvp.php">Part Information</a></li>
     </ul>
     <h5>Chassis</h5>
     <table>
      <tr class="titlerow"><th></th><th>Item</th><th>Weight</th><th>Space</th><th>Bonuses</th><th>Balance Score</th></tr>
<?php
$row_class = begin_row_class();

foreach($chassis as $itemname=>$stats)
{
  $details = get_item_byname($itemname);

  echo '<tr class="' . $row_class . '">' .
       '<td class="centered">' . item_display($details, '') . '</td><td>' . $itemname . '</td>' .
       '<td class="centered">' . $details['weight'] . '</td><td class="centered">' . blimp_size($details['bulk']) . '</td><td>';

  // every 3 weight is a minute you lag; every 10 minutes penalizes each (of 3) stat by -1
  $points = blimp_size($details['bulk']) - (($details['weight'] / 30) * 3);
  
  $bonuses = array();
  foreach($stats as $stat=>$bonus)
  {
    if($bonus > 0)
      $bonuses[] = $stat . ' +' . $bonus;
    else if($bonus < 0)
      $bonuses[] = $stat . ' ' . $bonus;

    if($stat == 'seats')
      $points += $bonus * 10;
    else if($stat == 'propulsion')
      $points += ($bonus / 10) * 3 * 2;
    else
      $points += $bonus * 5;
  }

  echo implode(', ', $bonuses) . '</td><td class="centered">' . number_format($points, 2) . '</td></tr>';

  $row_class = alt_row_class($row_class);
}
?>
     </table>
     <h5>Parts</h5>
     <table>
      <tr class="titlerow"><th></th><th>Item</th><th>Weight</th><th>Size</th><th>Bonuses</th><th>Score</th><td>Efficiency</td></tr>
<?php
$row_class = begin_row_class();

foreach($parts as $itemname=>$stats)
{
  $details = get_item_byname($itemname);

  echo '<tr class="' . $row_class . '">' .
       '<td class="centered">' . item_display($details, '') . '</td><td>' . $itemname . '</td>' .
       '<td class="centered">' . $details['weight'] . '</td><td class="centered">' . $details['bulk'] . '</td><td>';

  $points = -($details['bulk'] + (($details['weight'] / 30) * 3));
  
  $bonuses = array();
  foreach($stats as $stat=>$bonus)
  {
    if($bonus > 0)
      $bonuses[] = $stat . ' +' . $bonus;
    else if($bonus < 0)
      $bonuses[] = $stat . ' ' . $bonus;

    if($stat == 'seats')
      $points += $bonus * 10;
    else if($stat == 'propulsion')
      $points += ($bonus / 10) * 3 * 2;
    else if($stat == 'mana')
      $points += $bonus;
    else if($stat == 'power')
      $points += $bonus / 2;
    else
      $points += $bonus * 5;
  }

  $efficiency = ($points + $details['bulk']) / $details['bulk'];

  echo implode(', ', $bonuses) . '</td><td class="centered">' . number_format($points, 2) . '</td><td>' . number_format($efficiency, 2) . '</td></tr>';

  $row_class = alt_row_class($row_class);
}
?>
     </table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
