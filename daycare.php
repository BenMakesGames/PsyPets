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

if($_POST['action'] == 'Pick Up' && is_array($_POST['petid']))
{
  $petids = $_POST['petid'];

  foreach($petids as &$petid)
    $petid = (int)$petid;

  $petids = array_unique($petids);

  if(count($petids) > 0)
  {
    $database->FetchNone('
      UPDATE monster_pets
      SET location=\'home\'
      WHERE
        idnum IN(' . implode(',', $petids) . ')
        AND user=' . quote_smart($user['user']) . '
        AND location=\'shelter\'
      LIMIT ' . count($petids) . '
    ');

    if($database->AffectedRows() > 0)
    {
      require_once 'commons/statlib.php';
      record_stat($user['idnum'], 'Pets Picked Up at the Pet Shelter', $database->AffectedRows());
      
      $dialog = '
        <p>Sure thing, ' . $user['display'] . '!</p>
        <p>Don\'t forget to <a href="/myhouse/arrange_pets.php">arrange your pets</a>, too.</p>
      ';
    }
  }
}

if($_GET['dialog'] == 'cost')
{
  $dialog = '
    <p>No cost!  No limitations!</p>
    <p>If you find yourself with too many pets, leave them with me, and you can pick them up again any time you like.</p>
  ';
}
else
  $options[] = '<a href="?dialog=cost">Ask how much the service will cost, what the limitations will be, etc.</a>';

$sort = (int)$_GET['sort'];

$order_sort = '<a href="?sort=0">&#9651;</a>';
$gender_sort = '<a href="?sort=5">&#9651;</a>';
$name_sort = '<a href="?sort=3">&#9651;</a>';
$level_sort = '<a href="?sort=1">&#9661;</a>';

$LEVEL_SORT_SQL = '(str+dex+sta+per+`int`+wit+bra+athletics+stealth+sur+gathering+fishing+mining+cra+painting+carpentry+jeweling+sculpting+eng+mechanics+chemistry+smi+tai+binding+pil)';

switch($sort)
{
  case 1:
    $sortby = $LEVEL_SORT_SQL . ' DESC,petname ASC';
    $level_sort = '<a href="?sort=2">&#9660;</a>';
    break;

  case 2:
    $sortby = $LEVEL_SORT_SQL . ' ASC,petname ASC';
    $level_sort = '<a href="?sort=1">&#9650;</a>';
    break;

  case 3:
    $sortby = 'petname ASC,' . $LEVEL_SORT_SQL . ' DESC';
    $name_sort = '<a href="?sort=4">&#9650;</a>';
    break;

  case 4:
    $sortby = 'petname DESC,' . $LEVEL_SORT_SQL . ' DESC';
    $name_sort = '<a href="?sort=3">&#9660;</a>';
    break;

  case 5:
    $sortby = 'gender ASC,petname ASC';
    $gender_sort = '<a href="?sort=6">&#9650;</a>';
    break;

  case 6:
    $sortby = 'gender DESC,petname ASC';
    $gender_sort = '<a href="?sort=5">&#9660;</a>';
    break;

  default:
    $sortby = 'orderid ASC';
    $order_sort = '&#9650;';
    $sort = 0;
    break;
}

$daycare_pets = get_pets_byuser($user['user'], 'shelter', $sortby);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Pet Shelter &gt; Daycare</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Pet Shelter &gt; Daycare &gt; Pick Up Pets</h4>
     <ul class="tabbed">
      <li><a href="/petshelter.php">Adopt a Pet</a></li>
      <li class="activetab"><a href="/daycare.php">Daycare</a></li>
      <li><a href="/renameform.php">Rename a Pet</a></li>
      <li><a href="/spayneuter.php">Spay or Neuter a Pet</a></li>
      <li><a href="/giveuppet.php">Give Up a Pet</a></li>
<?php if($user['breeder'] == 'yes') echo '<li><a href="/genetics.php">Genetics Lab</a></li>'; ?>
      <li><a href="/breederslicense.php">Breeder's License</a></li>
     </ul>
     <ul class="tabbed">
      <li class="activetab"><a href="/daycare.php">Pick Up a Pet</a></li>
      <li><a href="/daycare_dropoff.php">Drop Off a Pet</a></li>
     </ul>
<?php
echo '<a href="/npcprofile.php?npc=Kim+Littrell"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/petsheltergirl-2.png" align="right" width="350" height="450" alt="(Kim Littrell)" /></a>';
include 'commons/dialog_open.php';

if($dialog != '')
  echo $dialog;
else
  echo '<p>If you need someone to take care of any of your pets for a little while, feel free to drop them off with me!</p>';

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

if(count($daycare_pets) == 0)
  echo '<p><i>You have no pets in Daycare.</i></p>';
else
{
?>
<form method="post">
<p><input type="submit" name="action" value="Pick Up" /></p>
<table>
 <tr class="titlerow">
  <th></th><th class="centered"><?= $order_sort ?></th><th class="centered"><?= $gender_sort ?></th><th>Name <?= $name_sort ?></th><th>Level <?= $level_sort ?></th>
 </tr>
<?php
  $rowclass = begin_row_class();

  foreach($daycare_pets as $pet)
  {
?>
 <tr class="<?= $rowclass ?>">
  <td><input type="checkbox" name="petid[]" value="<?= $pet['idnum'] ?>" /></td>
  <td><?= pet_graphic($pet) ?></td>
  <td class="centered"><?= gender_graphic($pet['gender'], $pet['prolific']) ?></td>
  <td><a href="/petprofile.php?petid=<?= $pet['idnum'] ?>"><?= $pet['petname'] ?></a></td>
  <td class="centered"><?= pet_level($pet) ?></td>
 </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
</table>
<p><input type="submit" name="action" value="Pick Up" /></p>
</form>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
