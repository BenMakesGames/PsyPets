<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/encryption.php';
require_once 'commons/formatting.php';
require_once 'commons/petgraphics.php';
require_once 'commons/moonphase.php';

if(count($userpets) == 0)
{
  header('Location: /myhouse.php');
  exit();
}

$petid = (int)$_GET['petid'];

$pet = get_pet_byid($petid);

if($pet['user'] != $user['user'] || $pet['ascend'] != 'yes' || $pet['location'] != 'home' || $pet['zombie'] == 'yes')
{
  header('Location: /myhouse.php');
  exit();
}

$custom_graphic = (strpos($pet['graphic'], '/') !== false);

$petgfx = $PET_GRAPHICS;

if($pet['graphic'] == 'unicorn.png' || $pet['graphic'] == 'unicorn_candy.png' || $pet['graphic'] == 'unicorn_citron.png' || $pet['graphic'] == 'unicorn_sopretty.png')
  array_unshift($petgfx, 'unicorn_sopretty.png');

if(!$custom_graphic)
{
  $gfx_index = array_search($pet['graphic'], $petgfx);
  
  if($gfx_index === false)
  {
    $gfx_index = 0;
    array_unshift($petgfx, $pet['graphic']);
  }

  $thispic = $petgfx[$gfx_index];
}

$errors = array();

$max_advantages = 3;

if(strlen($_POST['submit']) == 0)
  $_POST['picture'] = $thispic;
else if($_POST['submit'] == 'Reincarnate')
{
  if($custom_graphic)
    $graphic = $pet['graphic'];
  else
  {
    if(!in_array($_POST['picture'], $petgfx))
    {
      $_POST['picture'] = $thispic;
      $errors[] = 'Select a graphic for this pet.';
    }
    else
      $graphic = $_POST['picture'];
  }
/*
  if($_POST['personality'] < 1 || $_POST['personality'] > 6)
    $errors[] = 'You forgot to describe your pet\'s personality!';
*/
  if((int)$_POST['physical'] < 1 || (int)$_POST['physical'] > 3)
    $errors[] = 'You forgot to describe your pet\'s physical abilities!';

  if((int)$_POST['mental'] < 1 || (int)$_POST['mental'] > 3)
    $errors[] = 'You forgot to describe your pet\'s mental abilities!';

  if((int)$_POST['skill'][0] < 1 || (int)$_POST['skill'][0] > 23 || (int)$_POST['skill'][1] < 1 || (int)$_POST['skill'][1] > 23 || (int)$_POST['skill'][0] == (int)$_POST['skill'][1])
    $errors[] = 'You forgot to pick two skills and/or knowledges for your pet!';

  if(count($_POST['skill']) > 2)
    $errors[] = 'You picked more than two skills and/or knowledges for your pet!';

  if((int)$_POST['personality'] < 1 || (int)$_POST['personality'] > 10)
    $errors[] = 'You forgot to describe your pet\'s personality!';
    
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

    $open =          mt_rand(4, 6) + ($_POST['personality'] == 5 ? 2 : 0) - ($_POST['personality'] == 6 ? 2 : 0);
    $extraverted =   mt_rand(4, 6) + ($_POST['personality'] == 3 ? 2 : 0) - ($_POST['personality'] == 4 ? 2 : 0);
    $conscientious = mt_rand(4, 6) + ($_POST['personality'] == 1 ? 2 : 0) - ($_POST['personality'] == 2 ? 2 : 0);
    $playful =       mt_rand(4, 6) + ($_POST['personality'] == 7 ? 2 : 0) - ($_POST['personality'] == 8 ? 2 : 0);
    $independent =   mt_rand(4, 6) + ($_POST['personality'] == 9 ? 2 : 0) - ($_POST['personality'] == 10 ? 2 : 0);

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
    $leather = 0;
    $binding = 0;
    $piloting = 0;
    $astronomy = 0;
    $music = 0;
    
    if((int)$_POST['physical'] == 1)
      $str++;
    else if((int)$_POST['physical'] == 2)
      $dex++;
    else if((int)$_POST['physical'] == 3)
      $sta++;

    if((int)$_POST['mental'] == 1)
      $int++;
    else if((int)$_POST['mental'] == 2)
      $per++;
    else if((int)$_POST['mental'] == 3)
      $wit++;

    foreach($_POST['skill'] as $skill)
    {
      $skill = (int)$skill;

      if($skill == 1)
        $brawling++;
      else if($skill == 2)
        $athletics++;
      else if($skill == 3)
        $stealth++;
      else if($skill == 4)
        $survival++;
      else if($skill == 5)
        $gathering++;
      else if($skill == 6)
        $fishing++;
      else if($skill == 7)
        $mining++;
      else if($skill == 9)
        $crafting++;
      else if($skill == 10)
        $painting++;
      else if($skill == 11)
        $carpentry++;
      else if($skill == 12)
        $jeweling++;
      else if($skill == 13)
        $sculpting++;
      else if($skill == 14)
        $electrical_engineering++;
      else if($skill == 15)
        $mechanical_engineering++;
      else if($skill == 16)
        $chemistry++;
      else if($skill == 17)
        $smithing++;
      else if($skill == 18)
        $tailoring++;
      else if($skill == 19)
        $binding++;
      else if($skill == 20)
        $piloting++;
      else if($skill == 21)
        $astronomy++;
      else if($skill == 22)
        $music++;
      else if($skill == 23)
        $leather++;
    }

    $stats = '
      `extraverted`=' . $extraverted . ',
      `open`=' . $open . ',
      `conscientious`=' . $conscientious . ',
      `playful`=' . $playful . ',
      `independent`=' . $independent . ',
      `str`=' . $str . ',
      `dex`=' . $dex . ',
      `sta`=' . $sta . ',
      `per`=' . $per . ',
      `int`=' . $int . ',
      `wit`=' . $wit . ',
      `bra`=' . $brawling . ',
      `athletics`=' . $athletics . ',
      `stealth`=' . $stealth . ',
      `sur`=' . $survival . ',
      `gathering`=' . $gathering . ',
      `fishing`=' . $fishing . ',
      `mining`=' . $mining . ',
      `cra`=' . $crafting . ',
      `painting`=' . $painting . ',
      `carpentry`=' . $carpentry . ',
      `jeweling`=' . $jeweling . ',
      `sculpting`=' . $sculpting . ',
      `eng`=' . $electrical_engineering . ',
      `mechanics`=' . $mechanical_engineering . ',
      `chemistry`=' . $chemistry . ',
      `smi`=' . $smithing . ',
      `tai`=' . $tailoring . ',
      `leather`=' . $leather . ',
      `binding`=' . $binding . ',
      `pil`=' . $piloting . ',
      `astronomy`=' . $astronomy . ',
      `music`=' . $music . '
    ';

    $merits = '
      merit_steady_hands=\'' . ($_POST['100'] == 'on' ? 'yes' : 'no') . '\',
      merit_light_sleeper=\'' . ($_POST['101'] == 'on' ? 'yes' : 'no') . '\',
      merit_acute_senses=\'' . ($_POST['102'] == 'on' ? 'yes' : 'no') . '\',
      merit_catlike_balance=\'' . ($_POST['103'] == 'on' ? 'yes' : 'no') . '\',
      merit_tough_hide=\'' . ($_POST['104'] == 'on' ? 'yes' : 'no') . '\',
      merit_lightning_calculator=\'' . ($_POST['105'] == 'on' ? 'yes' : 'no') . '\',
      merit_silver_tongue=\'' . ($_POST['106'] == 'on' ? 'yes' : 'no') . '\',
      merit_lucky=\'' . ($_POST['107'] == 'on' ? 'yes' : 'no') . '\',
      merit_medium=\'' . ($_POST['108'] == 'on' ? 'yes' : 'no') . '\',
      merit_berserker=\'' . ($_POST['109'] == 'on' ? 'yes' : 'no') . '\',
      merit_predicts_earthquakes=\'' . ($_POST['110'] == 'on' ? 'yes' : 'no') . '\',
      merit_ravenous=\'' . ($_POST['111'] == 'on' ? 'yes' : 'no') . '\',
      merit_careful_with_equipment=\'' . ($_POST['112'] == 'on' ? 'yes' : 'no') . '\',
      merit_transparent=\'' . ($_POST['113'] == 'on' ? 'yes' : 'no') . '\',
      merit_pruriency=\'' . ($_POST['114'] == 'on' ? 'yes' : 'no') . '\',
      merit_sleep_walker=\'' . ($_POST['115'] == 'on' ? 'yes' : 'no') . '\',
      merit_moonkin=\'' . (mt_rand(1, 100) <= moon_phase_power(time()) ? 'yes' : 'no') . '\'
    ';

    $ascends = '
      ascend=\'no\',
      ascend_adventurer=\'no\',
      ascend_hunter=\'no\',
      ascend_inventor=\'no\',
      ascend_artist=\'no\',
      ascend_gatherer=\'no\',
      ascend_smith=\'no\',
      ascend_tailor=\'no\',
      ascend_leather=\'no\',
      ascend_fisher=\'no\',
      ascend_lumberjack=\'no\',
      ascend_miner=\'no\',
      ascend_carpenter=\'no\',
      ascend_jeweler=\'no\',
      ascend_painter=\'no\',
      ascend_sculptor=\'no\',
      ascend_mechanic=\'no\',
      ascend_binder=\'no\',
      ascend_chemist=\'no\',
      ascend_vhagst=\'no\'
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
      leather_count=0,
      binding_count=0,
      pil_count=0,
      astronomy_count=0,
      music_count=0
    ';

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

    $karma = karma_for_reincarnating(count($fields));

    $say_fields = implode(', ', $fields);
    $last_comma = strrpos($say_fields, ',');

    if($last_comma !== false)
      $say_fields = substr_replace($say_fields, ' and', $last_comma, 1);

    $database->FetchNone('
			INSERT INTO psypets_petlives
			(petid, life, birthdate, deathdate, graphic, level, mastery)
			VALUES
			(
				' . $pet['idnum'] . ',
				' . $pet['incarnation'] . ',
				' . $pet['birthday'] . ',
				' . $now . ',
				' . $database->Quote($pet['graphic']) . ',
				' . pet_level($pet) . ',
				' . $database->Quote($say_fields) . '
			)
		');

    if($pet['toolid'] > 0)
    {
      $command = 'UPDATE monster_inventory SET location=\'home\',user=' . quote_smart($user['user']) . ',changed=' . $now . ' WHERE idnum=' . $pet['toolid'] . ' LIMIT 1';
      $database->FetchNone($command, 'unequipping pet');
    }

    $knack_count = 4 - $advantages;
    $pet_knacks = array_rand($KNACKS, $knack_count);

    if(!is_array($pet_knacks))
      $pet_knacks = array($pet_knacks);

    $knack_list = array();
      
    foreach($KNACKS as $knack=>$title)
    {
      if(in_array($knack, $pet_knacks))
        $knack_list[] = $knack . '=' . mt_rand(1, mt_rand(1, 3));
      else
        $knack_list[] = $knack . '=0';
    }

    $database->FetchNone('
      UPDATE monster_pets SET
        original=\'yes\',birthday=' . $now . ',prolific=\'yes\',graphic=' . quote_smart($graphic) . ',
        sleeping=\'no\',lycanthrope=\'no\',changed=\'no\',dead=\'no\',last_love=0,pregnant_asof=0,costumed=\'no\',toolid=0,
        energy=15,food=15,love=10,esteem=10,love_exp=0,love_level=0,
        revealed_skills=\'no\',revealed_relationship_preferences=\'no\',revealed_preferences=\'no\',
        special_love=\'no\',special_digital=\'no\',
        nasty_wound=0,healing=0,sneezing=\'no\',eggplant=\'no\',
        ' . $stats . ',
        ' . $merits . ',
        ' . $ascends . ',
        ' . $training . ',
        ' . implode(',', $knack_list) . ',
        free_rename=\'yes\',
        incarnation=incarnation+1
      WHERE idnum=' . $pet['idnum'] . '
      LIMIT 1
    ');

    $badges = get_badges_byuserid($user['idnum']);

    $pet_level = pet_level($pet);
    
    if($badges['reincarnate20'] == 'no' && $pet_level <= 20)
    {
      set_badge($user['idnum'], 'reincarnate20');

      $body = 'You were able to reincarnate a pet in 20 or fewer levels!  Only the most devoted are able to perform such a feat!<br /><br />' .
              '<i>(You earned the World of Gods badge!)</i>';

      psymail_user($user['user'], $SETTINGS['site_ingame_mailer'], 'You reincarnated a level-' . $pet_level . ' pet!', $body);
    }

    if($badges['reincarnate30'] == 'no' && $pet_level <= 30)
    {
      set_badge($user['idnum'], 'reincarnate30');

      $body = 'You were able to reincarnate a pet in 30 or fewer levels!  Not an easy task!<br /><br />' .
              '<i>(You earned the World of Titans badge!)</i>';

      psymail_user($user['user'], $SETTINGS['site_ingame_mailer'], 'You reincarnated a level-' . $pet_level . ' pet!', $body);
    }

    if($badges['reincarnate40'] == 'no' && $pet_level <= 40)
    {
      set_badge($user['idnum'], 'reincarnate40');

      $body = 'You were able to reincarnate a pet in 40 or fewer levels!  An effort worthy of recognition, but surely you can do better!<br /><br />' .
              '<i>(You earned the World of Humans badge!)</i>';

      psymail_user($user['user'], $SETTINGS['site_ingame_mailer'], 'You reincarnated a level-' . $pet_level . ' pet!', $body);
    }

    if($badges['reincarnate50'] == 'no' && $pet_level <= 50)
    {
      set_badge($user['idnum'], 'reincarnate50');

      $body = 'You were able to reincarnate a pet in 50 or fewer levels, but can you do it earlier next time?<br /><br />' .
              '<i>(You earned the World of Animals badge!)</i>';

      psymail_user($user['user'], $SETTINGS['site_ingame_mailer'], 'You reincarnated a level-' . $pet_level . ' pet!', $body);
    }

    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Reincarnated a Pet', 1);

    $database->FetchNone('
      UPDATE monster_users
      SET karma=karma+' . $karma . '
      WHERE idnum=' . $user['idnum'] . '
      LIMIT 1
    ');

    header('Location: /myhouse.php?msg=142:' . $karma . ',168:' . $pet['petname']);
    exit();
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Self-Actualization &gt; <?= $pet['petname'] ?></title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Self-Actualization &gt; <?= $pet['petname'] ?></h4>
<?php
if(count($errors) > 0)
  echo '<ul><li class="failure">' . implode('</li><li class="failure">', $errors) . '</li></ul>';
?>
     <form action="petascend2.php?petid=<?= $pet['idnum'] ?>" method="post">
     <h5>Appearance</h5>
<?php
if($custom_graphic)
  echo '<p>' . $pet['petname'] . ' has a custom graphic, and therefore cannot have its appearance changed during reincarnation.</p>';
else
{
?>
     <p>You may choose a new appearance for your pet, <em>even a breeding-only pet</em>.</p>
     <iframe src="pickpet_reincarnated.php?petid=<?= $petid ?>&sel=<?= $_POST['picture'] ?>" width="250" height="384" style="border: 1px solid black; margin-bottom: 1em;"></iframe>
     <input name="picture" type="hidden" id="picture" value="<?= $_POST['picture'] ?>">
<?php
}
?>
     <h5>Survey</h5>
<?php
$ADVANTAGES_DESC = '<p>You may choose up to 3 special advantages for your pet.  Your pet will also receive a random selection of knacks, talents, or gifts, however the more advantages you choose, the fewer knacks, talents, or gifts the pet will receive.</p>';

require_once 'commons/petsurvey.php';

if($pet['toolid'] > 0)
  echo '<p><i>(The tool ' . $pet['petname'] . ' is using will be dropped into the Common Room of your house.)</i></p>';
?>
     <p><input type="submit" name="submit" value="Reincarnate" class="bigbutton" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
