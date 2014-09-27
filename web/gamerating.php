<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/benlib.php';

$command = 'SELECT pets,community,gameplay FROM psypets_whatisbendoing WHERE userid=' . $user['idnum'] . ' LIMIT 1';
$vote = $database->FetchSingle($command, 'fetching resident votes');

$message = '';

if($_POST['action'] == 'vote')
{
  if(array_key_exists($_POST['pets'], $RATINGS['pets']) &&
    array_key_exists($_POST['community'], $RATINGS['community']) &&
    array_key_exists($_POST['gameplay'], $RATINGS['gameplay']))
  {
    $pets = (int)$RATINGS['pets'][$_POST['pets']][0];
    $community = (int)$RATINGS['community'][$_POST['community']][0];
    $gameplay = (int)$RATINGS['gameplay'][$_POST['gameplay']][0];

    if($vote === false)
      $command = 'INSERT INTO psypets_whatisbendoing (userid, lastchange, pets, community, gameplay) VALUES ' .
        '(' . $user['idnum'] . ', ' . $now . ', ' . $pets . ', ' . $community . ', ' . $gameplay . ')';
    else
      $command = 'UPDATE psypets_whatisbendoing SET lastchange=' . $now .
        ',pets=' . $pets . ',community=' . $community . ',gameplay=' . $gameplay . ' WHERE userid=' . $user['idnum'] . ' LIMIT 1';

    $database->FetchNone($command, 'updating vote');

    $vote['pets'] = $pets;
    $vote['community'] = $community;
    $vote['gameplay'] = $gameplay;
    
    $message = '<p class="success">Thanks!  Your vote has been recorded!  Feel free to come back and update it at any time.</p>';
  }
  else
  {
    $message = '<p class="failure">What?  ' . $_POST['pets'] . ', ' . $_POST['community'] . ', ' . $_POST['gameplay'] . '?</p>';
  }
}

$get_max_time = microtime(true);

$command = 'SELECT COUNT(*) AS count,AVG(pets) AS rating FROM psypets_whatisbendoing WHERE pets!=0';
$rate_pets = $database->FetchSingle($command, 'fetching pets votes');

$command = 'SELECT COUNT(*) AS count,AVG(community) AS rating FROM psypets_whatisbendoing WHERE community!=0';
$rate_community = $database->FetchSingle($command, 'fetching community totals');

$command = 'SELECT COUNT(*) AS count,AVG(gameplay) AS rating FROM psypets_whatisbendoing WHERE gameplay!=0';
$rate_gameplay = $database->FetchSingle($command, 'fetching game play totals');

$get_max_time = microtime(true) - $get_max_time;
/*
$get_votes_time = microtime(true);

$command = 'SELECT COUNT(*) AS c FROM psypets_whatisbendoing GROUP BY(economy)';
$vote_data = $database->FetchMultipleBy($command, 'what', 'fetching vote totals');

$get_votes_time = microtime(true) - $get_votes_time;
*/
$footer_note = '<br />Took ' . round($get_max_time, 4) . 's fetching the average rating.';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; City Hall &gt; Game Rating</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="cityhall.php">City Hall</a> &gt; Game Rating</h4>
<?php
if($message != '')
  echo $message;

echo '<ul><li><a href="gamerating_admin.php">View detailed reports</a></li></ul>';
?>
     <h5>Average Ratings</h5>
     <table>
      <tr><td>Pets</td><td class="centered"><?= floor(($rate_pets['rating'] + 10) * 100 / 20) ?>%</td><td><?= rate_graphic((float)$rate_pets['rating']) ?></td></tr>
      <tr><td>Community</td><td class="centered"><?= floor(($rate_community['rating'] + 10) * 100 / 20) ?>%</td><td><?= rate_graphic((float)$rate_community['rating']) ?></td></tr>
      <tr><td>Game Play</td><td class="centered"><?= floor(($rate_gameplay['rating'] + 10) * 100 / 20) ?>%</td><td><?= rate_graphic((float)$rate_gameplay['rating']) ?></td></tr>
     </table>
     <form action="gamerating.php" method="post">
<?php
$rating_something = 0;

?>
     <h5>Pets</h5>
     <p>Every hour your pets engage themselves in some activity.  Between that time, there are many possible ways for you to directly and indirectly interact with your pets (petting, feeding, park events, buying them food and supplies...)</p>
     <ul class="plainlist">
<?php
foreach($RATINGS['pets'] as $key=>$values)
  echo '<li><input type="radio" name="pets" value="' . $key  . '"' . ($vote['pets'] == $values[0] ? ' checked' : '' ) . ' /> ' . $values[1] . '</li>';

echo '</ul>';

$rating_something++;
?>
     <h5>Community</h5>
     <p>How would you describe the community of other players, as you experience it (through the Plaza, PsyMails, profile comments, etc)?</p>
     <ul class="plainlist">
<?php
foreach($RATINGS['community'] as $key=>$values)
  echo '<li><input type="radio" name="community" value="' . $key  . '"' . ($vote['community'] == $values[0] ? ' checked' : '' ) . ' /> ' . $values[1] . '</li>';

echo '</ul>';

$rating_something++;
?>
     <h5>Overall Game Play</h5>
     <ul class="plainlist">
<?php
foreach($RATINGS['gameplay'] as $key=>$values)
  echo '<li><input type="radio" name="gameplay" value="' . $key  . '"' . ($vote['gameplay'] == $values[0] ? ' checked' : '' ) . ' /> ' . $values[1] . '</li>';

echo '</ul>';

$rating_something++;

if($rating_something > 0)
{
  echo '     <p><input type="hidden" name="action" value="vote" /><input type="submit" value="Vote!" /></p>';

  if($rating_something < 3)
    echo '<p>You are not eligible to rate all aspects of the game at this time.  What you can rate depends on how long you\'ve been playing, whether you have a License to Commerce, and other factors.</p>';
}
else
  echo '<p>You are not eligible to rate any aspect of the game at this time.  What you can rate depends on how long you\'ve been playing, whether you have a License to Commerce, and other factors.</p>';
?>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
