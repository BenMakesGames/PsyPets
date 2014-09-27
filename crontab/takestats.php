<?php
/* records daily statistics
*/

$IGNORE_MAINTENANCE = true;

//ini_set('include_path', '/your/web/root');

require_once 'commons/dbconnect.php';
require_once 'commons/utility.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/userlib.php';
require_once 'commons/petlib.php';
require_once 'commons/itemlib.php';

$now = time();

  // okay, now for the real stuff:
	$object_data = $database->FetchSingle('SELECT COUNT(*) AS qty FROM monster_inventory');

  $timestamp = time();
  $numusers = 0;
  $num_active_users = 0;
  $num_monthly_users = 0;
  $num_weekly_users = 0;
  $numpets = 0;
  $cash = 0;
  $savings = 0;
  $objects = $object_data['qty'];
  $totallevels = 0;
  $maxlevel = 0;
  $malepets = 0;
  $pregnantpets = 0;
  $deadpets = 0;
  $num_posts = 0;
  $lurkers = 0;
  $posters = 0;

	$users = $database->FetchMultiple('SELECT idnum,user,money,savings,lastactivity,daily_posts,daily_threadviews FROM monster_users WHERE is_npc=\'no\'');

  foreach($users as $this_user)
  {
    if($this_user['user'] != 'psypets' && $this_user['user'] != 'broadcasting')
    {
      $numusers++;
      $cash += $this_user['money'];
      $savings += $this_user['savings'];

      if($timestamp - $this_user['lastactivity'] < 60 * 60 * 24)
      {
        $num_active_users++;
        $active_users[$this_user['user']] = true;
      }

      if($timestamp - $this_user['lastactivity'] < 60 * 60 * 24 * 7)
        $num_weekly_users++;

      if($timestamp - $this_user['lastactivity'] < 60 * 60 * 24 * 28)
        $num_monthly_users++;
      
      if($this_user['daily_posts'] > 0)
      {
        $posters++;
        $num_posts += $this_user['daily_posts'];
      }
      
      if($this_user['daily_threadviews'] > 2 && $this_user['daily_posts'] == 0)
        $lurkers++;
    }
  }

	$pets = $database->FetchMultiple('SELECT user,dead,gender,pregnant_asof,`' . implode('`,`', $PET_SKILLS) . '` FROM monster_pets');

  foreach($pets as $this_pet)
  {
    if($this_pet['user'] != 'psypets')
    {
      if($this_pet['dead'] != 'no')
        $deadpets++;
      else
      {
        $pet_level = pet_level($this_pet);
      
        $numpets++;
        $totallevels += $pet_level;

        if($pet_level > $maxlevel)
          $maxlevel = $pet_level;

        $pet_levels[$pet_level]++;

        if($this_pet['gender'] == "male")
          $malepets++;

        if($this_pet['pregnant_asof'] > 0)
          $pregnantpets++;

        // alive and active pets
        if($active_users[$this_pet['user']] === true)
          $activepets++;
      }
    }
  }
  
  $data = $database->FetchSingle('
    SELECT SUM(value) AS total
    FROM psypets_player_stats
    WHERE stat=\'Favor Received from Magic Vouchers\'
  ');

  $voucher_favor = (int)$data['total'];

  if(date('j') == 7)
    $extra = ',take_survey_please=\'yes\'';
  else
    $extra = '';

  $command = 'UPDATE monster_users SET daily_posts=0,daily_threadviews=0' . $extra;
  $database->FetchNone($command, 'clearing forum usage stats');

  $q_comment = $database->Quote(implode("<br />\n", $DAILYCOMMENTS));

  $database->FetchNone('
    INSERT INTO monster_statistics
    (
      `timestamp`,
      `comment`,
      `numusers`,
      `numactiveusers`,
      `nummonthlyusers`,
      `numweeklyusers`,
      `numpets`,
      `numactivepets`,
      `cash`,
      `savings`,
      `voucherfavor`,
      `objects`,
      `totallevels`,
      `maxlevel`,
      `malepets`,
      `deadpets`,
      `pregnantpets`,
      `numlurkers`,
      `numposters`,
      `numposts`
    )
    VALUES
    (
      ' . $timestamp . ',
      ' . $q_comment . ',
      ' . $numusers . ',
      ' . $num_active_users . ',
      ' . $num_monthly_users . ',
      ' . $num_weekly_users . ',
      ' . $numpets . ',
      ' . $activepets . ',
      ' . $cash . ',
      ' . $savings . ',
      ' . $voucher_favor . ',
      ' . $objects . ',
      ' . $totallevels . ',
      ' . $maxlevel . ',
      ' . $malepets . ',
      ' . $deadpets . ',
      ' . $pregnantpets . ',
      ' . $lurkers . ',
      ' . $posters . ',
      ' . $num_posts . '
    )
  ');

  echo 'Finished taking stats.';
?>
