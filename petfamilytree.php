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
require_once 'commons/zodiac.php';
require_once 'commons/petactivitystats.php';

$petid = (int)$_GET['petid'];

$this_pet = $database->FetchSingle('SELECT * FROM `monster_pets` WHERE idnum=' . $petid . ' LIMIT 1');

if($this_pet === false)
{
  header('Location: ./directory.php');
  exit();
}

if($user['idnum'] > 0)
  $profile_url = 'residentprofile.php';
else
  $profile_url = 'publicprofile.php';

$owner = $database->FetchSingle('SELECT * FROM `monster_users` WHERE `user`=' . quote_smart($this_pet['user']) . ' LIMIT 1');

if($owner === false)
{
  header('Location: ./directory.php');
  exit();
}

if($owner['user'] == $SETTINGS['site_ingame_mailer'])
  $where = 'the Pet Shelter';
else if($owner['user'] == 'graveyard')
  $where = '<b style="color:#420;">the afterlife</b>';
else
  $where = $owner['display'] . '\'s House';

if($this_pet['motherid'] == 0)
  $mother = 'none';
else
{
  $mother = get_pet_byid($this_pet['motherid']);
  if($mother === false)
    $mother = 'departed';
}

if($this_pet['fatherid'] == 0)
  $father = 'none';
else
{
  $father = get_pet_byid($this_pet['fatherid']);
  if($father === false)
    $father = 'departed';
}

if($this_pet['motherid'] > 0 || $this_pet['fatherid'] > 0)
{
	$ors = array();

	if($this_pet['motherid'] > 0) $ors[] = 'motherid=' . $this_pet['motherid'];
	if($this_pet['fatherid'] > 0) $ors[] = 'fatherid=' . $this_pet['fatherid'];
		

  $siblings = $database->FetchMultiple('
    SELECT *
    FROM monster_pets
    WHERE
      idnum!=' . $this_pet['idnum'] . '
      AND (' . implode(' OR ', $ors) . ')
  ');
}
else
  $siblings = array();

$children = $database->FetchMultiple('
  SELECT *
  FROM monster_pets
  WHERE
    motherid=' . $this_pet['idnum'] . '
    OR fatherid=' . $this_pet['idnum'] . '
');

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
      <li class="activetab"><a href="/petfamilytree.php?petid=<?= $petid ?>">Family Tree</a></li>
<?php
if($user['user'] == $this_pet['user'] || $user['admin']['clairvoyant'] == 'yes')
  echo '<li><a href="/petlogs.php?petid=' . $petid . '">Activity Logs</a></li>';

echo '<li><a href="/petevents.php?petid=' . $petid . '">Park Event Logs</a></li>';

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

     <table>
      <tr>
       <td valign="top"><img src="/gfx/geneology.png" width="16" height="16" alt="" /></td>
       <td>
        <p><?= $this_pet['petname'] ?> is a <?= numeric_place($this_pet['generation']) ?>-generation pet.</p>
<?php
if($mother == 'none')
  echo '<p>' . ucfirst(p_pronoun($this_pet['gender'])) . ' mother is unknown; ';
else if($mother == 'departed')
  echo '<p>' . ucfirst(p_pronoun($this_pet['gender'])) . ' mother is <i class="dim">[departed #' . $this_pet['motherid'] . ']</i>; ';
else
  echo '<p>' . ucfirst(p_pronoun($this_pet['gender'])) . ' mother is <a href="/petfamilytree.php?petid=' . $this_pet['motherid'] . '">' . $mother['petname'] . '</a>; ';

if($father == 'none')
  echo ' ' . p_pronoun($this_pet['gender']) . ' father is unknown.</p>';
else if($father == 'departed')
  echo ' ' . p_pronoun($this_pet['gender']) . ' father is <i class="dim">[departed #' . $this_pet['fatherid'] . ']</i>.</p>';
else
  echo ' ' . p_pronoun($this_pet['gender']) . ' father is <a href="/petfamilytree.php?petid=' . $this_pet['fatherid'] . '">' . $father['petname'] . '</a>.</p>';
?>
       </td>
      </tr>
      <tr>
       <td valign="top"><img src="/gfx/siblings.png" width="16" height="16" alt="" /></td>
       <td>
<?php
$num_siblings = count($siblings);

echo '<p>' . $this_pet['petname'] . ' has ' . ($num_siblings > 0 ? $num_siblings : 'no') . ' sibling' . ($num_siblings == 1 ? '' : 's') . '.</p>';

if($num_siblings > 0)
{
  echo '<ul>';

  foreach($siblings as $sibling)
    echo '<li><a href="/petfamilytree.php?petid=' . $sibling['idnum'] . '">' . $sibling['petname'] . '</a></li>';

  echo '</ul>';
}
?>
       </td>
      </tr>
      <tr>
       <td valign="top"><img src="/gfx/children.png" width="16" height="16" alt="" /></td>
       <td>
<?php
$num_children = count($children);

echo '<p>' . $this_pet['petname'] . ' has ' . ($num_children > 0 ? $num_children : 'no') . ' child' . ($num_children == 1 ? '' : 'ren') . '.</p>';

if($num_children)
{
  echo '<ul>';

  foreach($children as $child)
    echo '<li><a href="/petfamilytree.php?petid=' . $child['idnum'] . '">' . $child['petname'] . '</a></li>';

  echo '</ul>';
}
?>
       </td>
      </tr>
     </table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
