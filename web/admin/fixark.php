<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/arklib.php';
require_once 'commons/petlib.php';
require_once 'commons/userlib.php';

if($admin['manageitems'] != 'yes')
{
    header('Location: /admin/tools.php');
    exit();
}

$command = 'SELECT * FROM psypets_ark WHERE graphic IN (\'' . implode('\',\'', $ARK_GRAPHICS_EXCLUDED) . '\')';
$bad_donations = $database->FetchMultiple($command, 'fetching bad ark donations');

$removed_by_user = array();

foreach($bad_donations as $pet)
{
  $owner = get_user_byid($pet['userid'], 'idnum,user,display');
  $userids_by_user[$owner['user']] = $pet['userid'];

  if($pet['petid'] > 0)
  {
    $command = 'UPDATE monster_pets SET user=' . quote_smart($owner['user']) . ', location=\'shelter\' WHERE idnum=' . $pet['idnum'] . ' LIMIT 1';
  }
  else
  {
    $petid = create_offspring($owner['user'], 1, array($pet['graphic']), random_blood_type(), random_blood_type(), false);
    $command = 'UPDATE monster_pets SET gender=\'' . $pet['gender'] . '\',location=\'shelter\' WHERE idnum=' . $petid . ' LIMIT 1';
    $database->FetchNone($command, 'correcting created pet\'s gender and location');
  }
  
  $command = 'DELETE FROM psypets_ark WHERE userid=' . $pet['userid'] . ' AND graphic=\'' . $pet['graphic'] . '\' AND gender=\'' . $pet['gender'] . '\' LIMIT 1';
  $database->FetchNone($command, 'deleting bad donation record');

  echo $pet['gender'] . ' ' . $pet['graphic'] . ' from ' . $owner['display'] . ' (' . $owner['user'] . ') has been removed.<br />';
  
  $removed_by_user[$owner['user']]++;
}

if(count($removed_by_user) > 0)
{
  foreach($removed_by_user as $user=>$quantity)
  {
    if($quantity == 1)
      psymail_user($user, 'ark', $quantity . ' pet has been returned to you!', 'The returned pet uses a special, pay-only graphic.  That it was accepted in the first place was a mistake on my part.  I have returned the pet to the <a href="daycare.php">Daycare</a>.');
    else
      psymail_user($user, 'ark', $quantity . ' pets have been returned to you!', 'The returned pets use special, pay-only graphics.  That they were accepted in the first place was a mistake on my part.  I have returned the pets in question to the <a href="daycare.php">Daycare</a>.');

    $userid = $userids_by_user[$user];

    $command = 'UPDATE monster_users SET arkcount=arkcount-' . $quantity . ' WHERE idnum=' . $userid . ' LIMIT 1';
    $database->FetchNone($command, 'updating ark count');
    
    echo '(' . $user . ') has been psymailed about his/her ' . $quantity . ' removals.<br />';
  }
}

echo 'all done!';
?>
