<?php
require_once 'commons/init.php';

$require_petload = 'no';
$wiki = 'My_Statistics';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';

$sort = array(
  1 => '<a href="?sort=1">&#9651;</a>',
  2 => '<a href="?sort=2">&#9661;</a>',
  3 => '<a href="?sort=3">&#9661;</a>'
);

switch($_GET['sort'])
{
  case 1:
    $order = 'stat ASC';
    $sort[1] = '&#9650;';
    break;

  case 2:
    $order = 'value DESC,stat ASC';
    $sort[2] = '&#9660;';
    break;

  default:
    $order = 'lastupdate DESC,stat ASC';
    $sort[3] = '&#9660;';
    break;
}

$command = 'SELECT stat,value,lastupdate FROM psypets_player_stats WHERE userid=' . $user['idnum'] . ' ORDER BY ' . $order;
$stats = fetch_multiple($command, 'fetching player stats');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Account &gt; My Statistics</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myaccount/">My Account</a> &gt; My Statistics</h4>
     <p>Countless, pointless (well, mostly-pointless) statistics gathered for your viewing pleasure...</p>
     <table>
      <thead>
       <tr class="titlerow">
        <th><nobr>Statistic <?= $sort[1] ?></nobr></th><th class="righted"><nobr>Value <?= $sort[2] ?></nobr></th><th class="centered"><nobr>Last Update <?= $sort[3] ?></nobr></th>
       </tr>
      </thead>
      <tbody>
<?php
$rowclass = begin_row_class();

foreach($stats as $stat)
{
  echo '
    <tr class="' . $rowclass . '">
     <td>' . $stat['stat'] . '</td>
     <td class="righted">' . $stat['value'] . '</td>
     <td class="centered">' . ($stat['lastupdate'] == 0 ? '<i class="dim">unknown</i>' : duration($now - $stat['lastupdate'], 2) . ' ago') . '</td>
    </tr>
  ';

  $rowclass = alt_row_class($rowclass);
}
?>
      </tbody>
     </table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
