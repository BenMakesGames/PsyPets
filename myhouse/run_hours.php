<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';

if($user['idnum'] == 1)
  $checkpet = 'checkpet';
else
  $checkpet = 'checkpet';

require_once 'commons/' . $checkpet . '.php';

require_once 'commons/houselib.php';
require_once 'commons/questlib.php';

$max_pets = max_active_pets($user, $house);

$house_hours = floor(($now - $house['lasthour']) / (60 * 60));

$can_spend_hours = (count($userpets) <= $max_pets && $house['curbulk'] <= min(max_house_size(), $house['maxbulk']) && $user['no_hours_fool'] == 'no');

if($house_hours > 0)
{
  if($can_spend_hours && (substr($_POST['action'], -3) == 'go!' || $house_hours <= $user['auto_spend_hours']))
  {
    // UPDATE THE PETS
		if($_POST['action'] == '8 hours, go!')
			check_pets($user['idnum'], 8);
		else
			check_pets($user['idnum']);

		load_user_pets($user, $userpets);

    $house = get_house_byuser($user['idnum']);
    $house_hours = floor(($now - $house['lasthour']) / (60 * 60));
  }
  else if($_POST['action'] == 'Skip them!')
  {
    fetch_none('
			UPDATE monster_houses
			SET lasthour=lasthour+' . ($house_hours * 60 * 60) . '
			WHERE idnum=' . $house['idnum'] . '
			LIMIT 1
		');

    require_once 'commons/statlib.php';

    if(record_stat_with_badge($user['idnum'], 'House Hours Skipped', $house_hours, 168, 'ihavealife'))
      $CONTENT['messages'][] = '<span class="success">(You received the I Have a Life, Too Badge!)</span>';

    $house = get_house_byuser($user['idnum']);
    $house_hours = floor(($now - $house['lasthour']) / (60 * 60));
  }
}

header('Location: /myhouse.php');
exit();
?>