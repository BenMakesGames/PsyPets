<?php
if($okay_to_be_here !== true)
  exit();

$participants = array();
$pet_count = 0;
$total_stats = 0;

foreach($userpets as $pet)
{
  if($pet['dead'] == 'no' && $pet['changed'] == 'no' && $pet['sleeping'] == 'no')
  {
    $pet_count++;
    if($pet['toolid'] > 0)
    {
      $item = get_inventory_byid($pet['toolid']);

      switch($item['itemname'])
      {
        case 'Amethyst Rose Garland':
          $participants[] = $pet;
          $total_stats += 2;
          $petids[] = $pet['idnum'];
          $tools[] = $pet['toolid'];
          break;
        case 'Caesar':
          $participants[] = $pet;
          $total_stats += 5;
          $petids[] = $pet['idnum'];
          $tools[] = $pet['toolid'];
          break;
        case 'Clover Wreath':
          $participants[] = $pet;
          $total_stats += 1;
          $petids[] = $pet['idnum'];
          $tools[] = $pet['toolid'];
          break;
        case 'Elfin Pride':
          $participants[] = $pet;
          $total_stats += 5;
          $petids[] = $pet['idnum'];
          $tools[] = $pet['toolid'];
          break;
        case 'Gold Laurel':
          $participants[] = $pet;
          $total_stats += 3;
          $petids[] = $pet['idnum'];
          $tools[] = $pet['toolid'];
          break;
        case 'Poison Ivy Laurel':
          $participants[] = $pet;
          $total_stats += 2;
          $petids[] = $pet['idnum'];
          $tools[] = $pet['toolid'];
          break;
      }
    }
  }
}

if($pet_count == 0)
  echo 'You have no pets that can be rallied to action.  (Sleeping, dead, and pets in wereform cannot be rallied.)';
else if(count($participants) == 0)
  echo 'You attempt to rally your pets to action, but none seem motivated.  (Pets will need to be equipped with a wreath - and not Rome - to participate.)';
else if(count($participants) == 1)
{
  if($petcount == 1)
    echo 'You give a rallying speech.  ' . $adventurers[0]['petname'] . ' seems interested, but has no one else to talk to.';
  else
    echo 'You give a rallying speech.  ' . $adventurers[0]['petname'] . ' seems interested, but hesitates when ' . pronoun($adventurers[0]['gender']) . ' realizes ' . pronoun($adventurers[0]['gender']) . '\'s the only one.';
}
else
{
  if($total_stats > 10)
  {
    $num_participants = count($participants);
  
    $i = 0;
    foreach($participants as $pet)
    {
      if($i > 0)
      {
        if($i == $num_participants - 1)
          $pet_list .= ' and ';
        else
          $pet_list .= ', ';
      }

      $pet_list .= $pet['petname'];

      $i++;
    }

    echo 'You give a rallying speech!  ' . $pet_list . ' are moved to action!</p><p>';

    if($total_stats > 30)
    {
      echo 'They discuss various plans, shooting ideas back and forth in rapid fire.  When it seems like they\'ve hit a snag, one of the pets or another comes up with solution.</p><p>Finally, hours later, they combine their resources to bring their idea to fruition!</p><p>';

      $command = 'UPDATE monster_pets SET toolid=0 WHERE idnum ' . $database->In($petids) . ' LIMIT ' . count($petids);
      $database->FetchNone($command, 'unequipping pets (1)');

      $command = 'DELETE FROM monster_inventory WHERE idnum ' . $database->In($tools) . ' LIMIT ' . count($tools);
      $database->FetchNone($command, 'unequipping pets (2)');

      add_inventory($user['user'], '', 'Rome', 'Created by ' . $pet_list . '.' , 'home');

      echo '<i>(You have received the item Rome!  You can find it in the common room of your house.  The wreaths that ' . $pet_list . ' were wearing were consumed to create it.)</i>';
    }
    else if($total_stats > 20)
      echo 'They seem to brainstorm some really good ideas, however some of the key points are contingent upon some very specific and unlikely circumstances.</p><p>They realize it\'s just not possible in the end, and give up.';
    else
      echo 'They begin to discuss plans - great plans - but then begin to doubt themselves.</p><p>They disperse a few minutes later, accomplishing nothing.';
  }
  else
    echo 'You give a rallying speech.  At first ' . $pet_list . ' seem very excited, but calm down again quickly once you\'re done.';
} // have enough adventurers
?>
