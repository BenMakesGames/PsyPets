<?php
$login = 'breederslicense';
$whereat = 'petshelter';
$wiki = 'Pet_Shelter';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
require_once 'commons/questlib.php';
require_once 'commons/economylib.php';

if($user['breeder'] != 'yes')
{
  header('location: ./breederslicense.php');
  exit();
}

$bloodtype_cost = value_with_inflation(200);
$father_cost = value_with_inflation(600); 

$dialog = '<p>With a small blood sample, I can have a scientist at HERG run a few simple genetic tests.  There is a fee, however, to cover the equipment costs.</p>';

if($_POST['submit'] == 'Run Tests')
{
  $reveal_ids = array();
  $father_ids = array();
  $tests = 0;

  foreach($_POST as $key=>$value)
  {
    if($key{0} == 'p' && $key{1} == '_')
    {
      $petid = (int)substr($key, 2);
      
      if($value == 'bloodtype')
      {
        $pet = get_pet_byid($petid, 'user,petname,dead,bloodtype,bloodtype_revealed,location');
        if($pet['user'] == $user['user'] && $pet['dead'] == 'no' && $pet['bloodtype_revealed'] == 'no' && $pet['location'] == 'home')
        {
          $reveal_ids[] = $petid;
          $total_cost += $bloodtype_cost;
          $tests++;
          $results[] = $pet['petname'] . ' has a blood genotype of ' . $pet['bloodtype'];
        }
      }
    }
  }
  
  if($tests > 0)
  {
    if($total_cost > $user['money'])
      $dialog = '<p>The total cost for those tests is ' . $total_cost . '<span class="money">m</span>, but you only have ' . $user['money'] . '<span class="money">m</span> on hand.';
    else
    {
      take_money($user, $total_cost, 'Genetics Lab fees');
      $user['money'] -= $total_cost;
    
      if(count($reveal_ids) > 0)
      {
        $command = 'UPDATE monster_pets SET bloodtype_revealed=\'yes\' WHERE idnum IN (' . implode(', ', $reveal_ids) . ') LIMIT ' . count($reveal_ids);
        $database->FetchNone($command, 'revealing pets\' bloodtypes');
      }
      
      $dialog = '<p>The results are in!  The results will also be visible on your ' . ($tests == 1 ? 'pet\'s profile' : 'pets\' profiles') . '.</p>' .
                '<ul><li>' . implode('</li><li>', $results) . '</li></ul>';

      load_user_pets($user, $userpets);
    }
  }
}
/*
if($_GET['dialog'] == 'pregnancy')
{
}
else
  $options[] = '<a href="genetics.php?dialog=pregnancy">Ask about pet pregnancy</a>';
*/

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Pet Shelter &gt; Genetics Lab</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4>Pet Shelter &gt; Genetics Lab</h4>
     <ul class="tabbed">
      <li><a href="petshelter.php">Adopt a Pet</a></li>
      <li><a href="daycare.php">Daycare</a></li>
      <li><a href="renameform.php">Rename a Pet</a></li>
      <li><a href="spayneuter.php">Spay or Neuter a Pet</a></li>
      <li><a href="giveuppet.php">Give Up a Pet</a></li>
      <li class="activetab"><a href="genetics.php">Genetics Lab</a></li>
      <li><a href="breederslicense.php">Breeder's License</a></li>
     </ul>
<?php
echo '<a href="/npcprofile.php?npc=Kim+Littrell"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/petsheltergirl-2.png" align="right" width="350" height="450" alt="(Kim Littrell)" /></a>';
include 'commons/dialog_open.php';

echo $dialog;

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

echo '<h4>Test</h4>';

$pets_listed = 0;
$row_class = begin_row_class();

foreach($userpets as $pet)
{
  $test_options = array();

  if($pet['dead'] == 'no')
  {
    if($pet['bloodtype_revealed'] == 'no')
      $test_options[] = '<option value="bloodtype">Bloodtype Test (' . $bloodtype_cost . 'm)</option>';
  }
  
  if(count($test_options) > 0)
  {
    if($pets_listed == 0)
      echo '<form action="genetics.php" method="post">' .
           '<table><tr class="titlerow"><th></th><th>Pet</th><th>Options</th></tr>';
?>
<tr class="<?= $row_class ?>">
 <td><a href="/petprofile.php?petid=<?= $pet['idnum'] ?>"><?= pet_graphic($pet) ?></a></td>
 <td><?= $pet['petname'] ?></td>
 <td><select name="p_<?= $pet['idnum'] ?>">
  <option value=""></option>
<?php
    foreach($test_options as $option)
      echo $option;
?>
 </select></td>
</tr>
<?php    
    $pets_listed++;
    $row_class = alt_row_class($row_class);
  }
}

if($pets_listed > 0)
  echo '</table><p><input type="submit" name="submit" value="Run Tests" /></p></form>';
else
  echo '<p>There are no tests that can be run on any of your pets.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
