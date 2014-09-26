<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/petlib.php';
require_once 'commons/grammar.php';

if(count($userpets) == 0)
  echo '<p>You have no pet to give this to.</p>';
else
{
  $petid = (int)$_POST['petid'];

  if($petid > 0)
    $target_pet = get_pet_byid($petid);
  else
    $target_pet = array();

  if($target_pet['user'] != $user['user'] || $target_pet['changed'] == 'yes' || $target_pet['sleeping'] == 'yes' || $target_pet['zombie'] == 'yes' || $target_pet['dead'] != 'no' || $target_pet['location'] != 'home')
  {
?>
<p>Which pet will drink?  <i>(Sleeping pets, dead pets, zombies, and pets in wereform cannot be given a potion.)</i></p>
<?php
  if($this_item['itemname'] == 'Proselytism\'s Broth')
    echo '<p>Note: Pets that drink this potion have to re-earn the option to reincarnate, should they have already earned it.  They will also be unequipped, if equipped.</p>';
?>
 <form action="itemaction.php?idnum=<?= $_GET['idnum'] ?>" method="post">
 <p>
  <select name="petid">
<?php
    foreach($userpets as $this_pet)
    {
      if($this_pet['changed'] == 'yes')
        echo '   <option disabled>' . $this_pet['petname'] . ' (is in wereform)</option>';
      else if($this_pet['sleeping'] == 'yes')
        echo '   <option disabled>' . $this_pet['petname'] . ' (is sleeping)</option>';
      else if($this_pet['dead'] != 'no')
        echo '   <option disabled>' . $this_pet['petname'] . ' (is dead)</option>';
      else if($this_pet['zombie'] == 'yes')
        echo '   <option disabled>' . $this_pet['petname'] . ' (is a zombie)</option>';
      else
        echo '   <option value="' . $this_pet['idnum'] . '">' . $this_pet['petname'] . '</option>';
    }
?>
  </select> <input type="submit" name="submit" value="Give" />
 </p>
 </form>
<?php
  }
  else
  {
    if($this_item['itemname'] == 'Inspiration Draught')
    {
    /*
      $l = $target_pet['level'];
      $target_pet['exp'] += (4 * ($l + 1) * ($l / 4) + 8);

      save_pet($target_pet, array('exp'));
      echo '   <p>A faint light envelops ' . $target_pet['petname'] . ' before fading away with the goblet itself.</p>';

      delete_inventory_byid($this_inventory['idnum']);

      $AGAIN_WITH_ANOTHER = true;
    */
    }
    else if($this_item['itemname'] == 'Proselytism\'s Broth')
    {
      if($target_pet['free_respec'] == 'yes')
      {
        echo '<p>' . $target_pet['petname'] . ' refuses the potion.</p>';
      
        $AGAIN_WITH_SAME = true;
      }
      else
      {
        echo '
          <p>Bubbles form under ' . $target_pet['petname'] . '\'s skin and begin to move around, generally meandering toward the head.</p><p>They do this for a few seconds, gaining in frequency and violence, before suddenly dying down and stopping entirely.</p>
          <p><i>(<a href="petrespec.php?idnum=' . $target_pet['idnum'] . '">' . $target_pet['petname'] . ' may be respec\'d!</a>)</i></p>
        ';

        $command = 'UPDATE monster_pets SET free_respec=\'yes\' WHERE idnum=' . $target_pet['idnum'] . ' LIMIT 1';
        $database->FetchNone($command, 'marking pet for free respec');

        delete_inventory_byid($this_inventory['idnum']);

        $AGAIN_WITH_ANOTHER = true;
      }
    }
    else if($this_item['itemname'] == 'Fertility Draught')
    {
      $command = "UPDATE monster_pets SET gender='female',prolific='yes',pregnant_asof=1,pregnant_by='' WHERE idnum=" . $target_pet['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'making pet a fertile, pregnant female');

      echo '   <p>' . $target_pet['petname'] . ' rises from the floor, gently spinning.  A soft glow surrounds her before she settles back onto the ground.</p>';

      if($target_pet['gender'] == 'male')
      {
        set_pet_badge($target_pet, 'genderswitcher');
        echo '<p>(Yes: "her" and "she"!  ' . $target_pet['petname'] . ' has become a girl!)</p>';
      }

      delete_inventory_byid($this_inventory['idnum']);

      $AGAIN_WITH_ANOTHER = true;
    }
    else if($this_item['itemname'] == 'Werebane Quaff')
    {
      $command = "UPDATE monster_pets SET lycanthrope='no' WHERE idnum=" . $target_pet['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'delycanthroping pet');

      echo '<p>' . $target_pet['petname'] . ' twitches a little, then shakes it off.</p><p><i>(If ' . $target_pet['petname'] . ' had lycanthropy, it is now cured.)</i></p>';

      delete_inventory_byid($this_inventory['idnum']);

      $AGAIN_WITH_ANOTHER = true;
    }
    else if($this_item['itemname'] == 'Cup of Knowledge')
    {
      $database->FetchNone('UPDATE monster_pets SET inspired=12 WHERE idnum=' . $target_pet['idnum'] . ' AND inspired<12 LIMIT 1');

      echo '<p>' . $target_pet['petname'] . ' swallows the entire drink!</p>';

      delete_inventory_byid($this_inventory['idnum']);

      $AGAIN_WITH_ANOTHER = true;
    }
    else if($this_item['itemname'] == 'Sleeping Potion')
    {
      $database->FetchNone('UPDATE monster_pets SET sleeping=\'yes\' WHERE idnum=' . (int)$target_pet['idnum'] . ' LIMIT 1');

      echo '<p>' . $target_pet['petname'] . ' drinks the potion... and immediately passes out!</p>';

      delete_inventory_byid($this_inventory['idnum']);

      $AGAIN_WITH_ANOTHER = true;
    }
    else if($this_item['itemname'] == 'Two Sleeping Potions')
    {
      echo '<p>' . $target_pet['petname'] . ' drinks the potion... and immediately passes out!</p>';

      $database->FetchNone('UPDATE monster_pets SET sleeping=\'yes\' WHERE idnum=' . (int)$target_pet['idnum'] . ' LIMIT 1');
      $database->FetchNone('UPDATE monster_inventory SET itemname=\'Sleeping Potion\' WHERE idnum=' . (int)$this_inventory['idnum'] . ' LIMIT 1');

      $AGAIN_WITH_SAME = true;
      $AGAIN_WITH_ANOTHER = true;
    }
    else if($this_item['itemname'] == 'Incredible Healing Potion')
    {
      echo '<p>' . $target_pet['petname'] . ' drinks the potion...</p>';
    
      if($target_pet['nasty_wound'] > 0)
      {
        $command = 'UPDATE monster_pets SET nasty_wound=0 WHERE idnum=' . $target_pet['idnum'] . ' LIMIT 1';
        $database->FetchNone($command, 'putting pet ... TO SLEEP!');

        echo '<p>... ' . his_her($target_pet['gender']) . ' nasty wound is completely healed!</p>';
      }
      else
        echo '<p>... nothing seems to happen.</p>';

      delete_inventory_byid($this_inventory['idnum']);

      $AGAIN_WITH_ANOTHER = true;
    }
    else
    {
      echo 'eh?<br />';
      exit();
    }
  }
}
?>
