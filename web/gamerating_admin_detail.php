<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/benlib.php';

if(array_key_exists('gameplay', $_GET))
  $extra_where = 'gameplay=' . (int)$_GET['gameplay'];
else if(array_key_exists('community', $_GET))
  $extra_where = 'community=' . (int)$_GET['community'];
else if(array_key_exists('pets', $_GET))
  $extra_where = 'pets=' . (int)$_GET['pets'];
else
{
  header('Location: ./gamerating_admin.php');
  exit();
}

$get_calc_time = microtime(true);

$command = 'SELECT COUNT(*) AS count,AVG(pets) AS rating FROM psypets_whatisbendoing WHERE pets!=0 AND ' . $extra_where;
$rate_pets = $database->FetchSingle($command, 'fetching pets average');

$command = 'SELECT COUNT(*) AS count,AVG(community) AS rating FROM psypets_whatisbendoing WHERE community!=0 AND ' . $extra_where;
$rate_community = $database->FetchSingle($command, 'fetching community average');

$command = 'SELECT COUNT(*) AS count,AVG(gameplay) AS rating FROM psypets_whatisbendoing WHERE gameplay!=0 AND ' . $extra_where;
$rate_gameplay = $database->FetchSingle($command, 'fetching game play average');

$command = 'SELECT COUNT(*) AS count,pets FROM psypets_whatisbendoing WHERE ' . $extra_where . ' GROUP BY(pets)';
$count_pets = $database->FetchMultipleBy($command, 'pets', 'fetching pets vote counts');

$command = 'SELECT COUNT(*) AS count,community FROM psypets_whatisbendoing WHERE ' . $extra_where . ' GROUP BY(community)';
$count_community = $database->FetchMultipleBy($command, 'community', 'fetching community vote counts');

$command = 'SELECT COUNT(*) AS count,gameplay FROM psypets_whatisbendoing WHERE ' . $extra_where . ' GROUP BY(gameplay)';
$count_gameplay = $database->FetchMultipleBy($command, 'gameplay', 'fetching game play vote counts');

$command = 'SELECT userid FROM psypets_whatisbendoing WHERE ' . $extra_where;
$userids = $database->FetchMultiple($command, 'fetching voter ids');

$total_signupdate = 0;

foreach($userids as $userid)
{
  $idnum = $userid['userid'];
  
  $voter = get_user_byid($idnum, 'display,signupdate,user');

  $total_signupdate += $voter['signupdate'];
  $names[] = $voter['user'];
  $displays[] = $voter['display'];
}

$command = 'SELECT COUNT(*) FROM monster_pets WHERE user IN (\'' . implode('\',\'', $names) . '\')';
$data = $database->FetchSingle($command, 'fetching voter\'s pets');

$total_pets = (int)$data['COUNT(*)'];

$average_signupdate = $total_signupdate / count($names);
$average_pets = $total_pets / count($names);

$get_calc_time = microtime(true) - $get_calc_time;

$footer_note = '<br />Took ' . round($get_calc_time, 4) . 's fetching the all the information.';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; City Hall &gt; Game Rating &gt; Details &gt; Focus</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4><a href="cityhall.php">City Hall</a> &gt; <a href="gamerating.php">Game Rating</a> &gt; <a href="gamerating_admin.php">Details</a> &gt; Focus</h4>
<?php
if($user['admin']['clairvoyant'] == 'yes')
{
?>
     <h5>Average Ratings</h5>
     <table border="0" cellspacing="0" cellpadding="4">
      <tr><td>Pets</td><td class="centered"><?= floor(($rate_pets['rating'] + 10) * 100 / 20) ?>%</td><td><?= rate_graphic((float)$rate_pets['rating']) ?></td></tr>
      <tr><td>Community</td><td class="centered"><?= floor(($rate_community['rating'] + 10) * 100 / 20) ?>%</td><td><?= rate_graphic((float)$rate_community['rating']) ?></td></tr>
      <tr><td>Game Play</td><td class="centered"><?= floor(($rate_gameplay['rating'] + 10) * 100 / 20) ?>%</td><td><?= rate_graphic((float)$rate_gameplay['rating']) ?></td></tr>
     </table>
     <p>Voters: <?= implode(', ', $displays) ?></p>
<?php
}
?>
     <p>Total pets among voters: <?= $total_pets ?></p>
     <p>Average pets/voter: <?= round($average_pets, 2) ?></p>
     <p>Average signup date: <?= duration($now - $average_signupdate, 2) ?> ago</p>
     <form action="gamerating.php" method="post">
     <h5>Pets</h5>
     <table>
<?php
foreach($RATINGS['pets'] as $key=>$values)
  echo '<tr><td>' . $count_pets[$values[0]]['count'] . '</td><td>' . $values[1] . '</td></tr>';
?>
     </table>

     <h5>Community</h5>
     <table>
<?php
foreach($RATINGS['community'] as $key=>$values)
  echo '<tr><td>' . $count_community[$values[0]]['count'] . '</td><td>' . $values[1] . '</td></tr>';
?>
     </table>

     <h5>Overall Game Play</h5>
     <table>
<?php
foreach($RATINGS['gameplay'] as $key=>$values)
  echo '<tr><td>' . $count_gameplay[$values[0]]['count'] . '</td><td>' . $values[1] . '</td></tr>';
?>
     </table>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
