<?php
require_once 'commons/init.php';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/grammar.php';
require_once 'commons/utility.php';
require_once 'commons/petlib.php';
require_once 'commons/petbadges.php';

require_once 'libraries/db_messages.php';

$petid = (int)$_GET['petid'];

$this_pet = $database->FetchSingle('SELECT * FROM `monster_pets` WHERE idnum=' . $petid . ' LIMIT 1');

if($this_pet === false)
{
  header('Location: ./directory.php');
  exit();
}

$command = 'SELECT * FROM psypets_profile_pet WHERE petid=' . $this_pet['idnum'] . ' LIMIT 1';
$profile = $database->FetchSingle($command, 'fetching pet profile text');

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

$life = ShortScale::toOrdinal($this_pet['incarnation']);

$max_advantages = $this_pet['incarnation'] - 1;

$errors = array();

if($_POST['action'] == 'Respec')
{
  if(!is_array($_POST['descriptions']))
    $_POST['descriptions'] = array();

  $_POST['descriptions'] = array_unique($_POST['descriptions']);

  if(count($_POST['descriptions']) < 3)
    $errors[] = '<span class="failure">You need to pick <em>at least</em> three statements to describe your pet.</span>';
  else if(count($_POST['descriptions']) > 10)
    $errors[] = '<span class="failure">You may not pick more than 10 statements to describe your pet.</span>';

  $advantages = 0;

  foreach($_POST as $key=>$value)
  {
    if(is_numeric($key) && $value == 'on')
    {
      if($key >= 100)
        $advantages++;
    }
  }

  if($advantages > $max_advantages)
    $errors[] = 'You may not choose more than ' . $max_advantages . ' Pet Advantage' . ($max_advantages != 1 ? 's' : '') . '.';

  if(count($errors) == 0)
  {
    $str = 0;
    $dex = 0;
    $sta = 0;
    $int = 0;
    $per = 0;
    $wit = 0;

    $open = mt_rand(3, 7);
    $extraverted = mt_rand(3, 7);
    $conscientious = mt_rand(3, 7);
    $playful = mt_rand(3, 7);
    $independent = mt_rand(3, 7);

    $brawling = 0;
    $athletics = 0;
    $stealth = 0;
    $survival = 0;
    $gathering = 0;
    $fishing = 0;
    $mining = 0;
    $crafting = 0;
    $painting = 0;
    $carpentry = 0;
    $jeweling = 0;
    $sculpting = 0;
    $electrical_engineering = 0;
    $mechanical_engineering = 0;
    $chemistry = 0;
    $smithing = 0;
    $tailoring = 0;
    $binding = 0;
    $piloting = 0;

    $weights = array();
    
    foreach($_POST['descriptions'] as $description)
    {
      switch($description)
      {
        case 'go':
          $weights['int'] += 10;
          break;
        case 'milkman':
          $weights['bra'] += 10;
          $weights['athletics'] += 5;
          $weights['str'] += 2;
          break;
        case 'presents':
          $weights['sur'] += 10;
          $weights['athletics'] += 5;
          $weights['stealth'] += 3;
          $weights['bra'] += 2;
          break;
        case 'vcr':
          $weights['eng'] += 10;
          $weights['int'] += 5;
          $weights['wit'] += 2;
          break;
        case 'run':
          $weights['athletics'] += 7;
          $weights['sta'] += 7;
          $weights['str'] += 3;
          break;
        case 'truffles':
          $weights['gathering'] += 10;
          $weights['per'] += 7;
          break;
        case 'car-chasing':
          $weights['sta'] += 5;
          $weights['athletics'] += 5;
          $weights['str'] += 2;
          break;
        case 'climber':
          $weights['dex'] += 10;
          $weights['athletics'] += 3;
          break;
        case 'fishing':
          $weights['fishing'] += 10;
          $weights['dex'] += 2;
          break;
        case 'hiking':
          $weights['sta'] += 10;
          $weights['gathering'] += 4;
          $weights['str'] += 4;
          $weights['sur'] += 1;
          break;
        case 'rock-climbing':
          $weights['str'] += 9;
          $weights['sta'] += 6;
          $weights['athletics'] += 4;
          $weights['dex'] += 4;
          break;
        case 'spelunking':
          $weights['mining'] += 6;
          $weights['per'] += 2;
          break;
        case 'tough':
          $weights['sta'] += 6;
          $weights['str'] += 1;
          break;
        case 'imagination':
          $weights['wit'] += 8;
          $weights['int'] += 5;
          break;
        case 'color':
          $weights['painting'] += 10;
          $weights['per'] += 5;
          $weights['cra'] += 3;
          $weights['tai'] += 2;
          break;
        case 'hears':
          $weights['per'] += 7;
          break;
        case 'beading':
          $weights['jeweling'] += 10;
          $weights['dex'] += 4;
          $weights['per'] += 2;
          $weights['cra'] += 2;
          break;
        case 'large':
          $weights['str'] += 5;
          $weights['sta'] += 5;
          break;
        case 'knits':
          $weights['tai'] += 10;
          $weights['dex'] += 4;
          break;
        case 'gems':
          $weights['mining'] += 5;
          $weights['jeweling'] += 5;
          break;
        case 'forts':
          $weights['carpentry'] += 8;
          $weights['cra'] += 1;
          break;
        case 'detective':
          $weights['int'] += 5;
          $weights['per'] += 5;
          $weights['wit'] += 5;
          break;
        case 'fashion':
          $weights['tai'] += 6;
          $weights['jeweling'] += 5;
          break;
        case 'rc':
          $weights['pil'] += 10;
          $weights['dex'] += 5;
          $weights['per'] += 4;
          $weights['eng'] += 3;
          $weights['mechanics'] += 2;
          break;
        case 'paints':
          $weights['painting'] += 8;
          $weights['dex'] += 3;
          $weights['per'] += 2;
          break;
        case 'volcano':
          $weights['chemistry'] += 8;
          $weights['int'] += 3;
          break;
        case 'claymation':
          $weights['sculpting'] += 10;
          $weights['dex'] += 5;
          $weights['per'] += 2;
          break;
        case 'origami':
          $weights['cra'] += 10;
          $weights['dex'] += 5;
          $weights['per'] += 2;
          $weights['sur'] += 1;
          break;
        case 'roborena':
          $weights['mechanics'] += 8;
          $weights['eng'] += 8;
          $weights['int'] += 5;
          $weights['smi'] += 2;
          break;
        case 'sports':
          $weights['athletics'] += 5;
          $weights['dex'] += 2;
          break;
        case 'hide-and-seek':
          $weights['per'] += 7;
          $weights['stealth'] += 9;
          break;
        case 'arcane':
          $weights['binding'] += 10;
          $weights['int'] += 5;
          break;
        case 'repairs':
          $weights['smi'] += 10;
          break;
        case 'stalks':
          $weights['stealth'] += 10;
          $weights['per'] += 5;
          $weights['sur'] += 2;
          break;
        case 'take-apart':
          $weights['mechanics'] += 8;
          $weights['eng'] += 4;
          $weights['int'] += 2;
          $weights['per'] += 2;
          break;
      }
    }

    // get total
    $total = 0;
    
    foreach($weights as $stat=>$value)
      $total += $value;

    $points = pet_level($this_pet);
    $new_points = 0;
    $new_total = 0;
    
    foreach($weights as $stat=>$value)
    {
      $score[$stat] = round($points * $value / $total);
      $new_points += $score[$stat];
    }

    // if rounding brought us over...
    while($new_points > $points)
    {
      $stat = array_rand($score);

      while($score[$stat] < 1)
        $stat = array_rand($score);

      $score[$stat]--;
      $new_points--;
    }
    
    // if rounding brought us under...
    while($new_points < $points)
    {
      $score[array_rand($score)]++;
      $new_points++;
    }

    foreach($PET_SKILLS as $skill)
      $stat_updates[] = '`' . $skill . '`=' . (int)$score[$skill];

    $stats = implode(',', $stat_updates);

    $ascends = '
      ascend=\'no\',
      ascend_adventurer=\'no\',
      ascend_hunter=\'no\',
      ascend_inventor=\'no\',
      ascend_artist=\'no\',
      ascend_gatherer=\'no\',
      ascend_smith=\'no\',
      ascend_tailor=\'no\',
      ascend_fisher=\'no\',
      ascend_lumberjack=\'no\',
      ascend_miner=\'no\',
      ascend_carpenter=\'no\',
      ascend_jeweler=\'no\',
      ascend_painter=\'no\',
      ascend_sculptor=\'no\',
      ascend_mechanic=\'no\',
      ascend_binder=\'no\',
      ascend_chemist=\'no\'
    ';

    $training = '
      str_count=0,
      dex_count=0,
      sta_count=0,
      per_count=0,
      int_count=0,
      wit_count=0,
      bra_count=0,
      athletics_count=0,
      stealth_count=0,
      sur_count=0,
      gathering_count=0,
      fishing_count=0,
      mining_count=0,
      cra_count=0,
      painting_count=0,
      carpentry_count=0,
      jeweling_count=0,
      sculpting_count=0,
      eng_count=0,
      mechanics_count=0,
      chemistry_count=0,
      smi_count=0,
      tai_count=0,
      binding_count=0,
      pil_count=0
    ';

    if($this_pet['toolid'] > 0)
    {
      $command = 'UPDATE monster_inventory SET location=\'home\',user=' . quote_smart($user['user']) . ',changed=' . $now . ' WHERE idnum=' . $this_pet['toolid'] . ' LIMIT 1';
      $database->FetchNone($command, 'unequipping pet');
    }

    $database->FetchNone('
      UPDATE monster_pets SET
        costumed=\'no\',toolid=0,
        free_respec=\'no\',
        ' . $stats . ',
        ' . $ascends . ',
        ' . $training . '
      WHERE idnum=' . $this_pet['idnum'] . '
      LIMIT 1
    ');

    add_db_message($user['idnum'], FLASH_MESSAGE_PET_PROGRESS, $this_pet['petname'] . '\'s skills have been reset!');
    
    header('Location: /petprofile.php?petid=' . $petid);
    exit();
  }
}

$exp_required = level_exp($this_pet['love_level']);

include 'commons/html.php';
?>
 <head>
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $owner['display'] ?> &gt; <?= $this_pet['petname'] ?> &gt; Respec</title>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/petnote1.js"></script>
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
     <h5>What is Pet Retraining?</h5>
     <p>Retraining a pet will allow you to rearrange your pets' skills.  For example, you could turn a skilled Hunter into a skilled Jeweler.</p>
     <p class="obstacle">Any current training will be forgotten, and if the pet has earned mastery in any fields, it will lose its master status.</p>
     <h5>Why Would I Do This?</h5>
     <p>If you received or accidentally trained an unskilled pet, or if the game rules have changed significantly, you may want to dramatically change your pet's abilities.  A pet retraining will let you do just that!</p>
     <form method="post">
     <h5>Pet Survey</h5>
<?php
  if(count($errors) > 0)
    echo '<ul><li>', implode('</li><li>', $errors), '</li></ul>';
?>
     <p>Check off at least 3 statements that describe what you want your pet to be like (you may check off up to 10, if you want).  How you answer will determine how your pet's abilities are redistributed.  It's total level - <?= pet_level($this_pet) ?> - will remain unchanged.</p>
     <ul class="plainlist">
      <li><input type="checkbox" name="descriptions[]" value="go" /> Always beats you when you play Go.</li>
      <li><input type="checkbox" name="descriptions[]" value="milkman" /> Attacks the milkman.</li>
      <li><input type="checkbox" name="descriptions[]" value="presents" /> Brings you back "presents".</li>
      <li><input type="checkbox" name="descriptions[]" value="vcr" /> Can program the VCR.</li>
      <li><input type="checkbox" name="descriptions[]" value="run" /> Can run around the house for hours on end.</li>
      <li><input type="checkbox" name="descriptions[]" value="truffles" /> Can sniff out truffles.</li>
      <li><input type="checkbox" name="descriptions[]" value="car-chasing" /> Chases cars.</li>
      <li><input type="checkbox" name="descriptions[]" value="climber" /> Climbs in, on, up, down, and around everything.</li>
      <li><input type="checkbox" name="descriptions[]" value="fishing" /> Enjoys fishing.</li>
      <li><input type="checkbox" name="descriptions[]" value="hiking" /> Enjoys hiking.</li>
      <li><input type="checkbox" name="descriptions[]" value="rock-climbing" /> Goes rock-climbing.</li>
      <li><input type="checkbox" name="descriptions[]" value="spelunking" /> Goes spelunking.</li>
      <li><input type="checkbox" name="descriptions[]" value="tough" /> Has a high tolerance for pain.</li>
      <li><input type="checkbox" name="descriptions[]" value="imagination" /> Has an active imagination.</li>
      <li><input type="checkbox" name="descriptions[]" value="color" /> Has an eye for color.</li>
      <li><input type="checkbox" name="descriptions[]" value="hears" /> Hears when you're getting food from a mile away.</li>
      <li><input type="checkbox" name="descriptions[]" value="beading" /> Is interested in beading.</li>
      <li><input type="checkbox" name="descriptions[]" value="large" /> Is of large and imposing stature.</li>
      <li><input type="checkbox" name="descriptions[]" value="knits" /> Knit <?= t_pronoun($this_pet['pronoun']) ?> a scarf and hat.</li>
      <li><input type="checkbox" name="descriptions[]" value="gems" /> Likes gemstones.</li>
      <li><input type="checkbox" name="descriptions[]" value="forts" /> Likes to build forts.</li>
      <li><input type="checkbox" name="descriptions[]" value="detective" /> Likes to play "detective".</li>
      <li><input type="checkbox" name="descriptions[]" value="fashion" /> Looks through fashion magazines.</li>
      <li><input type="checkbox" name="descriptions[]" value="rc" /> Loves <?= p_pronoun($this_pet['gender']) ?> remote-control toys.</li>
      <li><input type="checkbox" name="descriptions[]" value="paints" /> Loves to paint.</li>
      <li><input type="checkbox" name="descriptions[]" value="volcano" /> Made a vinegar and baking-soda volcano for the science fair.</li>
      <li><input type="checkbox" name="descriptions[]" value="claymation" /> Made a claymation video.</li>
      <li><input type="checkbox" name="descriptions[]" value="origami" /> Makes all kinds of origami animals.</li>
      <li><input type="checkbox" name="descriptions[]" value="roborena" /> Participates regularly in Roborena events.</li>
      <li><input type="checkbox" name="descriptions[]" value="sports" /> Plays a lot of sports.</li>
      <li><input type="checkbox" name="descriptions[]" value="hide-and-seek" /> Plays hide and seek with <?= p_pronoun($this_pet['gender']) ?> friends.</li>
      <li><input type="checkbox" name="descriptions[]" value="arcane" /> Pores over dusty tomes about the arcane arts.</li>
      <li><input type="checkbox" name="descriptions[]" value="repairs" /> Repairs broken weapons.</li>
      <li><input type="checkbox" name="descriptions[]" value="stalks" /> Stalks during the night.</li>
      <li><input type="checkbox" name="descriptions[]" value="take-apart" /> Takes stuff apart to see how it works.</li>
     </ul>
     <p><input type="submit" name="action" value="Respec" /></p>
     </form>
<?php if($pet['toolid'] > 0): ?>
  <p><i>(The tool <?= $pet['petname'] ?> is using will be dropped into the Common Room of your house.)</i></p>
<?php endif; ?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
