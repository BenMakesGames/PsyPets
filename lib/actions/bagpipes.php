<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/questlib.php';

$quest = get_quest_value($user['idnum'], 'Caladcholg');

if($quest !== false)
  echo 'You play with everything you have.</p><p><i>(You already got Caladcholg.)</i>';
else if(substr($this_inventory['location'], 0, 4) != 'home')
  echo 'You play with everything you have, but no one is around to appreciate your song.';
else
{
  $adventurers = array();
  $tools = array();
  $pet_count = 0;
  $total_strength = 0;
  $total_dexterity = 0;
  $total_stamina = 0;
  $total_brawl = 0;

  foreach($userpets as $pet)
  {
    if($pet['dead'] == 'no' && $pet['changed'] == 'no' && $pet['sleeping'] == 'no')
    {
      $pet_count++;
      if($pet['toolid'] > 0)
      {
        $tools[] = $pet['toolid'];
        $item = get_inventory_byid($pet['toolid']);
        $details = get_item_byname($item['itemname']);

        if($details['itemname'] == 'Tam' || $details['itemname'] == 'Tartan Ruana' || $details['itemname'] == 'Kilt')
        {
          $total_str += $pet['str'] + $details['equip_str'];
          $total_dex += $pet['dex'] + $details['equip_dex'];
          $total_sta += $pet['sta'] + $details['equip_sta'];
          $total_bra += $pet['bra'] + $details['equip_adventuring'];

          $adventurers[] = $pet;
        }
      }
    }
  }

  if($pet_count == 0)
    echo 'You play with everything you have, but no one is around to appreciate your song.';
  else if(count($adventurers) == 0)
    echo 'You play with everything you have, but your pets are unmoved.';
  else if(count($adventurers) == 1)
  {
    if($petcount == 1)
      echo 'You play with everything you have.  ' . $adventurers[0]['petname'] . ' seems interested, but lonely.';
    else
      echo 'You play with everything you have.  ' . $adventurers[0]['petname'] . ' seems interested, but hesitant when ' . pronoun($adventurers[0]['gender']) . ' realizes ' . pronoun($adventurers[0]['gender']) . '\'s the only one.';
  }
  else
  {
    $thirds = 2 / 3;
    // 2 + 1.5 + .66 = 4.16
    $total_power = floor($total_bra + $total_str * .75 + $total_sta * $thirds) - 1;

    $i = 0;
    $pet_list = '';
    foreach($adventurers as $pet)
    {
      if($i == count($adventurers) - 1)
        $pet_list .= ' and ';
      else if($i > 0)
        $pet_list .= ', ';

      $pet_list .= $pet['petname'];

      $i++;
    }
    
    if($total_power > 20)
    {
      echo 'You play with everything you have.  ' . $pet_list . ' get excited, and run out into the mountains.</p><p>';

      if($total_power > 60)
      {
        echo 'They return home a little later, somewhat worse for wear, but holding a great prize: Caladcholg!</p><p><i>(Caladcholg has been put into your common room.)</i>';
        add_inventory($user['user'], '', 'Caladcholg', 'Claimed by ' . $pet_list . ' from a defeated Baobhan Sith.' , 'home');
        add_quest_value($user['idnum'], 'Caladcholg', 1);
      }
      else if($total_power > 50)
        echo 'They return home a little later, disappointed by their loss against a powerful Baobhan Sith.';
      else if($total_power > 40)
        echo 'They return home a little later, dejected at their total loss in the face of a Baobhan Sith.';
      else if($total_power > 30)
        echo 'They return home a little later after being worn down by a handful of Wandering Banshees.';
      else
        echo 'They return home a little later, panicky about a strange monster they think they might have seen.';
    }
    else
      echo 'You play with everything you have.  ' . $pet_list . ' get excited and consider running out to adventure in the mountains, but decide against it after considering the strength of the foes they might face compared to their own.';
  } // have enough adventurers

} // at home
?>
