<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';



if($admin['manageaccounts'] != 'yes')
{
  header('Location: /');
  exit();
}

$target_resident = false;

if($_POST['submit'] == 'Reset Pets to Level 4')
{
  $target_resident = get_user_byuser($_POST['user'], 'user,display,idnum');
  
  $pets = $database->FetchMultiple(('SELECT idnum FROM monster_pets WHERE user=' . quote_smart($target_resident['user']));
  
  foreach($pets as $pet)
  {
    $updates = array();
    $stats = array();
    
    foreach($PET_SKILLS as $skill)
      $stats[$skill] = 0;
    
    for($x = 0; $x < 4; ++$x)
      $stats[$PET_SKILLS[array_rand($PET_SKILLS)]]++;
    
    foreach($stats as $stat=>$value)
      $updates[] = '`' . $stat . '`=' . $value;
      
    $database->FetchNone(('UPDATE monster_pets SET ' . implode(',', $updates) . ' WHERE idnum=' . $pet['idnum'] . ' LIMIT 1');
  }

  $pets = $database->FetchMultiple(('SELECT idnum,petname,graphic,dead FROM monster_pets WHERE user=' . quote_smart($target_resident['user']));
  
  $CONTENT['messages'][] = '<span class="success">IT IS DONE!</span>';
}
else if($_POST['submit'] == 'Find')
{
  $target_resident = get_user_bydisplay(trim($_POST['resident']), 'user,display,idnum');
  
  if($target_resident !== false)
  {
    $pets = $database->FetchMultiple(('SELECT idnum,petname,graphic,dead FROM monster_pets WHERE user=' . quote_smart($target_resident['user']));
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &nbsp; Administrative Tools &nbsp; Account Pet Reset</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Account Pet Reset</h4>
<?php
if($error_message)
  echo "<p style=\"color:red;\">" . $error_message . "</p>\n";

if($target_resident !== false)
{
?>
  <form method="post">
  <input type="hidden" name="user" value="<?= $target_resident['user'] ?>" />
  <p><input type="submit" name="submit" value="Reset Pets to Level 4" class="bigbutton" /></p>
  <h5><?= $target_resident['display'] ?>'s Pets</h5>
  <div>
<?php
  foreach($pets as $pet)
  {
?>
  <div style="width:64px; height:72px; text-align:center; float:left;">
  <?= pet_graphic($pet) ?><br />
  <?= $pet['petname'] ?>
  </div>
<?php
  }
  echo '<div style="clear:left;"></div></div>';
}
else
{
  echo '
    <p>Enter the resident name of the account whose pets should be reset.</p>
    <p>The reset will not be applied immediately; you will have a chance to look over the pets.</p>
    <h5>Resident Name</h5>
    <form method="post">
    <p><input type="text" name="resident" /> <input type="submit" name="submit" value="Find" /></p>
    </form>
  ';
}
?>


<?php include "commons/footer_2.php"; ?>
 </body>
</html>
