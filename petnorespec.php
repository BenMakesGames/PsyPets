<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/grammar.php';
require_once 'commons/utility.php';
require_once 'commons/petlib.php';
require_once 'commons/petbadges.php';
require_once 'commons/shortscale.php';

$petid = (int)$_GET['petid'];

$command = 'SELECT * FROM `monster_pets` ' .
           'WHERE idnum=' . $petid . ' LIMIT 1';
$this_pet = $database->FetchSingle($command, 'fetching pet');

if($this_pet === false)
{
  header('Location: /directory.php');
  exit();
}

$command = 'SELECT * FROM `monster_users` WHERE `user`=' . quote_smart($this_pet['user']) . ' LIMIT 1';
$owner = $database->FetchSingle($command, 'fetching pet owner');

if($owner === false)
{
  header('Location: /directory.php');
  exit();
}

if($this_pet['user'] != $user['user'] || $this_pet['free_respec'] != 'yes')
{
  header('Location: /petprofile.php?petid=' . $petid);
  exit();
}

$petbadges = get_pet_badges($petid);

$exp_required = level_exp($this_pet['love_level']);

include 'commons/html.php';
?>
 <head>
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $owner['display'] ?> &gt; <?= $this_pet['petname'] ?> &gt; Respec</title>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <table style="padding-top:8px;">
      <tr>
       <td><?= pet_graphic($this_pet) ?></td>
       <td>
        <h4><a href="userprofile.php?user=<?= link_safe($owner['display']) ?>"><?= $owner['display'] ?></a> &gt; <?= $this_pet['petname'] ?></h4>
<?php
foreach($petbadges as $badge=>$value)
{
  if($value == 'yes')
    echo '<img src="gfx/badges/pet/' . $badge . '.png" height="20" width="20" title="' . $PET_BADGE_DESC[$badge] . '" /> ';
}
?>
       </td>
      </tr>
     </table>
     <ul class="tabbed">
      <li><a href="/petprofile.php?petid=<?= $petid ?>">Summary</a></li>
      <li><a href="/petfamilytree.php?petid=<?= $petid ?>">Family Tree</a></li>
<?php
  echo '
    <li><a href="/petlogs.php?petid=' . $petid . '">Activity Logs</a></li>
    <li><a href="/petevents.php?petid=' . $petid . '">Park Event Logs</a></li>
    <li><a href="/petlevelhistory.php?petid=' . $petid . '">Training History</a></li>
  ';

  if($this_pet['love_exp'] >= $exp_required && $this_pet['zombie'] != 'yes')
    echo '<li><a href="/affectionup.php?petid=' . $petid . '" class="success">Affection Reward!</a></li>';
  if($this_pet['ascend'] == 'yes')
    echo '<li><a href="/petascend.php?petid=' . $petid . '">Reincarnate</a></li>';
  if($this_pet['free_respec'] == 'yes')
    echo '<li class="activetab"><a href="/petrespec.php?petid=' . $petid . '" class="success">Retrain!</a></li>';
?>
     </ul>
     <p class="failure">The ability to respec pets has been disabled until a new respeccing system has been created.  Sorry!</p>
     <hr />
     <h5>What is Pet Retraining?</h5>
     <p>Retraining a pet will allow you to rearrange your pets' skills.  For example, you could turn a skilled Hunter into a skilled Jeweler.</p>
     <p>If the pet has earned mastery in any fields, it will lose its master status.</p>
     <h5>Why Would I Do This?</h5>
     <p>If you received or accidentally trained an unskilled pet, or if the game rules have changed significantly, you may want to dramatically change your pet's abilities.  A pet retraining will let you do just that!</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
