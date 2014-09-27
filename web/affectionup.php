<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/questlib.php';
require_once 'commons/leveluplib.php';
require_once 'commons/statlib.php';
require_once 'commons/relationshiplib.php';

require_once 'libraries/db_messages.php';

function record_affection_stat_and_exit($user, $pet, $destination = 'petprofile')
{
  if(record_stat_with_badge($user['idnum'], 'Earned a Pet\'s Affection', 1, 901, 'over9000'))
    header('Location: /' . $destination . '.php?petid=' . $pet['idnum'] . '&msg=90:Over 900');
  else
    header('Location: /' . $destination . '.php?petid=' . $pet['idnum']);

  exit();
}

$petid = (int)$_GET['petid'];

$this_pet = get_pet_byid($petid);

if($this_pet['idnum'] == 0 || $this_pet['user'] != $user['user'])
{
  header('Location: /myhouse.php');
  exit();
}

$exp_required = level_exp($this_pet['love_level']);

$can_affection = true;

if($this_pet['zombie'] == 'yes')
{
  $can_affection = false;
  $reason = '<p class="failure">Zombies cannot receive affection!</p>';
}
else if($this_pet['changed'] == 'yes')
{
  $can_affection = false;
  $reason = '<p class="failure">' . $this_pet['petname'] . ' is in wereform, and more interested in tearing things apart than in having a nice conversation.</p>';
}
else if($this_pet['love_exp'] < $exp_required)
{
  $can_affection = false;
  $reason = '<p class="failure">' . $this_pet['petname'] . ' does not have enough affection for you.</p>';
}

$button_texts = array(
  '<3', 'Make It So', 'Alright!', 'Neat-o',
  'Sweet!', ':)',
);

$friends = $database->FetchMultipleBy('
	SELECT
		b.idnum,
		a.rejected,
		a.forbidden,
		a.intimacy,a.passion,a.commitment,
		(a.intimacy+a.passion+a.commitment) AS total_feeling,
		b.user,
		b.petname,
    b.gender,
    b.graphic,b.graphic_flip,
		b.dead,b.changed,b.zombie,b.eggplant
	FROM
		psypets_pet_relationships AS a
		LEFT JOIN monster_pets AS b ON a.friendid=b.idnum
	WHERE
		a.petid=' . $this_pet['idnum'] . '
	ORDER BY total_feeling DESC
', 'idnum');

$allowed_pet_stats = array();

$badges = get_badges_byuserid($user['idnum']);

if($badges['str_trainer'] == 'yes')       $allowed_pet_stats['str'] = 'Strength';
if($badges['dex_trainer'] == 'yes')       $allowed_pet_stats['dex'] = 'Dexterity';
if($badges['sta_trainer'] == 'yes')       $allowed_pet_stats['sta'] = 'Stamina';
if($badges['int_trainer'] == 'yes')       $allowed_pet_stats['int'] = 'Intelligence';
if($badges['wit_trainer'] == 'yes')       $allowed_pet_stats['wit'] = 'Wits';
if($badges['per_trainer'] == 'yes')       $allowed_pet_stats['per'] = 'Perception';
if($badges['athletics_trainer'] == 'yes') $allowed_pet_stats['athletics'] = 'Athletics';
if($badges['stealth_trainer'] == 'yes')   $allowed_pet_stats['stealth'] = 'Stealth';
if($badges['bra_trainer'] == 'yes')       $allowed_pet_stats['bra'] = 'Combat';
if($badges['sur_trainer'] == 'yes')       $allowed_pet_stats['sur'] = 'Survival';
if($badges['gathering_trainer'] == 'yes') $allowed_pet_stats['gathering'] = 'Nature';
if($badges['fishing_trainer'] == 'yes')   $allowed_pet_stats['fishing'] = 'Fishing';
if($badges['mining_trainer'] == 'yes')    $allowed_pet_stats['mining'] = 'Mining';
if($badges['cra_trainer'] == 'yes')       $allowed_pet_stats['cra'] = 'Handicrafts';
if($badges['painting_trainer'] == 'yes')  $allowed_pet_stats['painting'] = 'Painting';
if($badges['carpentry_trainer'] == 'yes') $allowed_pet_stats['carpentry'] = 'Carpentry';
if($badges['jeweling_trainer'] == 'yes')  $allowed_pet_stats['jeweling'] = 'Jeweling';
if($badges['sculpting_trainer'] == 'yes') $allowed_pet_stats['sculpting'] = 'Sculpting';
if($badges['eng_trainer'] == 'yes')       $allowed_pet_stats['eng'] = 'Electronics';
if($badges['mechanics_trainer'] == 'yes') $allowed_pet_stats['mechanics'] = 'Mechanics';
if($badges['chemistry_trainer'] == 'yes') $allowed_pet_stats['chemistry'] = 'Chemistry';
if($badges['smi_trainer'] == 'yes')       $allowed_pet_stats['smi'] = 'Smithing';
if($badges['tai_trainer'] == 'yes')       $allowed_pet_stats['tai'] = 'Tailory';
if($badges['leather_trainer'] == 'yes')   $allowed_pet_stats['leather'] = 'Leather-working';
if($badges['binding_trainer'] == 'yes')   $allowed_pet_stats['binding'] = 'Magic-binding';
if($badges['pil_trainer'] == 'yes')       $allowed_pet_stats['pil'] = 'Piloting';
if($badges['astronomy_trainer'] == 'yes') $allowed_pet_stats['astronomy'] = 'Astronomy';
if($badges['music_trainer'] == 'yes')     $allowed_pet_stats['music'] = 'Music';

if($_POST['confirm'] == 'yes' && $can_affection)
{
  if($_POST['action'] == 'discourage_friends')
  {
    if(array_key_exists($_POST['friend'], $friends))
    {
      $friend = $friends[$_POST['friend']];
    
      $message = $this_pet['petname'] . ' has gained an decreased appreciation for ' . $friend['petname'] . '!';
    
      if(gain_love_level($this_pet, $message))
      {
        save_pet($this_pet, array('love_exp', 'love_level'));
        
        add_db_message($user['idnum'], FLASH_MESSAGE_PET_PROGRESS, $message);

        $intimacy = max(0, $friend['intimacy'] - mt_rand(5, 15));
        $passion = max(0, $friend['passion'] - mt_rand(0, 10));
        $commitment = max(0, $friend['commitment'] - mt_rand(5, 15));

        $database->FetchNone('
          UPDATE psypets_pet_relationships
          SET
            intimacy=' . $intimacy . ',
            passion=' . $passion . ',
            commitment=' . $commitment . '
          WHERE
            petid=' . $this_pet['idnum'] . ' AND
            friendid=' . $friend['idnum'] . '
          LIMIT 1
        ');

        record_affection_stat_and_exit($user, $this_pet);
      }
    }
  }
  else if($_POST['action'] == 'encourage_friends')
  {
    if(array_key_exists($_POST['friend'], $friends))
    {
      $friend = $friends[$_POST['friend']];
    
      $message = $this_pet['petname'] . ' has gained an increased appreciation for ' . $friend['petname'] . '!';
    
      if(gain_love_level($this_pet, $message))
      {
        save_pet($this_pet, array('love_exp', 'love_level'));
        
        add_db_message($user['idnum'], FLASH_MESSAGE_PET_PROGRESS, $message);

        $intimacy = min(100, $friend['intimacy'] + mt_rand(5, 15));
        $passion = min(100, $friend['passion'] + mt_rand(0, 10));
        $commitment = min(100, $friend['commitment'] + mt_rand(5, 15));

        $database->FetchNone('
          UPDATE psypets_pet_relationships
          SET
            intimacy=' . $intimacy . ',
            passion=' . $passion . ',
            commitment=' . $commitment . '
          WHERE
            petid=' . $this_pet['idnum'] . ' AND
            friendid=' . $friend['idnum'] . '
          LIMIT 1
        ');

        record_affection_stat_and_exit($user, $this_pet);
      }
    }
  }
  else if($_POST['action'] == 'prefs')
  {
    if(gain_love_level($this_pet, $this_pet['petname'] . '\'s preferences have been revealed!'))
    {
      $this_pet['revealed_preferences'] = 'yes';

      save_pet($this_pet, array('love_exp', 'love_level', 'revealed_preferences'));

      add_db_message($user['idnum'], FLASH_MESSAGE_PET_PROGRESS, '<span class="success">' . $this_pet['petname'] . '\'s preferences have been revealed!  They can be viewed at any time from ' . p_pronoun($this_pet['gender']) . ' profile.</span>');

      record_affection_stat_and_exit($user, $this_pet);
    }
  }
  else if($_POST['action'] == 'skills')
  {
    if(gain_love_level($this_pet, $this_pet['petname'] . '\'s special skills have been revealed!'))
    {
      $this_pet['revealed_skills'] = 'yes';

      save_pet($this_pet, array('love_exp', 'love_level', 'revealed_skills'));

      add_db_message($user['idnum'], FLASH_MESSAGE_PET_PROGRESS, '<span class="success">' . $this_pet['petname'] . '\'s special skills have been revealed!  They can be viewed at any time from ' . p_pronoun($this_pet['gender']) . ' profile.</span>');

      record_affection_stat_and_exit($user, $this_pet);
    }
  }
  else if($_POST['action'] == 'attractions')
  {
    if(gain_love_level($this_pet, 'What ' . $this_pet['petname'] . ' likes in other pets has been revealed!'))
    {
      $this_pet['revealed_relationship_preferences'] = 'yes';

      save_pet($this_pet, array('love_exp', 'love_level', 'revealed_relationship_preferences'));

      add_db_message($user['idnum'], FLASH_MESSAGE_PET_PROGRESS, 'What ' . $this_pet['petname'] . ' likes in other pets has been revealed!  They can be viewed at any time from ' . p_pronoun($this_pet['gender']) . ' profile.');

      record_affection_stat_and_exit($user, $this_pet);
    }
  }
  else if($_POST['action'] == 'level')
  {
    if(array_key_exists($_POST['stat'], $allowed_pet_stats))
    {
      $desc = $allowed_pet_stats[$_POST['stat']];

      $message = $this_pet['petname'] . '\'s ' . strtolower($desc) . ' increases!';

      if(gain_love_level($this_pet, $message))
      {
        $this_pet[$_POST['stat']]++;

        save_pet($this_pet, array('love_exp', 'love_level', $_POST['stat']));

        add_db_message($user['idnum'], FLASH_MESSAGE_PET_PROGRESS, $message);

        record_affection_stat_and_exit($user, $this_pet, 'petlevelhistory');
      }
    }
  }
  else if($_POST['action'] == 'maxneeds')
  {
    $message = $this_pet['petname'] . '\'s Safety, Love, and Esteem needs were maxed, and ' . he_she($this_pet['gender']) . ' has become Inspired!';
  
    if(gain_love_level($this_pet, $message))
    {
      $safety = max_safety($this_pet);
      $love   = max_love($this_pet);
      $esteem = max_esteem($this_pet);

      $old_food = $this_pet['food'];
      $old_energy = $this_pet['energy'];
      $this_pet['food'] = 1;
      $this_pet['energy'] = 1;

      gain_safety($this_pet, $safety);
      gain_love($this_pet, $love);
      gain_esteem($this_pet, $esteem);

      $this_pet['food'] = $old_food;
      $this_pet['energy'] = $old_energy;
			
			$this_pet['inspired'] = 8;

      save_pet($this_pet, array('safety', 'love', 'esteem', 'inspired', 'love_exp', 'love_level'));

      add_db_message($user['idnum'], FLASH_MESSAGE_PET_PROGRESS, $message);

      record_affection_stat_and_exit($user, $this_pet, 'petlevelhistory');
    }
  }
  else if($_POST['action'] == 'person')
  {
    $allowed_personality_stats = array(
      'extraverted', 'open', 'conscientious', 'playful',
      'independent',
    );

    list($stat, $dir) = take_apart('_', $_POST['personality']);

    if(in_array($stat, $allowed_personality_stats) && ($dir == 'up' || $dir == 'down'))
    {
      if($dir == 'up' && $this_pet[$stat] < 10)
      {
        $desc = array('extraverted' => 'extroverted', 'open' => 'experimental',
          'conscientious' => 'conscientious',
          'playful' => 'playful', 'independent' => 'independent'
        );

        $message = $this_pet['petname'] . ' has become more ' . $desc[$stat] . '!';

        if(gain_love_level($this_pet, $message))
        {
          $this_pet[$stat]++;
          save_pet($this_pet, array('love_exp', 'love_level', $stat));

          add_db_message($user['idnum'], FLASH_MESSAGE_PET_PROGRESS, '<span class="success">' . $message . '</span>');
          
          record_affection_stat_and_exit($user, $this_pet, 'petlevelhistory');
        }
      }
      else if($dir == 'down' && $this_pet[$stat] > 0)
      {
        $desc = array('extraverted' => 'introverted', 'open' => 'traditional',
          'conscientious' => 'laid-back',
          'playful' => 'serious', 'independent' => 'obedient'
        );

        $message = $this_pet['petname'] . ' has become more ' . $desc[$stat] . '!';

        if(gain_love_level($this_pet, $message))
        {
          $this_pet[$stat]--;
          save_pet($this_pet, array('love_exp', 'love_level', $stat));

          add_db_message($user['idnum'], FLASH_MESSAGE_PET_PROGRESS, '<span class="success">' . $message . '</span>');

          record_affection_stat_and_exit($user, $this_pet, 'petlevelhistory');
        }
      }
    }
  }

  $CONTENT['messages'][] = '<span class="failure">Say whaaaaat?</span>';
}

foreach($PET_SKILLS as $skill)
{
  $percent = $this_pet[$skill . '_count'] / level_stat_exp($this_pet[$skill]);
  if($percent >= .9)
    $almost_leveled[] = ucfirst($PET_STAT_DESCRIPTIONS[$skill]) . ' is ' . floor($percent * 100) . '% leveled.';
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Self-Actualization &gt; <?= $this_pet['petname'] ?></title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/petnote1.js"></script>
  <script type="text/javascript">
    $(function() {
      $('input[name="action"]').click(function() {
        var val = $('input[name="action"]:checked').val();

        $('#development div:visible').slideUp(200);
        $('#develop_' + val).slideDown(200);
      });
    });
  </script>
  <style type="text/css">
  #development div
  {
    display: none;
    margin: 10px 0 10px 15px;
  }

  #development label
  {
    font-weight: bold;
  }
  
  #development td,#development td *
  {
    vertical-align: middle;
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
  echo '<li><a href="/petlevelhistory.php?petid=' . $petid . '">Training History</a></li>';

  echo '<li class="activetab"><a href="/affectionup.php?petid=' . $petid . '" class="success">Affection Reward!</a></li>';

  if($this_pet['ascend'] == 'yes')
    echo '<li><a href="/petascend.php?petid=' . $petid . '" class="success">Reincarnate!</a></li>';
  if($this_pet['free_respec'] == 'yes')
    echo '<li><a href="/petrespec.php?petid=' . $petid . '" class="success">Retrain!</a></li>';
}

echo '</ul>';

if($can_affection)
{
?>
<p><span class="failure">Under development!</span>  If there are other things you'd like to be able to do here, definitely let me know!</p>
<form method="post">
<ul class="plainlist" id="development">
<?php
  if($this_pet['revealed_preferences'] == 'no')
  {
?>
 <li style="margin-bottom:5px;">
  <input type="radio" name="action" value="prefs" id="action_prefs" /><label for="action_prefs"> Ask about <?= $this_pet['petname'] ?>'s preferences</label>
  <div id="develop_prefs">
   You will learn about <?= $this_pet['petname'] ?>'s likes and dislikes.
  </div>
 </li>
<?php
  }

  if($this_pet['revealed_relationship_preferences'] == 'no')
  {
?>
 <li style="margin-bottom:5px;">
  <input type="radio" name="action" value="attractions" id="action_attractions" /><label for="action_attractions"> Ask about what <?= $this_pet['petname'] ?> likes in other pets</label>
  <div id="develop_attractions">
   You will learn about <?= $this_pet['petname'] ?>'s preferences when it comes to friendships, and other relationships.
  </div>
 </li>
<?php
  }

  if($this_pet['revealed_skills'] == 'no')
  {
?>
 <li style="margin-bottom:5px;">
  <input type="radio" name="action" value="skills" id="action_skills" /><label for="action_skills"> Discover <?= $this_pet['petname'] ?>'s innate skills</label>
  <div id="develop_skills" style="display:none;margin: 10px 0 10px 15px;">
   <?= $this_pet['petname'] ?>'s innate talents and other skills will be revealed to you.
  </div>
 </li>
<?php
  }
?>
 <li style="margin-bottom:5px;">
  <input type="radio" name="action" value="maxneeds" id="action_maxneeds" /><label for="action_maxneeds"> Comfort and Reassure <?= $this_pet['petname'] ?></label>
  <div id="develop_maxneeds" style="display:none;margin: 10px 0 10px 15px;">
   <?= $this_pet['petname'] ?>'s Safety, Love, and Esteem needs will all be immediately filled by your inspiring words.
  </div>
 </li>
<?php
  if(count($allowed_pet_stats) > 0)
  {
?>
 <li style="margin-bottom:5px;">
  <input type="radio" name="action" value="level" id="action_level" /><label for="action_level"> Encourage <?= $this_pet['petname'] ?>'s skills</label>
  <div id="develop_level">
   <p>You will encourage a specific advancement for <?= $this_pet['petname'] ?>, who will immediately increase <?= his_her($this_pet['gender']) ?> proficiency at the skill of your choice.</p>
   <p>You may only encourage a skill you have extensive experience training.  (You must have the appropriate Trainer badge).</p>
   <select name="stat">
<?php
    asort($allowed_pet_stats);
    foreach($allowed_pet_stats as $stat=>$desc)
      echo '<option type="radio" name="stat" value="' . $stat . '" />' . $desc . '</option>';
?>
   </select>
  </div>
 </li>
<?php
  }
?>
 <li style="margin-bottom:5px;">
  <input type="radio" name="action" value="person" id="action_person" /><label for="action_person"> Encourage <?= $this_pet['petname'] ?>'s behavior</label>
  <div id="develop_person">
   <p>You will influence <?= $this_pet['petname'] ?>'s outlook on life...</p>
   <table>
    <tr>
     <td><?php if($this_pet['conscientious'] < 10) { ?><input type="radio" name="personality" value="conscientious_up" id="conscientious_up"><label for="conscientious_up"> More Conscientious</label><?php } else { ?><input type="radio" name="personality" value="conscientious_up" id="conscientious_up" disabled="disabled"><span class="dim"> More Conscientious</span><?php } ?></td>
     <td><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/ui/spectrum.png" alt="" /></td>
     <td class="righted"><?php if($this_pet['conscientious'] > 0) { ?><label for="conscientious_down">More Laid-back </label><input type="radio" name="personality" value="conscientious_down" id="conscientious_down"><?php } else { ?><span class="dim">More Laid-back </span><input type="radio" name="personality" value="conscientious_down" id="conscientious_down" disabled="disabled"><?php } ?></td>
    </tr>
    <tr>
     <td><?php if($this_pet['open'] < 10) { ?><input type="radio" name="personality" value="open_up" id="open_up"><label for="open_up"> More Experimental</label><?php } else { ?><input type="radio" name="personality" value="open_up" id="open_up" disabled="disabled"><span class="disabled"> More Experimental</span><?php } ?></td>
     <td><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/ui/spectrum.png" alt="" /></td>
     <td class="righted"><?php if($this_pet['open'] > 0) { ?><label for="open_down">More Traditional </label><input type="radio" name="personality" value="open_down" id="open_down"><?php } else { ?><span class="dim">More Traditional </span><input type="radio" name="personality" value="open_down" id="open_down" disabled="disabled"><?php } ?></td>
    </tr>
    <tr>
     <td><?php if($this_pet['extraverted'] < 10) { ?><input type="radio" name="personality" value="extraverted_up" id="extraverted_up"><label for="extraverted_up"> More Extroverted</label><?php } else { ?><input type="radio" name="personality" value="extraverted_up" id="extraverted_up" disabled="disabled"><span class="dim"> More Extroverted</span><?php } ?></td>
     <td><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/ui/spectrum.png" alt="" /></td>
     <td class="righted"><?php if($this_pet['extraverted'] > 0) { ?><label for="extraverted_down">More Introverted </label><input type="radio" name="personality" value="extraverted_down" id="extraverted_down"><?php } else { ?><span class="dim">More Introverted </span><input type="radio" name="personality" value="extraverted_down" id="extraverted_down" disabled="disabled"><?php } ?></td>
    </tr>
    <tr>
     <td><?php if($this_pet['playful'] < 10) { ?><input type="radio" name="personality" value="playful_up" id="playful_up"><label for="playful_up"> More Playful</label><?php } else { ?><input type="radio" name="personality" value="playful_up" id="playful_up" disabled="disabled"><span class="dim"> More Playful</span><?php } ?></td>
     <td><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/ui/spectrum.png" alt="" /></td>
     <td class="righted"><?php if($this_pet['playful'] > 0) { ?><label for="playful_down"> More Serious </label><input type="radio" name="personality" value="playful_down" id="playful_down"><?php } else { ?><span class="dim"> More Serious </span><input type="radio" name="personality" value="playful_down" id="playful_down" disabled="disabled"><?php } ?></td>
    </tr>
    <tr>
     <td><?php if($this_pet['independent'] < 10) { ?><input type="radio" name="personality" value="independent_up" id="independent_up"><label for="independent_up"> More Independent</label><?php } else { ?><input type="radio" name="personality" value="independent_up" id="independent_up" disabled="disabled"><span class="dim"> More Independent</span><?php } ?></td>
     <td><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/ui/spectrum.png" alt="" /></td>
     <td class="righted"><?php if($this_pet['independent'] > 0) { ?><label for="independent_down">More Obedient </label><input type="radio" name="personality" value="independent_down" id="independent_down"><?php } else { ?><span class="dim">More Obedient </span><input type="radio" name="personality" value="independent_down" id="independent_down" disabled="disabled"><?php } ?></td>
    </tr>
   </table>
  </div>
 </li>
<?php
  if(count($friends) > 0)
  {
?>
 <li style="margin-bottom:5px;">
  <input type="radio" name="action" value="encourage_friends" id="action_encourage_friends" /><label for="action_friends"> Encourage a friendship or relationship</label>
  <div id="develop_encourage_friends">
   <table>
    <thead>
     <tr>
      <th></th><th></th><th>Pet</th><th><?= $this_pet['petname'] ?>'s Opinion</th></th>
     </tr>
    </thead>
    <tbody>
<?php
    $rowclass = begin_row_class();

    foreach($friends as $friend)
    {
?>
     <tr class="<?= $rowclass ?>">
      <td><input type="radio" name="friend" value="<?= $friend['idnum'] ?>" /></td>
      <td><?= pet_graphic($friend) ?></td>
      <td><?= $friend['petname'] ?></td>
      <td><?= ucfirst(describe_relationship(0, $friend)) ?></td>
     </tr>
<?php
      $rowclass = alt_row_class($rowclass);
    }
?>
    </tbody>
   </table>
  </div>
 </li>
 <li style="margin-bottom:5px;">
  <input type="radio" name="action" value="discourage_friends" id="action_discourage_friends" /><label for="action_friends"> Discourage a friendship or relationship</label>
  <div id="develop_discourage_friends">
   <table>
    <thead>
     <tr>
      <th></th><th></th><th>Pet</th><th><?= $this_pet['petname'] ?>'s Opinion</th></th>
     </tr>
    </thead>
    <tbody>
<?php
    $rowclass = begin_row_class();

    foreach($friends as $friend)
    {
?>
     <tr class="<?= $rowclass ?>">
      <td><input type="radio" name="friend" value="<?= $friend['idnum'] ?>" /></td>
      <td><?= pet_graphic($friend) ?></td>
      <td><?= $friend['petname'] ?></td>
      <td><?= ucfirst(describe_relationship(0, $friend)) ?></td>
     </tr>
<?php
      $rowclass = alt_row_class($rowclass);
    }
?>
    </tbody>
   </table>
  </div>
 </li>
<?php
  }
?>
</ul>
<input type="hidden" name="confirm" value="yes" />
<p><input type="submit" value="<?= $button_texts[$this_pet['idnum'] % count($button_texts)] ?>" /></p>
</form>
<?php
}
else
  echo $reason;
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
