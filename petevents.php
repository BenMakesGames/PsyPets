<?php
$require_login = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/grammar.php';
require_once 'commons/utility.php';
require_once 'commons/petlib.php';
require_once 'commons/parklib.php';

$petid = (int)$_GET['petid'];

$this_pet = $database->FetchSingle('SELECT * FROM `monster_pets` WHERE idnum=' . $petid . ' LIMIT 1');

if($this_pet === false)
{
  header('Location: /directory.php');
  exit();
}

$profile = $database->FetchSingle('SELECT * FROM psypets_profile_pet WHERE petid=' . $this_pet['idnum'] . ' LIMIT 1');

$owner = get_user_byuser($this_pet['user']);

if($owner === false)
{
  header('Location: /directory.php');
  exit();
}

if($owner['user'] == $SETTINGS['site_ingame_mailer'])
  $where = 'the Pet Shelter';
else if($owner['user'] == 'graveyard')
  $where = '<b style="color:#420;">the afterlife</b>';
else
  $where = $owner['display'] . '\'s House';

$command = 'SELECT * FROM psypets_park_event_results WHERE petid=' . $petid . ' ORDER BY timestamp DESC';
$event_results = $database->FetchMultiple($command, 'fetching park event results for this pet');

$petbadges = get_pet_badges($petid);

$exp_required = level_exp($this_pet['love_level']);

include 'commons/html.php';
?>
 <head>
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $owner['display'] ?> &gt; <?= $this_pet['petname'] ?></title>
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
include 'commons/petprofile/pets.php';
?>
     <ul class="tabbed">
      <li><a href="/petprofile.php?petid=<?= $petid ?>">Summary</a></li>
      <li><a href="/petfamilytree.php?petid=<?= $petid ?>">Family Tree</a></li>
<?php
if($user['user'] == $this_pet['user'] || $user['admin']['clairvoyant'] == 'yes')
  echo '<li><a href="/petlogs.php?petid=' . $petid . '">Activity Logs</a></li>';

echo '<li class="activetab"><a href="/petevents.php?petid=' . $petid . '">Park Event Logs</a></li>';

if($user['user'] == $this_pet['user'] || $user['admin']['clairvoyant'] == 'yes')
{
  echo '<li><a href="/petlevelhistory.php?petid=' . $petid . '">Training History</a></li>';

  if($this_pet['love_exp'] >= $exp_required && $this_pet['zombie'] != 'yes')
    echo '<li><a href="/affectionup.php?petid=' . $petid . '" class="success">Affection Reward!</a></li>';
  if($this_pet['ascend'] == 'yes')
    echo '<li><a href="/petascend.php?petid=' . $petid . '" class="success">Reincarnate</a></li>';
  if($this_pet['free_respec'] == 'yes')
    echo '<li><a href="/petrespec.php?petid=' . $petid . '" class="success">Retrain!</a></li>';
}
?>
     </ul>
<?php
if(count($event_results) == 0)
  echo '<p>No park event results have been recorded for this pet.</p>';
else
{
?>
<table>
 <tr class="titlerow">
  <th></th>
  <th>When</th>
  <th>Event</th>
  <th>Size</th>
  <th class="centered">Placement</th>
  <th>Prize</th>
 </tr>
<?php
  $rowclass = begin_row_class();
  foreach($event_results as $event)
  {
    if($event['eventtype'] == 'ctf' || $event['eventtype'] == 'tow')
      $placement = ($event['placement'] == 1 ? 'winning team' : 'losing team');
    else if($event['eventtype'] == 'hunt')
      $placement = '&ndash;';
    else
      $placement = '#' . $event['placement'];

?>
 <tr class="<?= $rowclass ?>">
  <td><a href="/eventdetails.php?idnum=<?= $event['eventid'] ?>"><img src="//<?= $SETTINGS['site_domain'] ?>/gfx/search.gif" /></a></td>
  <td><?= duration($now - $event['timestamp'], 2) ?> ago</td>
  <td><?= $EVENT_TYPES[$event['eventtype']] ?></td>
  <td class="centered"><?= $event['size'] ? $event['size'] : '?' ?></td>
  <td class="centered"><?= $placement ?></td>
  <td><?= str_replace(',', '<br />', $event['prizename']) ?></td>
 </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
</table>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
