<?php
$whereat = "petshelter";
$wiki = "Pet_Shelter";
$require_petload = 'yes';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
 
if($_POST['action'] == 'giveup')
{
  foreach($_POST as $key=>$value)
  {
    if($key{0} == 'p' && $key{1} == '_' && ($value == 'yes' || $value == 'on'))
    {
      $petid = (int)substr($key, 2);
      $pet = get_pet_byid($petid);

      if($pet === false || $pet['user'] != $user['user'] || $pet['location'] != 'home')
        ;
      else if($pet['dead'] != 'no' || $pet['zombie'] == 'yes')
        $messages[] = '<span class="failure">' . $pet['petname'] . ' is dead.  I can\'t accept dead pets, obviously...</span>';
      else if($pet['protected'] == 'no')
      {
        $database->FetchNone('
					DELETE FROM psypets_pet_market
					WHERE petid=' . $petid . '
				');

        if($pet['toolid'] > 0)
        {
          $command = "UPDATE monster_inventory SET location='home',user=" . quote_smart($user['user']) . ",changed='" . $now . "' WHERE idnum=" . $pet['toolid'] . ' LIMIT 1';
          $database->FetchNone($command, 'unequipping pet (tool)');
        }

        if($pet['keyid'] > 0)
        {
          $command = "UPDATE monster_inventory SET location='home',user=" . quote_smart($user['user']) . ",changed='" . $now . "' WHERE idnum=" . $pet['keyid'] . ' LIMIT 1';
          $database->FetchNone($command, 'unequipping pet (key)');
        }

        $command = 'UPDATE monster_pets SET ' .
                   'last_check=' . ($now + 60 * 60 * 24 * rand(13, 17)) . ', toolid=0,keyid=0, ' .
                   'user=\'psypets\', prolific=\'no\', pregnant_asof=0, ' .
                   'history=' . quote_smart('given up by ' . $user['display'] . ' (' . $user['user'] . ')') . ' ' .
                   'WHERE idnum=' . $pet['idnum'] . ' AND user=' . quote_smart($user['user']) . ' LIMIT 1';
        $database->FetchNone($command, 'giving up pet');

        $user['pets_loaded'] = false;

        $messages[] = '<span class="success">' . $pet['petname'] . ' has been given up.</span>';

        require_once 'commons/statlib.php';
        record_stat($user['idnum'], 'Gave a Pet to the Pet Shelter', 1);
      }
      else
      {
        $messages[] = '<span class="failure">I\'m afraid I can\'t accept ' . $pet['petname'] . '</span>';
      }
    }
  }

  load_user_pets($user, $userpets);
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Pet Shelter &gt; Give Pet to Shelter</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Pet Shelter &gt; Give Pet to Shelter</h4>
     <ul class="tabbed">
      <li><a href="petshelter.php">Adopt a Pet</a></li>
      <li><a href="daycare.php">Daycare</a></li>
      <li><a href="renameform.php">Rename a Pet</a></li>
      <li><a href="spayneuter.php">Spay or Neuter a Pet</a></li>
      <li class="activetab"><a href="giveuppet.php">Give Up a Pet</a></li>
<?php if($user['breeder'] == 'yes') echo '<li><a href="genetics.php">Genetics Lab</a></li>'; ?>
      <li><a href="breederslicense.php">Breeder's License</a></li>
     </ul>
<?php
if(strlen($error_message) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

$giveupable = 0;

foreach($userpets as $pet)
  if($pet['protected'] == 'no' && $pet['dead'] == 'no') $giveupable++;

echo '<a href="/npcprofile.php?npc=Kim+Littrell"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/petsheltergirl-2.png" align="right" width="350" height="450" alt="(Kim Littrell)" /></a>';
include 'commons/dialog_open.php';

if($error_message)
  echo "<p>$error_message</p>";
else
{
  if($giveupable > 0)
  {
?>
     <p>Of course we're sorry to see a pet have to leave the comfort of home, but if you can't properly care for your pet, then this <em>is</em> the right decision.  Don't worry: I'm sure a new Resident will come to give him or her a new, happy home, and in the meanwhile Pet Shelter volunteers will come to care for and play with your pet, and all the pets here.</p>
     <p>We have to do some tests and related paperwork before we can put the pet up for adoption, so it may take about two weeks before the pets you give us get listed.</p>
<?php
  }
  else
    echo '<p>Ah... you don\'t seem to have any pets that can be given up at the moment.</p>';

    
  if(count($userpets) > $giveupable)
    echo '<p>The pet you received when you joined PsyPets cannot be given up.  I\'m also unable to take pets which have <a href="/af_custompetgraphic2.php">been given custom appearances</a> - not that you\'d <em>want</em> to give up such a pet!</p>';
}

include 'commons/dialog_close.php';

if(count($messages) > 0)
  echo '<ul><li>' . implode('</li><li>', $messages) . '</li></ul>';

if($user['breeder'] == 'yes')
  echo '<p>If you give up a pet which is listed in the Pet Market, it will be unlisted from the Pet Market.</p>';

if($giveupable > 0)
{
  $rowclass = begin_row_class();
?>
     <p>Check each pet you'd like to give up.</p>
     <form method="post">
     <table>
      <tr class="titlerow">
       <th>Give Up?</th>
       <th></th>
       <th></th>
       <th>Pet</th>
      </tr>
<?php
  foreach($userpets as $thispet)
  {
    if($thispet['dead'] == 'no' && $thispet['zombie'] == 'no' && $thispet['protected'] == 'no')
      $input = '<input type="checkbox" name="p_' . $thispet['idnum'] . '" />';
    else
      $input = '<input type="checkbox" disabled />';
?>
      <tr class="<?= $rowclass ?>">
       <td class="centered"><?= $input ?></td>
       <td><?= pet_graphic($thispet) ?></td>
       <td><?= gender_graphic($thispet['gender'], $thispet['prolific']) ?></td>
       <td><?= $thispet['petname'] ?></td>
      </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
     </table>
     <p><input type="hidden" name="action" value="giveup" /><input type="submit" value="Give Up" onclick="return confirm('Really give up the selected pets?  Really-really?');" /></p>
     </form>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
