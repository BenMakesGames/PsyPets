<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/grammar.php';
require_once 'commons/utility.php';
require_once 'commons/petlib.php';
require_once 'commons/parklib.php';
require_once 'commons/petbadges.php';

require_once 'libraries/progress_bar.php';

$petid = (int)$_GET['petid'];

$this_pet = $database->FetchSingle('SELECT * FROM `monster_pets` WHERE idnum=' . $petid . ' LIMIT 1');

if($this_pet === false)
{
  header('Location: /directory.php');
  exit();
}

if($user['user'] != $this_pet['user'] && $user['admin']['clairvoyant'] != 'yes')
{
  header('Location: /petprofile.php?petid=' . $petid);
  exit();
}

if($_POST['action'] == 'Clear History')
{
  $command = 'DELETE FROM psypets_pet_level_logs WHERE petid=' . $petid;
  $database->FetchNone($command, 'clearing pet level-up logs');
}

$command = 'SELECT * FROM psypets_pet_level_logs WHERE petid=' . $petid . ' ORDER BY idnum DESC';
$levelup_results = $database->FetchMultiple($command, 'fetching park event results for this pet');

$petbadges = get_pet_badges($petid);

$user_badges = get_badges_byuserid($user['idnum']);

$exp_required = level_exp($this_pet['love_level']);

foreach($PET_SKILLS as $skill)
{
  $percent = $this_pet[$skill . '_count'] / level_stat_exp($this_pet[$skill]);
  $almost_leveled[] = array('percent' => $percent, 'stat' => ucfirst($PET_STAT_DESCRIPTIONS[$skill]));
}

function skill_sort($a, $b)
{
  if($a['percent'] == $b['percent'])
    return 0;
  else
    return($a['percent'] > $b['percent'] ? -1 : 1);
}

usort($almost_leveled, 'skill_sort');

include 'commons/html.php';
?>
 <head>
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?> &gt; <?= $this_pet['petname'] ?></title>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/petnote1.js"></script>
  <style type="text/css">
   #family td
   {
     padding-left: 3em;
   }
  </style>
 </head>
 <body>
<?php
include 'commons/header_2.php';

$owner = $user;

include 'commons/petprofile/pets.php';
?>
     <ul class="tabbed">
      <li><a href="/petprofile.php?petid=<?= $petid ?>">Summary</a></li>
      <li><a href="/petfamilytree.php?petid=<?= $petid ?>">Family Tree</a></li>
<?php
if($user['user'] == $this_pet['user'] || $user['admin']['clairvoyant'] == 'yes')
  echo '<li><a href="/petlogs.php?petid=' . $petid . '">Activity Logs</a></li>';

echo '<li><a href="/petevents.php?petid=' . $petid . '">Park Event Logs</a></li>';

if($user['user'] == $this_pet['user'] || $user['admin']['clairvoyant'] == 'yes')
{
  echo '<li class="activetab"><a href="/petlevelhistory.php?petid=' . $petid . '">Training History</a></li>';

  if($this_pet['love_exp'] >= $exp_required && $this_pet['zombie'] != 'yes')
    echo '<li><a href="/affectionup.php?petid=' . $petid . '" class="success">Affection Reward!</a></li>';
  if($this_pet['ascend'] == 'yes')
    echo '<li><a href="/petascend.php?petid=' . $petid . '" class="success">Reincarnate!</a></li>';
  if($this_pet['free_respec'] == 'yes')
    echo '<li><a href="/petrespec.php?petid=' . $petid . '" class="success">Retrain!</a></li>';
}
?>
     </ul>
<?php
$num_training_revealed = 3 + ($user_badges['trained_20'] == 'yes' ? 2 : 0) + ($this_pet['merit_transparent'] == 'yes' ? 2 : 0);

echo '<h5>Current Training</h5><p>Only the ' . say_number($num_training_revealed) . ' stats closest to leveling are given here.</p><table><tbody>';

$i = 0;
foreach($almost_leveled as $almost)
{
  echo '
    <tr>
      <th>' . $almost['stat'] . '</th>
      <td>' . xhtml_progress_bar($almost['percent'] * 100, 100, 50, '#f00') . '</td>
      <td>' . ($almost['percent'] > 1 ? '100%+' : floor($almost['percent'] * 100) . '%') . '</td>
    </tr>
  ';

  if(++$i >= $num_training_revealed)
    break;
}

echo '</tbody></table><h5>History</h5>';
  
if(count($levelup_results) == 0)
  echo '<p>No level-up history has been recorded for this pet.</p>';
else
{
?>
<table>
 <tr class="titlerow">
  <th>When</th>
  <th>Description</th>
 </tr>
<?php
  $rowclass = begin_row_class();
  foreach($levelup_results as $event)
  {
?>
 <tr class="<?= $rowclass ?>">
  <td valign="top"><nobr><?= duration($now - $event['timestamp'], 2) ?> ago</nobr></td>
  <td valign="top"><?= $event['answer'] ?></td>
 </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
</table>
<form method="post">
<p><input type="submit" name="action" value="Clear History" class="bigbutton" /></p>
</form>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
