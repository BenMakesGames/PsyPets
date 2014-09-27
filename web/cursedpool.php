<?php
$wiki = 'The_Alchemist\'s';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
require_once 'commons/userlib.php';
require_once 'commons/itemlib.php';
require_once 'commons/questlib.php';
require_once 'commons/favorlib.php';

$free_use = false;

if(time() - $user['signupdate'] > 56 * 24 * 60 * 60)
{
  $questval = get_quest_value($user['idnum'], 'AlchemistQuest');
  if((int)$questval['value'] < 2)
  {
    header('Location: /alchemist_problem.php');
    exit();
  }

  if($questval['value'] == 2 && $user['idnum'] <= 40483)
    $free_use = true;
}

$petid = (int)$_POST['petid'];
$this_pet = get_pet_byid($petid);

if($this_pet['user'] != $user['user'] || $this_pet['location'] != 'home')
{
  header('Location: /alchemist.php?msg=9');
  exit();
}

if($_POST['payment'] == 'items')
{
	$inventory = $database->FetchMultipleBy('
		SELECT COUNT(idnum) AS qty,itemname
		FROM monster_inventory
		WHERE
			`user`=' . quote_smart($user['user']) . '
			AND `location`=\'storage\'
		GROUP BY itemname
	', 'itemname');

	if(($inventory['Astrolabe']['qty'] >= 60 && $inventory['The Smiling Sun']['qty'] >= 30 && $inventory['The Sleeping Moon']['qty'] >= 30) || $free_use)
	{
		if($this_pet['gender'] == 'male')
			$new_gender = 'female';
		else
			$new_gender = 'male';

		if($free_use)
			update_quest_value($questval['idnum'], 3);
		else
		{
			delete_inventory_byname($user['user'], 'Astrolabe',         60, 'storage');
			delete_inventory_byname($user['user'], 'The Smiling Sun',   30, 'storage');
			delete_inventory_byname($user['user'], 'The Sleeping Moon', 30, 'storage');
		}

		require_once 'commons/statlib.php';
		record_stat($user['idnum'], 'Dunked a Pet In the Cursed Pool', 1);

		$database->FetchNone('UPDATE monster_pets SET pregnant_asof=0,gender=' . quote_smart($new_gender) . ' WHERE idnum=' . $this_pet['idnum'] . ' LIMIT 1');

		set_pet_badge($this_pet, 'genderswitcher');
	}
	else
	{
		header('Location: /alchemist_pool.php?msg=70');
		exit();
	}
}
else if($_POST['payment'] == 'favor')
{
	if($user['favor'] >= 250)
	{
		if($this_pet['gender'] == 'male')
			$new_gender = 'female';
		else
			$new_gender = 'male';

    // record the Favor
    spend_favor($user, 250, 'switched a pet\'s gender', 0);

		require_once 'commons/statlib.php';
		record_stat($user['idnum'], 'Dunked a Pet In the Cursed Pool', 1);

		$database->FetchNone('UPDATE monster_pets SET pregnant_asof=0,gender=' . quote_smart($new_gender) . ' WHERE idnum=' . (int)$this_pet['idnum'] . ' LIMIT 1');

		set_pet_badge($this_pet, 'genderswitcher');
	}
	else
	{
		header('Location: /alchemist_pool.php?msg=149:You do not have enough Favor.');
		exit();
	}
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Alchemist's &gt; Cursed Pool</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4><a href="alchemist.php">The Alchemist's</a> &gt; Cursed Pool</h4>
     <ul class="tabbed">
      <li><a href="alchemist.php">Shop</a></li>
      <li class="activetab"><a href="alchemist_pool.php">Cursed Pool</a></li>
     </ul>
     <p><i><?= $this_pet['petname'] ?> wades out into the pool, seemingly unaffected.</i></p>
     <p><i>After a couple minutes The Alchemist calls <?= t_pronoun($this_pet['gender']) ?> back, at which point you realize that "<?= t_pronoun($this_pet['gender']) ?>" is no longer an appropriate pronoun.</i></p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
