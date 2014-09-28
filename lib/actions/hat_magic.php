<?php
if($okay_to_be_here !== true)
  exit();

$i = mt_rand(1, 10);
$now = time();

$AGAIN_WITH_SAME = true;

switch($i)
{
  case 1:
    $message = 'You reach inside the hat and pull out a Bag of Tricks!';

    add_inventory($user['user'], 'u:' . $user['idnum'], 'Bag of Tricks', 'Pulled out of a Magic Hat', $this_inventory['location']);
    break;

  case 2:
    require_once 'commons/fireworklib.php';

    $message = 'You tap the hat, causing it to send off small fireworks and sparklers!</p>' .
      '<p><i>(A firework has been readied!  Find a Plaza post to send it off to!)</i>';

    $supply = get_firework_supply($user);

    gain_firework($supply, mt_rand(1, 8));

    $command = 'UPDATE monster_users SET fireworks=' . quote_smart(render_firework_data_string($supply)) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'removing firework from player');

    break;

  case 3:
    $message = 'You reach inside the hat and pull out a Pole Lamp!';

    add_inventory($user['user'], 'u:' . $user['idnum'], 'Pole Lamp', 'Pulled out of a Magic Hat', $this_inventory['location']);
    break;

  case 4:
    $message = 'You reach inside the hat and pull out some of a scarf, and then some more, and then some more, until you finally produce an entire Scarf of Wind!';

    add_inventory($user['user'], 'u:' . $user['idnum'], 'Scarf of Wind', 'Pulled out of a Magic Hat', $this_inventory['location']);
    break;

  case 5:
    $message = 'You reach inside the hat and pull out the end of a red ribbon... you pull and pull until an entire 50-foot Red Ribbon is produced!';

    add_inventory($user['user'], 'u:' . $user['idnum'], '50-foot Red Ribbon', 'Pulled out of a Magic Hat', $this_inventory['location']);
    break;

  case 6:
    $message = 'You reach inside the hat and pull out a coin! ... Wait, wrong trick >_>  (That wasn\'t very impressive at all.)';

    add_inventory($user['user'], 'u:' . $user['idnum'], '1-moneys Coin', 'Pulled out of a Magic Hat', $this_inventory['location']);
    break;

  case 7:
    $num_flowers = mt_rand(4, 6);
    $flower = array('Amethyst Rose', 'Arbutus', 'Honeysuckle', 'Narcissus', 'Pansy', 'Periwinkle', 'Poinsettia', 'Primrose', 'Purple Lilac', 'White Lily', 'Yellow Acacia', 'White Lotus');

    for($x = 0; $x < $num_flowers; ++$x)
      $flowers[] = $flower[array_rand($flower)];

    if(mt_rand(1, 20) == 1)
      $flowers[] = 'Edelweiss';
    else if(mt_rand(1, 20) == 1)
      $flowers[] = 'Black Lotus';

    $message = 'You reach inside the hat and pull out ' . count($flowers) . ' flowers!';

    foreach($flowers as $this_flower)
      add_inventory($user['user'], 'u:' . $user['idnum'], $this_flower, 'Pulled out of a Magic Hat', $this_inventory['location']);

    break;

  case 8:
    $message = 'You reach inside the hat and pull out a Fluff-- ... oh, just a Fluff.  Damn.';

    add_inventory($user['user'], 'u:' . $user['idnum'], 'Fluff', 'Pulled out of a Magic Hat', $this_inventory['location']);
    break;

  case 9:
    // 25% Shooting Star Painting
    if(mt_rand(1, 4) == 1)
    {
      $message = 'You reach inside the hat and pull out a Shooting Star-- ... Painting?  Darn.';

      add_inventory($user['user'], 'u:' . $user['idnum'], 'Shooting Star Painting', 'Pulled out of a Magic Hat', $this_inventory['location']);
    }
    // 75% Shooting Star Painting
    else
    {
      $message = 'You reach inside the hat and pull out a Shooting Star!';

      add_inventory($user['user'], 'u:' . $user['idnum'], 'Shooting Star', 'Pulled out of a Magic Hat', $this_inventory['location']);
    }
    break;

  case 10: // pull a rabbit, or familiar
    // 10% - familiar (total of 1% >_>)
    if(mt_rand(1, 10) == 1)
    {
      $location = $this_inventory['location'];
      if(substr($location, 0, 8) == 'storage/')
        $location = 'storage';
    
      $message = 'You reach inside the hat and feel something squirming!</p>' .
        '<p>After a moment of struggling, you pull out a rabbi-- wait, what?!</p>' .
        '<p>This is no Rabbit!  It\'s a Familiar!</p>' .
        '<p>It looks at you with a look as puzzled as your own before you finally remember to let go.</p>' .
        '<p>(Also, your Magic Hat vanishes in a puff of smoke, its work apparently done.)';
        
      add_inventory($user['user'], 'u:' . $user['idnum'], 'Familiar', 'Pulled out of a Magic Hat', $location);

      delete_inventory_byid($this_inventory['idnum']);
      $AGAIN_WITH_ANOTHER = true;
      $AGAIN_WITH_SAME = false;
    }
    // 90% - rabbit
    else
    {
      $message = 'You reach inside the hat and feel something squirming!</p>' .
        '<p>After a moment of struggling, you pull out a rabbit!</p>' .
        '<p>It hops into your house.</p>' .
        '<p>(Also, your Magic Hat vanishes in a puff of smoke, its work apparently done.)';

      $petid = create_random_pet($user['user']);

      $command = 'UPDATE monster_pets SET graphic=\'special/bunny_mocha.png\' WHERE idnum=' . $petid . ' LIMIT 1';
      $database->FetchNone($command, 'item: ' . $this_inventory['itemname']);

      $level = mt_rand(1, 5);
      
      $initial_stats = array();
      $updates = array();

      for($i = 0; $i < $level; ++$i)
        $initial_stats[$PET_SKILLS[array_rand($PET_SKILLS)]]++;

      foreach($initial_stats as $stat=>$score)
        $updates[] = '`' . $stat . '`=' . $score;

      $database->FetchNone('UPDATE monster_pets SET ' . implode(',', $updates) . ' WHERE idnum=' . $petid . ' LIMIT 1');

      delete_inventory_byid($this_inventory['idnum']);
      $AGAIN_WITH_ANOTHER = true;
      $AGAIN_WITH_SAME = false;
    }
    break;
}
?>
<p><?= $message ?></p>
