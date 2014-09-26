<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/questlib.php';

if(substr($this_inventory['location'], 0, 4) != 'home')
  echo 'You look into the vase.  In the water\'s reflection you see your house.';
else
{
  $equips = array();

  foreach($userpets as $pet)
  {
    if($pet['dead'] == 'no' && $pet['changed'] == 'no' && $pet['sleeping'] == 'no')
    {
      $pet_count++;
      if($pet['toolid'] > 0)
      {
        $item = get_inventory_byid($pet['toolid']);
        $details = get_item_byname($item['itemname']);

        if($details['itemname'] == 'Mars')
          $equips['mars'] = $pet;
        else if($details['itemname'] == 'Jupiter')
          $equips['jupiter'] = $pet;
        else if($details['itemname'] == 'Venus')
          $equips['venus'] = $pet;
        else if($details['itemname'] == 'Mercury')
          $equips['mercury'] = $pet;
        else if($details['itemname'] == 'Saturn')
          $equips['saturn'] = $pet;
        else if($details['itemname'] == 'Neptune')
          $equips['neptune'] = $pet;
        else if($details['itemname'] == 'Uranus')
          $equips['uranus'] = $pet;
      }
    }
  }

  $pet_count = count($equips);

  if($pet_count == 0)
  {
    echo 'You look into the vase.  In the water\'s reflection you see the entire solar system spread out before you, eight planets meandering around the Sun.';
  }
  else
  {
    echo 'You look into the vase.  In the water\'s reflection ';
    
    $i = 0;
    $pet_list = '';
    foreach($equips as $planet=>$pet)
    {
      if($i > 0)
      {
        if($i == $pet_count - 1)
          $pet_list .= ' and ';
        else
          $pet_list .= ', ';
      }

      $pet_list .= $pet['petname'];

      $i++;
    }
    
    echo $pet_list . ' ' . ($pet_count > 1 ? 'are arranged, ' : 'is ') . ' looking back at you.  ';
    
    if($pet_count < 7)
    {
      echo 'Something seems missing, however.  ';

      if($pet_count == 7)
        echo 'Just one more should be enough...';
      else if($pet_count == 6)
        echo 'A couple more should do it...';
      else if($pet_count >= 4)
        echo 'A few more are needed...';
      else if($pet_count >= 2)
        echo 'There are far too few...';
      else
        echo 'One alone cannot possibly do it...';
    }
    else
    {
      echo 'Mercury, Venus, Mars, Jupiter, Saturn, Uranus, Neptune... aligned, they form a path through heaven.</p>' .
           '<p>There is a sudden light from the deepest reaches of the void which fixates itself on this newfound path.  ';

      $health = 21;
      
      echo 'It crashes toward the solar system, ';

      $destroyed = array();

      require_once 'commons/statlib.php';

      $health -= $eqips['neptune']['int'];
      if($health > 0)
      {
        echo 'through Neptune... ';
        $destroyed[] = 'neptune';

        record_stat($user['idnum'], 'Destroyed Neptune', 1);

        $health -= $equips['uranus']['int'];
        if($health > 0)
        {
          echo 'through Uranus... ';
          $destroyed[] = 'uranus';

          record_stat($user['idnum'], 'Destroyed Uranus', 1);

          $health -= $equips['saturn']['int'];
          if($health > 0)
          {
            echo 'through Saturn... ';
            $destroyed[] = 'saturn';

            record_stat($user['idnum'], 'Destroyed Saturn', 1);

            $health -= $equips['jupiter']['int'];
            if($health > 0)
            {
              echo 'through Jupiter... ';
              $destroyed[] = 'jupiter';

              record_stat($user['idnum'], 'Destroyed Jupiter', 1);

              $health -= $equips['mars']['int'];
              if($health > 0)
              {
                echo 'through Mars... past the Earth... ';
                $destroyed[] = 'mars';

                record_stat($user['idnum'], 'Destroyed Mars', 1);

                $health -= $equips['venus']['int'];
                if($health > 0)
                {
                  echo 'through Venus... ';
                  $destroyed[] = 'venus';

                  record_stat($user['idnum'], 'Destroyed Venus', 1);

                  $health -= $equips['mercury']['int'];
                  if($health > 0)
                  {
                    echo 'through Mercury... ';
                    $destroyed[] = 'mercury';

                    record_stat($user['idnum'], 'Destroyed Mercury', 1);

                    $caught = 'sun';
                  }
                  else
                    $caught = 'mercury';
                }
                else
                  $caught = 'venus';
              }
              else
                $caught = 'mars';
            }
            else
              $caught = 'jupiter';
          }
          else
            $caught = 'saturn';
        }
        else
          $caught = 'uranus';
      }
      else
        $caught = 'neptune';

      if($caught === false)
      {
        $location = 'the Sun';
        $maker = 'u:' . $user['idnum'];
      }
      else
      {
        $location = ucfirst($caught);
        $maker = 'p:' . $equips[$caught]['idnum'];
      }
      
      echo 'and into ' . $location . '.</p>' .
           '<p>The image of a fox rises from ' . $location . ', large enough to swallow it up, and then, with a flash of brilliant light and the sound of chimes lightly blown in a summer breeze, twists itself into the tiny Pipe Fox which leaps out of the now-cracked Midheaven Vase and into your hands.';

      add_inventory($user['user'], $maker, 'Pipe Fox', 'Found in a Midheaven Vase', $this_inventory['location']);

      $command = 'UPDATE monster_inventory SET itemname=\'Cracked Midheaven Vase\' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'itemaction.php/Midheaven Vase');

      if(count($destroyed) > 0)
      {
        foreach($destroyed as $planet)
        {
          delete_inventory_byid($equips[$planet]['toolid']);
          $command = 'UPDATE monster_pets SET toolid=0 WHERE idnum=' . $equips[$planet]['idnum'] . ' LIMIT 1';
          $database->FetchNone($command, 'itemaction.php/Midheaven Vase');
        }
      }
    }
  }
} // at home
?>
