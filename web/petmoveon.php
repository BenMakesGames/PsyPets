<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/encryption.php';
require_once 'commons/formatting.php';
require_once 'commons/moonphase.php';

if(count($userpets) == 0)
{
  header('Location: /myhouse.php');
  exit();
}

$petid = (int)$_GET['petid'];

$pet = get_pet_byid($petid);

if($pet['user'] != $user['user'] || $pet['dead'] == 'no')
{
  header('Location: /myhouse.php');
  exit();
}

if($_POST['submit'] == 'Move On')
{
  $command = 'UPDATE monster_pets SET user=\'graveyard\' WHERE idnum=' . $petid . ' LIMIT 1';
  $database->FetchNone($command, 'deleting pet');

  $command = 'DELETE FROM psypets_pet_market WHERE petid=' . $petid . ' LIMIT 1';
  $database->FetchNone($command, 'deleting pet market listing, if one exists');

  // protected pets always get an urn
  if($pet['protected'] == 'no')
  {
    if(rand() % 2 == 0)
    {
      $where = 'home';

      $pet['movedon'] = true;

      if($pet['graphic'] == 'mushroom.gif')
      {
        $command = "INSERT INTO `monster_inventory` " .
                   "(`user`, `itemname`, `message`, `location`) " .
                   "VALUES " .
                   '(' . quote_smart($user['user']) . ", 'Mushroom', " . quote_smart($pet['petname'] . "'s remains") . ", 'home')";
      }
      else if($pet['graphic'] == 'broccoli.gif')
      {
        $command = 'INSERT INTO `monster_inventory` ' .
                   '(`user`, `itemname`, `message`, `location`) ' .
                   'VALUES ' .
                   '(' . quote_smart($user['user']) . ", 'Broccoli', " . quote_smart($pet['petname'] . "'s remains") . ", 'home')";
      }
      else if($pet["graphic"] == "chicken.png" || $pet["graphic"] == "chicken_red.png" || $pet["graphic"] == "chicken_blue.png" || $pet["graphic"] == "chickie.gif" || $pet["graphic"] == "rooster_not_ostrich.gif")
      {
        $command = 'INSERT INTO `monster_inventory` ' .
                   '(`user`, `itemname`, `message`, `location`) ' .
                   'VALUES ' .
                   '(' . quote_smart($user['user']) . ", 'Chicken', " . quote_smart($pet['petname'] . "'s remains") . ", 'home')";
      }
      else if($pet['graphic'] == 'ba-ha.gif')
      {
        $command = 'INSERT INTO `monster_inventory` ' .
                   '(`user`, `itemname`, `message`, `location`) ' .
                   'VALUES ' .
                   '(' . quote_smart($user['user']) . ", 'Fish', " . quote_smart($pet['petname'] . "'s remains") . ", 'home')";
      }
      else
      {
        $command = 'INSERT INTO `monster_inventory` ' .
                   '(`user`, `itemname`, `message`, `location`) ' .
                   'VALUES ' .
                   '(' . quote_smart($user['user']) . ", 'Urn', " . quote_smart($pet['petname'] . "'s ashes") . ", 'home')";
      }
    }
  }
  else
  {
    $command = 'INSERT INTO `monster_inventory` ' .
               '(`user`, `itemname`, `message`, `location`) ' .
               'VALUES ' .
               '(' . quote_smart($user['user']) . ", 'Urn', " . quote_smart($pet['petname'] . "'s ashes") . ", 'home')";
  }

  if(strlen($command) > 0)
  {
    $database->FetchNone($command, 'adding food item >_>');
  }

  if($pet['toolid'] > 0)
    $database->FetchNone('UPDATE monster_inventory SET location=\'home\',changed=' . time() . ',user=' . quote_smart($user['user']) . ' WHERE idnum=' . $pet['toolid'] . ' LIMIT 1');

  if($pet['keyid'] > 0)
    $database->FetchNone('UPDATE monster_inventory SET location=\'home\',changed=' . time() . ',user=' . quote_smart($user['user']) . ' WHERE idnum=' . $pet['keyid'] . ' LIMIT 1');

  $ghost = (mt_rand(1, 512) == 1 ? 'yes' : 'no');

  $command = '
    INSERT INTO psypets_graveyard (`locid`, `ownerid`, `timestamp`, `tombstone`, `petname`, `petid`, `ghost`)
    VALUES (
      ' . $pet['locid'] . ',
      ' . $user['idnum'] . ',
      ' . time() . ',
      ' . ($pet['idnum'] % 4 + 1) . ',
      ' . quote_smart($pet['petname']) . ',
      ' . $petid . ',
      ' . quote_smart($ghost) . '
    )
  ';

  $database->FetchNone($command, 'adding graveyard entry');

  $id = $database->InsertID();

  $loc = 'editepitaph.php?id=' . $id;

  require_once 'commons/statlib.php';
  record_stat($user['idnum'], '"Move On"\'d a Pet', 1);

  header('Location: ./editepitaph.php?id=' . $id);
  exit();
}
else if($_POST['submit'] == 'Reincarnate')
{
  if($pet['toolid'] > 0)
    $database->FetchNone('UPDATE monster_inventory SET location=\'home\',changed=' . time() . ',user=' . quote_smart($user['user']) . ' WHERE idnum=' . $pet['toolid'] . ' LIMIT 1');

  if($pet['keyid'] > 0)
    $database->FetchNone('UPDATE monster_inventory SET location=\'home\',changed=' . time() . ',user=' . quote_smart($user['user']) . ' WHERE idnum=' . $pet['keyid'] . ' LIMIT 1');

  $stats = array(
    'extraverted' => mt_rand(2, 8),
    'open' => mt_rand(2, 8),
    'conscientious' => mt_rand(2, 8),
    'playful' => mt_rand(2, 8),
    'independent' => mt_rand(2, 8),
    'energy' => 6,
    'food' => 12,
    'safety' => 12,
    'love' => 12,
    'esteem' => 12,
    'love_exp' => 0,
    'love_level' => 0,
    'revealed_skills' => 'no',
    'revealed_relationship_preferences' => 'no',
    'revealed_preferences' => 'no',
    'birthday' => time(),
    'nasty_wound' => 0,
    'healing' => 0,
    'lycanthrope' => 'no',
    'changed' => 'no',
    'eggplant' => 'no',
    'dead' => 'no',
    'pregnant_asof' => 0,
    'pregnant_by' => '',
    'toolid' => 0,
    'keyid' => 0,
    'costumed' => 0,
    'sleeping' => 'no',
    'likes_color' => $COLORS[array_rand($COLORS)],
    'merit_moonkin' => (mt_rand(1, 100) <= moon_phase_power(time()) ? 'yes' : 'no'),
  );
  
  foreach($ASCEND_STATS as $stat)
    $stats[$stat] = 'no';
    
  foreach($PET_SKILLS as $stat)
  {
    $stats[$stat] = 0;
    $stats[$stat . '_count'] = 0;
  }

  list($likes_flavor, $dislikes_flavor) = array_rand($FLAVORS, 2);

  $knack_count = mt_rand(1, 3);
  $pet_knacks = array_rand($KNACKS, $knack_count);

  if(!is_array($pet_knacks))
    $pet_knacks = array($pet_knacks);

  $knack_values = array();

  $petgender = mt_rand(1, 2) == 1 ? 'male' : 'female';
  
  foreach($KNACKS as $knack=>$name)
    $stats[$knack] = 0;
  
  foreach($pet_knacks as $knack)
    $stats[$knack] = mt_rand(1, mt_rand(1, 3));

  if($petgender == 'male')
	{
		$attracted_to_males = mt_rand(0, mt_rand(0, 100));
		$attracted_to_females = mt_rand(mt_rand(0, 50), mt_rand(50, 100));
	}
	else if($petgender == 'female')
	{
		$attracted_to_males = mt_rand(mt_rand(0, 50), mt_rand(50, 100));
		$attracted_to_females = mt_rand(0, mt_rand(0, 100));
	}

  $sets = array();
  
  foreach($stats as $stat=>$value)
    $sets[] = '`' . $stat . '`=' . quote_smart($value);
  
  $database->FetchNone('
    UPDATE monster_pets
    SET
      ' . implode(',', $sets) . ',
      gender=' . quote_smart($petgender) . ',
      attraction_to_males=' . $attracted_to_males . ',
      attraction_to_females=' . $attracted_to_females . ',
      likes_flavor=' . quote_smart($likes_flavor) . ',
      dislikes_flavor=' . quote_smart($dislikes_flavor) . '
    WHERE idnum=' . $pet['idnum'] . '
    LIMIT 1
  ');

  $database->FetchNone('
    INSERT INTO psypets_pet_level_logs
    (timestamp, petid, answer)
    VALUES
    (
      ' . time() . ',
      ' . $pet['idnum'] . ',
      ' . quote_smart($pet['petname'] . ' was reincarnated!') . '
    )
  ');
  
  header('Location: /myhouse.php');
  exit();
}

  $cause_of_death = array(
    'starved' => 'starved to death',
    'magical' => 'transported to the elemental plane of negative energy',
    'bonestaff' => 'striken down by powerful magics',
  );

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Pet Death</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Pet Death &gt; <?= $pet['petname'] ?></h4>
     <p><strong><?= $pet['petname'] ?> has died, <?= $cause_of_death[$pet['dead']] ?>.</strong></p>
     <p><?= ucfirst(pronoun($pet['gender'])) ?> can continue as a ghost, however there may not be much left for <?= t_pronoun($pet['gender']) ?> to do.</p>
     <p>There do exist powerful magics which can bring pets back to the world of the living, however these are exceedingly rare and hard to come by.</p>
     <p>Alternatively, <?= $pet['petname'] ?> may be allowed to move on, or be reincarnated...</p>
     <h5>Moving On</h5>
     <p><?= $pet['petname'] ?> will be gone forever, and a tombstone will be created for <?= him_her($pet['gender']) ?> in <a href="/graveyard.php">The Graveyard</a>.</p>
     <h5>Reincarnation</h5>
     <p><?= $pet['petname'] ?> will be reborn.  <?= ucfirst(his_her($pet['gender'])) ?> gender, skills, and even personality will be different.  In most respects, <?= $pet['petname'] ?> will be an entirely new pet.</p>
     <p><i>(<?= ucfirst(his_her($pet['gender'])) ?> name, appearance, fixed/unfixedness, and family tree will remain unchanged, however.)</i></p>
     <h5>Which Will It Be?</h5>
     <form method="post">
     <p><input type="submit" name="submit" value="Move On" class="bigbutton" /> <input type="submit" name="submit" value="Reincarnate" class="bigbutton" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
