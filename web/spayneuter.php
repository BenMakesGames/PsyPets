<?php
$whereat = 'petshelter';
$wiki = 'Pet_Shelter';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/petlib.php';
require_once 'commons/messages.php';

$sort_sort = '<a href="spayneuter.php?sort=sort">&#9661;</a>';
$gender_sort = '<a href="spayneuter.php?sort=gender">&#9661;</a>';
$name_sort = '<a href="spayneuter.php?sort=name">&#9661;</a>';

if($_GET['sort'] == 'gender')
{
  $sort = 'gender';
  $gender_sort = '&#9660;';
  $order_by = 'gender';
}
else if($_GET['sort'] == 'name')
{
  $sort = 'name';
  $name_sort = '&#9660;';
  $order_by = 'petname';
}
else
{
  $sort = 'sort';
  $sort_sort = '&#9660;';
  $order_by = 'orderid';
}

if($_POST['action'] == 'fix')
{
  foreach($_POST as $key=>$value)
  {
    if($key{0} == 'p' && $key{1} == '_' && ($value == 'yes' || $value == 'on'))
    {
      $petid = (int)substr($key, 2);
      $pet = get_pet_byid($petid);
  
      if($pet['user'] == $user['user'] && $pet['prolific'] == 'yes' && $pet['dead'] == 'no' && $pet['location'] == 'home')
      {
        if($pet['pregnant_asof'] == 0)
        {
          $command = "UPDATE monster_pets SET prolific='no',love=1,safety=1,esteem=1 WHERE idnum=" . $pet['idnum'] . ' LIMIT 1';
          $database->FetchNone($command, 'snip, snip!');
          
          $messages[] = '<span class="success">' . $pet['petname'] . ' has been fixed.</span>';

          $user['pets_loaded'] = false;
        }
        else
          $messages[] = '<span class="failure">' . $pet['petname'] . ' could not be fixed; ' . pronoun($pet['gender']) . ' is pregnant!</span>';
      }
      else
        $messages[] = '<span class="failure">' . $pet['petname'] . ' could not be fixed.</span>';
    }
  }
}

$command = 'SELECT idnum,graphic,dead,prolific,gender,petname FROM monster_pets WHERE user=' . quote_smart($user['user']) . ' AND prolific=\'yes\' AND location=\'home\' ORDER BY ' . $order_by . ' ASC';
$my_pets = $database->FetchMultiple($command, 'fetching pets for fixing');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Pet Shelter &gt; Spay or Neuter Your Pet</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Pet Shelter &gt; Spay or Neuter Your Pet</h4>
     <ul class="tabbed">
      <li><a href="petshelter.php">Adopt a Pet</a></li>
      <li><a href="daycare.php">Daycare</a></li>
      <li><a href="renameform.php">Rename a Pet</a></li>
      <li class="activetab"><a href="spayneuter.php">Spay or Neuter a Pet</a></li>
      <li><a href="giveuppet.php">Give Up a Pet</a></li>
<?php if($user['breeder'] == 'yes') echo '<li><a href="genetics.php">Genetics Lab</a></li>'; ?>
      <li><a href="breederslicense.php">Breeder's License</a></li>
     </ul>
<?php
echo '<a href="/npcprofile.php?npc=Kim+Littrell"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/petsheltergirl-2.png" align="right" width="350" height="450" alt="(Kim Littrell)" /></a>';
include 'commons/dialog_open.php';
?>
     <p>Spaying and Neutering is free, and recommended if you are not certain that you can care for another pet.  Be careful, though, as it cannot be easily undone.</p>
<?php
include 'commons/dialog_close.php';

if(count($messages) > 0)
  echo '<ul><li>' . implode('</li><li>', $messages) . '</li></ul>';

if(count($my_pets) > 0)
{
  $rowclass = begin_row_class();
?>
     <p>Check each pet you'd like fixed.  <i>(Already-fixed pets are not listed here.)</i></p>
     <form action="spayneuter.php?sort=<?= $sort ?>" method="post">
     <table>
      <tr class="titlerow">
       <th>Fix?</th>
       <th><?= $sort_sort ?></th>
       <th><?= $gender_sort ?></th>
       <th>Pet Name <?= $name_sort ?></th>
      </tr>
<?php
  foreach($my_pets as $thispet)
  {
    if($thispet['dead'] == 'no')
      $input = '<input type="checkbox" name="p_' . $thispet['idnum'] . '" />';
    else
      $input = '<input type="checkbox" disabled />';
?>
      <tr class="<?= $rowclass ?>">
       <td class="centered"><?= $input ?></td>
       <td><?= pet_graphic($thispet) ?></td>
       <td><?= gender_graphic($thispet['gender'], $thispet['prolific']) ?></td>
       <td><?= $thispet['petname'] . ($thispet['dead'] != 'no' ? ' <i class="failure">(dead)</i>' : '') ?></td>
      </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
     </table>
     <p><input type="hidden" name="action" value="fix" /><input type="submit" value="Snip, snip!" /></p>
     </form>
<?php
}
else
  echo '<p>You do not have any pets... (Or at least, none that aren\'t already fixed.)</p>';
?>

<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
