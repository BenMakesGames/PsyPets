<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/moonphase.php';

$mob_tools = array(
  'Adz',
  'Bloodied Scythe',
  'Pitchfork',
  'Rake',
  'Scythe',
  'Spade',
  'Torch',
);
  
echo '<style type="text/css">#content { background: url(gfx/castle.png) no-repeat; }</style>';

$adventurers = array();
$tools = array();
$pet_count = 0;
$total_str = 0;
$total_athletics = 0;
$total_sta = 0;
$total_bra = 0;

foreach($userpets as $pet)
{
  if($pet['dead'] == 'no' && $pet['changed'] == 'no' && $pet['sleeping'] == 'no' && $pet['zombie'] == 'no')
  {
    $pet_count++;
    if($pet['toolid'] > 0)
    {
      $tools[] = $pet['toolid'];
      $item = get_inventory_byid($pet['toolid']);
      $details = get_item_byname($item['itemname']);

      if(in_array($details['itemname'], $mob_tools))
      {
        $total_str += $pet['str'] + $details['equip_str'];
        $total_athletics += $pet['athletics'] + $details['equip_athletics'];
        $total_sta += $pet['sta'] + $details['equip_sta'];
        $total_bra += $pet['bra'] + $details['equip_adventuring'];

        $adventurers[] = $pet;
      }
    }
  }
}

$num_adventurers = count($adventurers);

$step = 1;

if($_GET['step'] == 2 && $num_adventurers >= 5)
{
  $target = get_user_bydisplay($_POST['resident']);

  if($target === false || $target['activated'] == 'no')
    echo 'There is no Resident by the name of "' . $_POST['resident'] . '".</p><p>';
  else if($target['idnum'] == $user['idnum'])
    echo 'You can\'t attack yourself... &gt;_&gt;</p><p>';
  else
  {
    $zombies = $database->FetchMultiple('SELECT idnum FROM monster_pets WHERE user=' . quote_smart($target['user']) . ' AND zombie=\'yes\'');
    
    $num_zombies = count($zombies);
    
    if($num_zombies == 0)
      echo $target['display'] . ' does not have any zombies.</p><p>';
    else
    {
      echo 'You lead a mob of ' . $num_adventurers . ' of your pets to ' . $target['display'] . '\'s house and burst through the front door! ';

      $step = 2;

      $thirds = 2 / 3;
      // 2 + 1.5 + .66 = 4.16
      $total_power = ceil($total_bra + $total_str * .75 + $total_sta * $thirds + $total_athletics * $thirds) - 1;

      // a fuller moon makes zombies easier to kill; a newer moon makes zombies harder
      $total_power *= (moon_phase_power(time()) + 3) / 3; // new moon: 1/3 to full moon: 5/3
      
      $zombies_killed = floor($total_power / 10);

      if($zombies_killed > 0)
      {
        if($zombies_killed > $num_zombies)
        {
          $command = 'SELECT idnum FROM monster_pets WHERE user=' . quote_smart($target['user']) . ' AND zombie=\'yes\' LIMIT ' . count($zombies);
          $zombies_killed = $num_zombies;
        }
        else
          $command = 'SELECT idnum FROM monster_pets WHERE user=' . quote_smart($target['user']) . ' AND zombie=\'yes\' ORDER BY RAND() LIMIT ' . $zombies_killed;

        $ids = $database->FetchMultiple($command, 'fetching zombie ids');

        $killed_ids = array();
        foreach($ids as $id)
          $killed_ids[] = $id['idnum'];

        $database->FetchNone('DELETE FROM monster_pets WHERE idnum IN (' . implode(',', $killed_ids) . ') LIMIT ' . $zombies_killed);
        $database->FetchNone('DELETE FROM psypets_pet_market WHERE petid IN (' . implode(',', $killed_ids) . ') LIMIT ' . $zombies_killed);

        if($zombies_killed == 1)
        {
          if($num_zombies == 1)
          {
            echo 'In the battle that follows, your pets overtake the zombie, destroying it.';
            $zombie_report = 'Your zombie was destroyed in the attack.';
          }
          else
          {
            echo 'In the battle that follows, your pets overtake and destroy a single zombie before being forced to retreat.';
            $zombie_report = 'One of your ' . count($zombies) . ' zombies was destroyed in the attack.';
          }
        }
        else
        {
          if($num_zombies == $zombies_killed)
          {
            echo 'In the battle that follows, your pets overtake the ' . $zombies_killed . ' zombies, destroying them.';
            $zombie_report = 'All ' . $zombies_killed . ' of your zombies were destroyed in the attack.';
          }
          else
          {
            echo 'In the battle that follows, your pets overtake and destroy ' . $zombies_killed . ' zombies before being forced to retreat.';
            $zombie_report = $zombies_killed . ' of your ' . count($zombies) . ' zombies were destroyed in the attack.';
          }
        }
        
        $badges = get_badges_byuserid($user['idnum']);
        if($badges['zombiehunter'] == 'no')
        {
          set_badge($user['idnum'], 'zombiehunter');
          echo '</p><p><i>(You received the Zombie Hunter badge.)</i>';
        }

        psymail_user(
          $target['user'],
          'psypets',
          'A mob stormed your house!',
          'Fearing the presence of the undead, a mob of ' . $num_adventurers . ' pets lead by {r ' . $user['display'] . '} stormed your house!  (It\'s a risk you must accept, when harboring zombies.)<br /><br />' . $zombie_report
        );

        require_once 'commons/statlib.php';
        record_stat($user['idnum'], 'Zombies Lynched', $zombies_killed);
      }
      else
      {
        if($num_zombies == 1)
          echo 'Your mob is unable to overtake the zombie, however, and is forced to retreat.';
        else
          echo 'Your mob is unable to overtake any of the ' . count($zombies) . ' zombies, however, and is forced to retreat.';

        require_once 'commons/statlib.php';
        record_stat($user['idnum'], 'Defeated by Zombies', 1);
      }
      
      $exp_per_pet = ceil(count($adventurers) * 2 / $zombies_killed);
      
      foreach($adventurers as $adventurer)
      {
        add_logged_event($user['idnum'], $adventurer['idnum'], 0, 'realtime', false, '<span class="success">' . $mypet['petname'] . ' took part in a zombie lynching!</span>');

        // 2/3 chance of losing equipped item
        if(mt_rand(1, 3) != 3)
        {
          $database->FetchNone('
            UPDATE monster_inventory
            SET
              itemname=\'Ruins\',
              health=0,
              message=\'This item was broken while lynching zombies\'
            WHERE idnum=' . $adventurer['toolid'] . '
            LIMIT 1
          ');
        }
      }
      
      delete_inventory_byid($this_inventory['idnum']);
      
      $AGAIN_WITH_ANOTHER = true;
    } // have enough adventurers
  }
}

if($step == 1)
{
?>
<p>Zombies are abominations that should be wiped from the surface of the earth!</p>
<p>You may lead a mob of pets against a Resident who houses one or more zombies.  Only pets equipped with certain items - torches and farm equipment, mostly - may be part of a mob (see list below), and you will need at least 5 pets thus equipped.  (Sleeping pets and werepets may not participate.)</p>
<?php
  if($num_adventurers == 0)
    echo '<p>None of your pets can currently join a mob.</p>';
  else if($num_adventurers == 1)
    echo '<p>Only 1 of your pets is ready.</p>';
  else
    echo '<p>' . $num_adventurers . ' of your pets are ready.</p>';

  if($num_adventurers >= 5)
  {
?>
<p>Which Resident will your mob go after?</p>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&amp;step=2" method="post">
<p>Resident: <input name="resident" size="24" maxlength="24" /> <input type="submit" value="Go!" /></p>
</form>
<p><i>(P.S. Residents who tell you not to kill their zombies (and/or whine when you do), should <strong>definitely</strong> be attacked!  Who do they think they are, pretending to have the authority to tell you what you may or may not do?!)</i></p>
<?php
  }
?>
<hr />
<h5>Mob Equipment</h5>
<p>Only a pet equipped with one of the following items may be part of a mob:</p>
<ul>
<?php
  foreach($mob_tools as $tool)
    echo '<li>', item_text_link($tool), '</li>';
?>
</ul>
<?php
}
?>
