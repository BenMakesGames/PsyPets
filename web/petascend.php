<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/encryption.php';
require_once 'commons/formatting.php';

if(count($userpets) == 0)
{
  header('Location: /myhouse.php');
  exit();
}

$petid = (int)$_GET['petid'];

$pet = get_pet_byid($petid);

if($pet['user'] != $user['user'] || $pet['ascend'] != 'yes')
{
  header('Location: /myhouse.php');
  exit();
}

$fields = array();

if($pet['ascend_adventurer'] == 'yes')
  $fields[] = 'adventurer';

if($pet['ascend_gatherer'] == 'yes')
  $fields[] = 'gatherer';

if($pet['ascend_lumberjack'] == 'yes')
  $fields[] = 'lumberjack';

if($pet['ascend_hunter'] == 'yes')
  $fields[] = 'hunter';

if($pet['ascend_fisher'] == 'yes')
  $fields[] = 'fisher';

if($pet['ascend_miner'] == 'yes')
  $fields[] = 'miner';

if($pet['ascend_artist'] == 'yes')
  $fields[] = 'handipet';

if($pet['ascend_smith'] == 'yes')
  $fields[] = 'smith';

if($pet['ascend_tailor'] == 'yes')
  $fields[] = 'tailor';

if($pet['ascend_leather'] == 'yes')
  $fields[] = 'leather';

if($pet['ascend_carpenter'] == 'yes')
  $fields[] = 'carpenter';

if($pet['ascend_jeweler'] == 'yes')
  $fields[] = 'jeweler';

if($pet['ascend_painter'] == 'yes')
  $fields[] = 'painter';

if($pet['ascend_sculptor'] == 'yes')
  $fields[] = 'sculptor';

if($pet['ascend_chemist'] == 'yes')
  $fields[] = 'chemist';

if($pet['ascend_inventor'] == 'yes')
  $fields[] = 'electrical engineer';

if($pet['ascend_mechanic'] == 'yes')
  $fields[] = 'mechanical engineer';

if($pet['ascend_binder'] == 'yes')
  $fields[] = 'magic-binder';

if($pet['ascend_vhagst'] == 'yes')
  $fields[] = 'Virtual Hide-and-Go-Seek Tagger';

$say_fields = implode(', ', $fields);
$last_comma = strrpos($say_fields, ',');

if($last_comma !== false)
  $say_fields = substr_replace($say_fields, ' and', $last_comma, 1);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Self-Actualization &gt; <?= $pet['petname'] ?></title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Self-Actualization &gt; <?= $pet['petname'] ?></h4>
     <p><strong><?= $pet['petname'] ?> has become a master <?= $say_fields ?>!</strong></p>
     <p><?= ucfirst(pronoun($pet['gender'])) ?> can continue as <?= pronoun($pet['gender']) ?> always has, however there may not be much left for <?= t_pronoun($pet['gender']) ?> to do.</p>
     <p>Alternatively, you may have <?= $pet['petname'] ?> be reincarnated!</p>
     <p><em>Reincarnation resets the stats of the pet completely!</em>  If it is a were-creature, it will become normal; if it is pregnant, it will lose its pregnancy; a dead pet that is reincarnated is even restored healthily to life!</p>
     <p>Additionally, reincarnated pets have access to equipment and special abilities that non-reincarnated pets do not have.</p>
     <ul>
      <li><a href="/petascend2.php?petid=<?= $pet['idnum'] ?>">Lovely!  Reincarnate <?= $pet['petname'] ?>!</a></li>
     </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
