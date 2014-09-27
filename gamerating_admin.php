<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/benlib.php';

$command = 'SELECT COUNT(*) AS count,AVG(pets) AS rating FROM psypets_whatisbendoing WHERE pets!=0';
$rate_pets = $database->FetchSingle($command, 'fetching pets average');

$command = 'SELECT COUNT(*) AS count,AVG(community) AS rating FROM psypets_whatisbendoing WHERE community!=0';
$rate_community = $database->FetchSingle($command, 'fetching community average');

$command = 'SELECT COUNT(*) AS count,AVG(gameplay) AS rating FROM psypets_whatisbendoing WHERE gameplay!=0';
$rate_gameplay = $database->FetchSingle($command, 'fetching game play average');

$command = 'SELECT COUNT(*) AS count,pets FROM psypets_whatisbendoing GROUP BY(pets)';
$count_pets = $database->FetchMultipleBy($command, 'pets', 'fetching pets vote counts');

$command = 'SELECT COUNT(*) AS count,community FROM psypets_whatisbendoing GROUP BY(community)';
$count_community = $database->FetchMultipleBy($command, 'community', 'fetching community vote counts');

$command = 'SELECT COUNT(*) AS count,gameplay FROM psypets_whatisbendoing GROUP BY(gameplay)';
$count_gameplay = $database->FetchMultipleBy($command, 'gameplay', 'fetching game play vote counts');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; City Hall &gt; Game Rating &gt; Details</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4><a href="cityhall.php">City Hall</a> &gt; <a href="gamerating.php">Game Rating</a> &gt; Details</h4>
     <p>Click on an answer to see more information about the people who voted for that answer.</p>
     <h5>Pets</h5>
     <table>
<?php
foreach($RATINGS['pets'] as $key=>$values)
  echo '<tr><td>' . $count_pets[$values[0]]['count'] . '</td><td><a href="gamerating_admin_detail.php?pets=' . $values[0] . '">' . $values[1] . '</a></td></tr>';
?>
     </table>

     <h5>Community</h5>
     <table>
<?php
foreach($RATINGS['community'] as $key=>$values)
  echo '<tr><td>' . $count_community[$values[0]]['count'] . '</td><td><a href="gamerating_admin_detail.php?community=' . $values[0] . '">' . $values[1] . '</a></td></tr>';
?>
     </table>

     <h5>Overall Game Play</h5>
     <table>
<?php
foreach($RATINGS['gameplay'] as $key=>$values)
  echo '<tr><td>' . $count_gameplay[$values[0]]['count'] . '</td><td><a href="gamerating_admin_detail.php?gameplay=' . $values[0] . '">' . $values[1] . '</a></td></tr>';
?>
     </table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
