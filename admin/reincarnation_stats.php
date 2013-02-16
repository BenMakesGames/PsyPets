<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

function calculate_median($arr) {
    sort($arr);
    $count = count($arr); //total numbers in array
    $middleval = floor(($count-1)/2); // find the middle value, or the lowest middle value
    if($count % 2) { // odd number, middle is the median
        $median = $arr[$middleval];
    } else { // even number, calculate avg of 2 medians
        $low = $arr[$middleval];
        $high = $arr[$middleval+1];
        $median = (($low+$high)/2);
    }
    return $median;
}

if($admin["manageitems"] != "yes")
{
  header("Location: /admin/tools.php");
  exit();
}

$history = $database->FetchMultiple(('
  SELECT
    b.petname,
    b.user,
    a.birthdate,
    a.deathdate,
    (a.deathdate-a.birthdate)/(60*60*24*7),
    a.level,
    a.mastery
  FROM
    `psypets_petlives` AS a
    LEFT JOIN monster_pets AS b
      ON a.petid=b.idnum
  WHERE deathdate>' . ($now - (60 * 60 * 24 * 30 * 6)) . '
  ORDER BY a.`deathdate` DESC
');

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Reincarnation Statistics</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Reincarnation Statistics</h4>
  <h5>Reincarnations In the Last 6 Months</h5>
  <table>
   <thead>
    <tr>
     <th>Name</th><th>Level</th>
     <th colspan="3">Life</th>
     <th>Age</th>
     <th>Masteries</th>
    </tr>
   </thead>
   <tbody>
<?php
$rowclass = begin_row_class();

foreach($history as $life)
{
  $masteries = explode(' ', $life['mastery']);
  $mastery_count = count($masteries);

  if(in_array('and', $masteries))
    $mastery_count--;

  if(in_array('electrical', $masteries))
    $mastery_count--;

  if(in_array('mechanical', $masteries))
    $mastery_count--;

  if(in_array('Hide-and-Go-Seek', $masteries))
    $mastery_count -= 2;
?>
    <tr class="<?= $rowclass ?>">
     <td><?= $life['petname'] ?></td>
     <td><?= $life['level'] ?></td>
     <td><?= local_date($life['birthdate'], $user['timezone'], $user['daylightsavings']) ?></td>
     <td>-</td>
     <td><?= local_date($life['deathdate'], $user['timezone'], $user['daylightsavings']) ?></td>
     <td><nobr><?= duration($life['deathdate'] - $life['birthdate'], 2) ?></nobr></td>
     <td>(<?= $mastery_count ?>) <?= $life['mastery'] ?></td>
    </tr>
<?php
  $durations_by_count[$mastery_count][] = $life['deathdate'] - $life['birthdate'];

  if($mastery_count == 1)
  {
    $durations_by_single_profession[$life['mastery']][] = $life['deathdate'] - $life['birthdate'];
    $levels_by_single_profession[$life['mastery']][] = $life['level'];
  }

  $rowclass = alt_row_class($rowclass);
}
?>
   </tbody>
  </table>
  <h5>Time to Get X Masteries</h5>
  <table>
   <thead>
    <tr><th>Masteries</th><th>Shortest</th><th>Average</th><th>Sample Size</th></tr>
   </thead>
   <tbody>
<?php
ksort($durations_by_count);

foreach($durations_by_count as $count=>$lengths)
{
  $average = array_sum($lengths) / count($lengths);

  echo '<tr><th>' . $count . '</th><td>' . duration(min($lengths), 2) . '</td><td>' . duration($average, 2) . '</td><td>(' . count($lengths) . ')</td></tr>';
}
?>
   </tbody>
  </table>
  <h5>Time to Get One Mastery</h5>
  <table>
   <thead>
    <tr><th>Mastery</th><th>Shortest</th><th>Average</th><th>Sample Size</th></tr>
   </thead>
   <tbody>
<?php
foreach($durations_by_single_profession as $mastery=>$lengths)
{
  $average = array_sum($lengths) / count($lengths);

  echo '<tr><th>' . $mastery . '</th><td>' . duration(min($lengths), 2) . '</td><td>' . duration($average, 2) . '</td><td>(' . count($lengths) . ')</td></tr>';
}
?>
   </tbody>
  </table>
  <h5>Level to Get One Mastery</h5>
  <table>
   <thead>
    <tr><th>Mastery</th><th>Lowest</th><th>Average</th><th>Sample Size</th></tr>
   </thead>
   <tbody>
<?php
foreach($levels_by_single_profession as $mastery=>$levels)
{
  $average = array_sum($levels) / count($levels);

  echo '<tr><th>' . $mastery . '</th><td>' . min($levels) . '</td><td>' . round($average, 1) . '</td><td>(' . count($levels) . ')</td></tr>';
}
?>
   </tbody>
  </table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
