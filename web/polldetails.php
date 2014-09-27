<?php
$require_petload = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/polllib.php';

$pollid = (int)$_GET['id'];

$poll = get_poll_byid($pollid);

if($poll == false)
{
  header('Location: /admin/polls.php');
  exit();
}

$options = explode('|', $poll['options']);
$num_options = count($options);

$total = 0;

for($x = 0; $x < $num_options; $x++)
{
  $votes[$x] = get_poll_results($pollid, $x);
  $total += $votes[$x];
}

$current_poll = get_global('currentpoll');

if($pollid == $current_poll)
  $title = '<a href="pollstandalone.php">' . $poll['title'] . '</a>';
else
  $title = $poll['title'];

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Polls &gt; <?= $poll['title'] ?> &gt; Poll Results</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/polllist.php">Polls</a> &gt; <?= $title ?> &gt; Poll Results</h4>
<?= $poll['description'] ?>
     <table>
      <tr class="titlerow">
       <th>Option</th>
       <th colspan="2" class="centered">Votes</th>
      </tr>
<?php
$rowclass = begin_row_class();

for($x = 0; $x < $num_options; ++$x)
{
  echo '<tr class="' . $rowclass . '"><td>' . $options[$x] . '</td><td align="right">' . $votes[$x] . '</td><td>(' . round(($votes[$x] * 100) / $total) . '%)</td></tr>';
  $rowclass = alt_row_class($rowclass);
}
?>
      <tr class="<?= $rowclass ?>"><th>Total</th><td align="right"><?= $total ?></td><td></td></tr>
     </table>
<?php
if($admin['viewpolls'] == 'yes')
{
  echo '<h4>Duplicate-IP Voters</h4>';

  $command = 'SELECT ip,COUNT(residentid) AS `count` FROM psypets_poll_votes WHERE pollid=' . $pollid . ' GROUP BY ip HAVING `count`>1';
  $duplicates = $database->FetchMultiple($command, 'fetching possible duplicate votes');
  
  if(count($duplicates) > 0)
  {
    foreach($duplicates as $duplicate)
    {
      echo '<h5>' . $duplicate['ip'] . '</h5><ul>';

      $command = 'SELECT b.user,b.display FROM psypets_poll_votes AS a LEFT JOIN monster_users AS b ON a.residentid=b.idnum WHERE a.pollid=' . $pollid . ' AND a.ip=' . quote_smart($duplicate['ip']);
      $residents = $database->FetchMultiple($command, 'fetching potentially-alt-abusing voters');

      foreach($residents as $resident)
        echo '<li>' . $resident['display'] . ' (' . $resident['user'] . ')</li>';

      echo '</ul>';
    }
  }
  else
    echo '<p>None found!</p>';
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
