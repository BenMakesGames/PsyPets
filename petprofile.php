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
require_once 'commons/petgraphics.php';
require_once 'commons/relationshiplib.php';

$petid = (int)$_GET['petid'];

$this_pet = $database->FetchSingle('SELECT * FROM `monster_pets` WHERE idnum=' . $petid . ' LIMIT 1');

if($this_pet === false)
{
  header('Location: ./directory.php');
  exit();
}

if($user['idnum'] > 0)
{
  $command = 'SELECT * FROM psypets_profile_pet WHERE petid=' . $this_pet['idnum'] . ' LIMIT 1';
  $profile = $database->FetchSingle($command, 'fetching pet profile text');

  $profile_url = 'residentprofile.php';
}
else
{
  $profile = false;
  $profile_url = 'publicprofile.php';
}

$command = 'SELECT * FROM `monster_users` WHERE `user`=' . quote_smart($this_pet['user']) . ' LIMIT 1';
$owner = $database->FetchSingle($command, 'fetching pet owner');

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
{
  if($this_pet['location'] == 'shelter')
    $where = 'Daycare';
  else
    $where = $owner['display'] . '\'s House';
}

$pet_age = PetAge($this_pet['birthday'], $now);
$pet_years = PetYears($this_pet['birthday'], $now);

if($this_pet['incarnation'] > 1)
{
  $command = 'SELECT * FROM psypets_petlives WHERE petid=' . $this_pet['idnum'] . ' ORDER BY life ASC LIMIT ' . ($this_pet['incarnation'] - 1);
  $past_lives = $database->FetchMultiple($command);
}

$raised_string = '';

if($this_pet['birthedtouser'] > 0)
{
  $raised_by = get_user_byid($this_pet['birthedtouser'], 'display');
  if($raised_by !== false)
    $raised_string .= ' under the care of <a href="residentprofile.php?resident=' . link_safe($raised_by['display']) . '">' . $raised_by['display'] . '</a>';
}

$phoenix_graphics = array('phoenix/firebird_red.png', 'phoenix/firebird_yellow.png', 'phoenix/icebird.png');

$exp_required = level_exp($this_pet['love_level']);

$special_abilities = array();

if($this_pet['special_firebreathing'] == 'yes')
  $special_abilities[] = 'Fire-breathing';

if($this_pet['special_chameleon'] == 'yes')
  $special_abilities[] = 'Chameleon skin';

if($this_pet['special_sparkles'] == 'yes')
  $special_abilities[] = 'Sparkles';

if($this_pet['special_digital'] == 'yes')
  $special_abilities[] = 'Dreams in Digital';

if($this_pet['special_love'] == 'yes')
  $special_abilities[] = 'Doki-doki';

if($this_pet['special_lightning'] == 'yes')
  $special_abilities[] = 'Lightning';

if($this_pet['revealed_skills'] == 'yes')
{
  if($this_pet['merit_acute_senses'] == 'yes')
    $special_abilities[] = 'Acute senses';

  if($this_pet['merit_berserker'] == 'yes')
    $special_abilities[] = 'Berserker';

  if($this_pet['merit_ravenous'] == 'yes')
    $special_abilities[] = 'Bottomless stomach';

  if($this_pet['merit_catlike_balance'] == 'yes')
    $special_abilities[] = 'Cat-like balance';

  if($this_pet['merit_moonkin'] == 'yes')
    $special_abilities[] = 'Child of the Moon';

  if($this_pet['merit_careful_with_equipment'] == 'yes')
    $special_abilities[] = 'Handy';

  if($this_pet['merit_light_sleeper'] == 'yes')
    $special_abilities[] = 'Light sleeper';

  if($this_pet['merit_lightning_calculator'] == 'yes')
    $special_abilities[] = 'Lightning calculator';

  if($this_pet['merit_lucky'] == 'yes')
    $special_abilities[] = 'Luck of the fae';

  if($this_pet['merit_medium'] == 'yes')
    $special_abilities[] = 'Medium';

  if($this_pet['merit_predicts_earthquakes'] == 'yes')
    $special_abilities[] = 'Predicts earthquakes';

  if($this_pet['merit_pruriency'] == 'yes')
    $special_abilities[] = 'Pruriency';

  if($this_pet['merit_silver_tongue'] == 'yes')
    $special_abilities[] = 'Silver tongue';

  if($this_pet['merit_sleep_walker'] == 'yes')
    $special_abilities[] = 'Sleep-walker';

  if($this_pet['merit_transparent'] == 'yes')
    $special_abilities[] = 'Star student';

  if($this_pet['merit_steady_hands'] == 'yes')
    $special_abilities[] = 'Steady hands';

  if($this_pet['merit_tough_hide'] == 'yes')
    $special_abilities[] = 'Tough hide';

  foreach($KNACKS as $knack=>$description)
  {
    $stat = $this_pet[$knack];

    if($stat == 1)
      $special_abilities[] = 'Knack for ' . $description;
    else if($stat == 2)
      $special_abilities[] = 'Talent for ' . $description;
    else if($stat == 3)
      $special_abilities[] = 'Gift for ' . $description;
  }
}
  
$command = 'SELECT COUNT(idnum) AS c FROM `monster_pets` WHERE graphic=\'' . $this_pet['graphic'] . '\'';
$in_game = $database->FetchSingle($command, 'fetching number in-game');

$are_is = ($in_game['c'] == 2 ? 'is' : 'are');
$pets_pet = ($in_game['c'] == 2 ? 'pet' : 'pets');

$i = array_search($this_pet['graphic'], $PET_GRAPHICS);

if($in_game['c'] > 1)
  $count = $in_game['c'] - 1;
else
  $count = 'no';

if($i === false)
  $in_game_text = 'There ' . $are_is . ' ' . $count . ' other ' . $pets_pet . ' with this appearance';
else
  $in_game_text = 'There ' . $are_is . ' <a href="petencyclopedia_owners.php?i=' . ($i + 1) . '">' . $count . '</a> other ' . $pets_pet . ' with this appearance';

if($this_pet['petname'] == '')
  $this_pet['petname'] = '(unnamed pet)';

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
      <li class="activetab"><a href="/petprofile.php?petid=<?= $petid ?>">Summary</a></li>
      <li><a href="/petfamilytree.php?petid=<?= $petid ?>">Family Tree</a></li>
<?php
if($user['user'] == $this_pet['user'] || $user['admin']['clairvoyant'] == 'yes')
  echo '<li><a href="/petlogs.php?petid=' . $petid . '">Activity&nbsp;Logs</a></li> ';

echo '<li><a href="/petevents.php?petid=' . $petid . '">Park&nbsp;Event&nbsp;Logs</a></li> ';

if($user['user'] == $this_pet['user'] || $user['admin']['clairvoyant'] == 'yes')
{
  echo '<li><a href="/petlevelhistory.php?petid=' . $petid . '">Training&nbsp;History</a></li> ';

  if($this_pet['love_exp'] >= $exp_required && $this_pet['zombie'] != 'yes')
    echo '<li><a href="/affectionup.php?petid=' . $petid . '" class="success">Affection&nbsp;Reward!</a></li> ';
  if($this_pet['ascend'] == 'yes')
    echo '<li><a href="/petascend.php?petid=' . $petid . '" class="success">Reincarnate!</a></li> ';
  if($this_pet['free_respec'] == 'yes')
    echo '<li><a href="/petrespec.php?petid=' . $petid . '" class="success">Retrain!</a></li> ';
}

echo '</ul>';

if($user['user'] == $this_pet['user'])
  echo '<ul><li><a href="/myaccount/petprofile.php?petid=' . $this_pet['idnum'] . '">Edit pet\'s profile</a></li></ul>';

echo '<table>';

if($profile !== false && strlen($profile['profile']) > 0)
{
?>
      <tr>
       <td valign="top"><img src="/gfx/speak.gif" width="16" height="16" alt="" /></td>
       <td>
        <p><?= format_text($profile['profile']) ?></p>
       </td>
      </tr>
<?php
}

if($this_pet['revealed_preferences'] == 'yes' || $this_pet['revealed_relationship_preferences'] == 'yes')
{
?>
      <tr>
       <td valign="top"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/emote/loves.gif" width="16" height="16" alt="" /></td>
       <td>
<?php
  if($this_pet['revealed_preferences'] == 'yes')
  {
    if($this_pet['likes_color'] == 'none')
      echo 'Does not have a favorite color.<br />';
    else
      echo 'Favorite color is ' . $this_pet['likes_color'] . '.<br />';

    if($this_pet['likes_flavor'] > 0)
      echo 'Really likes the taste of ' . $FLAVORS[$this_pet['likes_flavor']] . '.<br />';
    else
      echo 'Doesn\'t have a favorite food.<br />';

    if($this_pet['dislikes_flavor'] > 0)
      echo 'Really dislikes the taste of ' . $FLAVORS[$this_pet['dislikes_flavor']] . '.<br />';
    else
      echo 'Doesn\'t dislike any food.<br />';
  }
  
  if($this_pet['revealed_relationship_preferences'] == 'yes')
  {
    if($this_pet['attraction_to_males'] > $this_pet['attraction_to_females'])
    {
      echo ucfirst(preference_description($this_pet['attraction_to_males'])) . ' boys.<br />';
      echo ucfirst(preference_description($this_pet['attraction_to_females'])) . ' girls.<br />';
    }
    else
    {
      echo ucfirst(preference_description($this_pet['attraction_to_females'])) . ' girls.<br />';
      echo ucfirst(preference_description($this_pet['attraction_to_males'])) . ' boys.<br />';
    }
  }
?>
       </td>
      </tr>
<?php
}
?>
      <tr>
       <td valign="top"><img src="/gfx/search.gif" width="16" height="16" alt="" /></td>
       <td>
        <?= $this_pet['petname'] ?> is a <?= $pet_years ?> year-old <?= $this_pet['gender'] . (in_array($this_pet['graphic'], $phoenix_graphics) ? ' <b style="color:#930;">phoenix</b>' : '') ?> in <?= $where ?>.<br />
        Level: <?= $pet_level ?><br />
        Size: <?= (pet_size($this_pet) / 10) ?><br />
        Blood type: <?php
echo say_blood_type($this_pet['bloodtype']);

if($this_pet['bloodtype_revealed'] == 'yes')
  echo ' (genotype ' . $this_pet['bloodtype'] . ')';
?><br /><br />
        <?= $in_game_text ?>.
       </td>
      </tr>
<?php
if(count($special_abilities) > 0)
{
?>
      <tr>
       <td valign="top"><img src="/gfx/goldstar.png" width="16" height="16" alt="" /></td>
       <td>
        <p>Has special abilities!</p>
        <ul><li><?= implode('</li><li>', $special_abilities) ?></li></ul>
       </td>
      </tr>
<?php
}
?>
      <tr>
       <td valign="top"><img src="/gfx/birthday.png" width="16" height="16" alt="" /></td>
       <td>
        Born on <?= local_time($this_pet['birthday'], $user['timezone'], $user['daylightsavings']) . $raised_string ?><br />
        Zodiac sign is <?= $WESTERN_ZODIAC[get_western_zodiac($this_pet['birthday'])] . ' ' . $CHINESE_ZODIAC_EN[get_chinese_zodiac($this_pet['birthday'])] ?>
       </td>
      </tr>
<?php
if($this_pet['incarnation'] > 1)
{
?>
      <tr>
       <td valign="top"><img src="/gfx/ascend.png" width="16" height="16" alt="" /></td>
       <td>
        <p>Has been reincarnated <?= $this_pet['incarnation'] - 1 ?> time<?= $this_pet['incarnation'] == 2 ? '' : 's' ?>.</p>
<?php
  if($past_lives > 0)
  {
    echo '<ol class="plainlist">';

    foreach($past_lives as $life)
		{
			$life_graphic = ($life['graphic'] == '' ? '/gfx/shim.gif' : '/gfx/pets/' . $life['graphic']);
      echo '<li><img src="' . $life_graphic . '" width="24" height="24" alt="" class="inlineimage" /> ' . local_date($life['birthdate'], $user['timezone'], $user['daylightsavings']) . ' - ' . local_date($life['deathdate'], $user['timezone'], $user['daylightsavings']) . '; ascended as a level-' . $life['level'] . ' master ' . $life['mastery'] . '.</li>';
		}

    echo '</ol>';
  }
  
  echo '</td></tr>';
}

$friends = get_pet_friends($this_pet['idnum']);
$num_friends = count($friends);

if($num_friends > 0)
{
  echo '
    <tr>
     <td valign="top"><img src="/gfx/friends.gif" alt="friends" /></td>
     <td>
      <p>Knows ' . $num_friends . ' pet' . ($num_friends != 1 ? 's' : '') . '.</p>
      <ul>
  ';

  foreach($friends as $friend)
  {
    $friend_pet = get_pet_byid($friend['friendid']);
    $friend_feelings = get_pet_relationship($friend_pet, $this_pet);

    echo '<li><a href="/petprofile.php?petid=' . $friend['friendid'] . '">' . $friend_pet['petname'] . '</a> is ' . describe_relationship($this_pet, $friend) . '.  <i class="dim">(Met ' . duration($now - $friend['firstmet'], 2) . ' ago.)</i>';

    if($user['admin']['clairvoyant'] == 'yes')
    {
      echo '<br /><i class="dim">(intimacy: ' . $friend['intimacy'] . ', passion: ' . $friend['passion'] . ', commitment: ' . $friend['commitment'] . ')';
      echo '; sex suggest: ' . round(sex_suggest($this_pet, $friend_pet, $friend, $friend_feelings), 2) . '%, sex agree: ' . round(sex_agree($this_pet, $friend_pet, $friend, $friend_feelings), 2) . '% + ' . round(sex_agree($friend_pet, $this_pet, $friend_feelings, $friend) * ((10 - $this_pet['independent']) / 10), 2) . '%</i>';
    }

    echo '</li>';
  }

  echo '
      </ul>
     </td>
    </tr>
  ';
  
}

echo '</table>';

if($user['admin']['manageaccounts'] == 'yes')
{
  require_once 'commons/sqldumpfunc.php';
?>
<h5>Admin</h5>
<form action="admin_resetpetgraphic.php?id=<?= $this_pet['idnum'] ?>" method="post" onsubmit="return confirm('Really reset this pet graphic to that of a Desikh?');" >
<p><input type="submit" name="action" value="Reset to Desikh" class="bigbutton" /></p>
</form>
<form action="admin_protectpet.php?id=<?= $this_pet['idnum'] ?>" method="post" onsubmit="return confirm('Really change this pet\'s protection state?');" >
<p><input type="submit" name="action" value="<?php echo ($this_pet['protected'] == 'yes' ? 'Unprotect Pet' : 'Protect Pet'); ?>" class="bigbutton" /></p>
</form>
<?php
  dump_sql_results($this_pet);
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
