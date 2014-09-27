<?php
$whereat = 'petshelter';
$wiki = 'Pet_Shelter';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/petlib.php';
require_once 'commons/userlib.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/economylib.php';

$rename_fee = value_with_inflation(100);

$mypets = $database->FetchMultipleBy('
  SELECT *
  FROM monster_pets
  WHERE
    user=' . quote_smart($user['user']) . '
    AND location=\'home\'
    AND zombie=\'no\'
  ORDER BY orderid ASC
', 'idnum');

if($_POST['action'] == 'rename')
{
  $cost = 0;

  foreach($mypets as $thispet)
  {
    $petname = trim($_POST['p_' . $thispet['idnum']]);

    if($thispet['petname'] != $petname)
    {
      if(strlen($petname) > 32)
      {
        $message .= '<p>The name "' . $petname . '" is too long.  Pet names must be between 1 and 32 characters long.</p>';
        $_POST['p_' . $thispet['idnum']] = $thispet['petname'];
      }
      else if(strlen($petname) < 1)
      {
        $message .= '<p>The name "' . $petname . '" is too short.  Pet names must be between 1 and 32 characters long.</p>';
        $_POST['p_' . $thispet['idnum']] = $thispet['petname'];
      }
      else if(preg_match('/[\0\b\n\r\t><]/', $petname))
      {
        $message .= '<p>The following characters may not be used in pet names: &lt;, &gt;, tab.</p>';
        $_POST['p_' . $thispet['idnum']] = $thispet['petname'];
      }
      else
      {
        $_POST['p_' . $thispet['idnum']] = $petname;

        $num_pets++;

        if($thispet['free_rename'] == 'no')
          $cost += $rename_fee;
      }
    }
  }

  if($num_pets == 0)
  {
    if(strlen($message) < 0)
      $message .= '<p>You didn\'t provide any new names at all...</p>';
  }
  else if($cost > $user['money'])
  {
    $message .= '<p>The total paperwork fee would be ' . $cost . '<span class="money">m</span>, however you only have ' . $user['money'] . '<span class="money">m</span> on hand.</p>';
  }
  else
  {
    foreach($mypets as $id=>$thispet)
    {
      if($thispet['petname'] != $_POST['p_' . $thispet['idnum']])
      {
        $command = 'UPDATE monster_pets SET petname=' . quote_smart($_POST['p_' . $thispet['idnum']]) . ',free_rename=\'no\' WHERE idnum=' . $thispet['idnum'] . ' LIMIT 1';
        $database->FetchNone($command, 'renaming pet');
        
        $mypets[$id]['petname'] = $_POST['p_' . $thispet['idnum']];
        $mypets[$id]['free_rename'] = 'no';
      }
    }

    if($cost > 0)
    {
      $user['money'] -= $cost;
      $pet_count = ($cost / $rename_fee);
      take_money($user, $cost, 'Pet renaming fee for ' . $pet_count . ' pet' . ($pet_count == 1 ? '' : 's'));
    }

    $message .= '<p>I\'ve renamed ' . $num_pets . ' of your pets, for a total of ' . $cost . '<span class="money">m</span>.  If you need anything else, please let me know.</p>';
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Pet Shelter &gt; Rename Your Pet</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Pet Shelter &gt; Rename Your Pet</h4>
     <ul class="tabbed">
      <li><a href="petshelter.php">Adopt a Pet</a></li>
      <li><a href="daycare.php">Daycare</a></li>
      <li class="activetab"><a href="renameform.php">Rename a Pet</a></li>
      <li><a href="spayneuter.php">Spay or Neuter a Pet</a></li>
      <li><a href="giveuppet.php">Give Up a Pet</a></li>
<?php if($user['breeder'] == 'yes') echo '<li><a href="genetics.php">Genetics Lab</a></li>'; ?>
      <li><a href="breederslicense.php">Breeder's License</a></li>
     </ul>
<?php
echo '<a href="npcprofile.php?npc=Kim+Littrell"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/petsheltergirl-2.png" align="right" width="350" height="450" alt="(Kim Littrell)" /></a>';
include 'commons/dialog_open.php';

if(strlen($message) > 0)
  echo $message;
else
{
?>
     <p>There is a <?= $rename_fee ?><span class="money">m</span> paperwork fee associated with renaming pets, however that fee is waived once for newborns.</p>
<?php
  if(count($mypets) == 0)
    echo "<p>You do not seem to have any pets at this time.</p>\n";
}

include 'commons/dialog_close.php';

if(count($mypets) > 0)
{
  $rowclass = begin_row_class();
?>
     <form action="renameform.php" method="post">
     <table>
      <tr class="titlerow">
       <th></th>
       <th></th>
       <th>Pet</th>
       <th>Rename Fee</th>
      </tr>
<?php
  foreach($mypets as $thispet)
  {
?>
      <tr class="<?= $rowclass ?>">
       <td><?= pet_graphic($thispet) ?></td>
       <td><?= gender_graphic($thispet['gender'], $thispet['prolific']) ?></td>
       <td><input name="p_<?= $thispet['idnum'] ?>" value="<?= $thispet['petname'] ?>" /></td>
       <td class="centered"><?= $thispet['free_rename'] == 'yes' ? 'free!' : ($rename_fee . '<span class="money">m</span>') ?></td>
      </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
     </table>
     <p><input type="hidden" name="action" value="rename" /><input type="submit" value="Rename" /></p>
     </form>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
