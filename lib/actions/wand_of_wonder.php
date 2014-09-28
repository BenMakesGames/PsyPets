<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/statlib.php';

$destination = $this_inventory['location'];

if(substr($destination, 0, 8) == 'storage/')
  $destination = 'storage';

$i = mt_rand(1, 43);
$now = time();

$AGAIN_WITH_SAME = true;

if($i == 1)
{
  $amount = dice_roll(5, 20);
   
  for($j = 0; $j < $amount; ++$j)
    add_inventory($user['user'], '', 'Redsberries', 'Summoned by a Wand of Wonder', $this_inventory['location']);

  $message = 'Redsberries stream out of the wand, scattering themselves around the house.';
}
else if($i == 2)
{
  for($j = 0; $j < count($userpets); ++$j)
  {
    lose_stat($userpets[$j], 'safety', dice_roll(3, 4));
    save_pet($userpets[$j], array('safety'));
  }

  $message = 'A swarm of bats pours out of the wand!  They swarm and screech for a minute before fluttering out the window.';
}
else if($i == 3)
{
  $message = 'There\'s a flash of light and the distinct sound of glass shattering.';
}
else if($i == 4)
{
  $petindex = array_rand($userpets);
  $stat = $PET_SKILLS[array_rand($PET_SKILLS)];
   
  $userpets[$petindex][$stat]++;
   
  save_pet($userpets[$petindex], array($stat));

  $message = $userpets[$petindex]['petname'] . ' smiles.  <i>(And increases in level!)</i>';

  $log_message = $userpets[$petindex]['petname'] . ' gained mysterious training from a Wand of Wonder!';
  
  add_logged_event($user['idnum'], $userpets[$petindex]['idnum'], 0, 'realtime', false, $log_message);

  $database->FetchNone('
    INSERT INTO psypets_pet_level_logs
    (timestamp, petid, answer)
    VALUES
    (
      ' . time() . ',
      ' . $userpets[$petindex]['idnum'] . ',
      ' . quote_smart($log_message) . '
    )
  ');
}
else if($i == 5)
{
  $message = 'An explosion sounds in the distance...';
}
else if($i == 6)
{
  for($j = 0; $j < count($userpets); ++$j)
  {
    gain_safety($userpets[$j], dice_roll(1, 4), true);
    gain_love($userpets[$j], 1, true);
    save_pet($userpets[$j], array('safety', 'love'));
  }

  $message = 'The wand plays a soft tune.';
}
else if($i == 7)
{
  for($j = 0; $j < count($userpets); ++$j)
  {
    gain_love($userpets[$j], dice_roll(2, 4), true);
    save_pet($userpets[$j], array('love'));
  }

  $message = 'The room darkens, leaving you blind for a moment before the wand erupts into a dazzling light show containing every color you\'ve ever seen, and some you haven\'t.';
}
else if($i == 8)
{
  for($j = 0; $j < count($userpets); ++$j)
  {
    lose_stat($userpets[$j], 'safety', dice_roll(2, 4));
    lose_stat($userpets[$j], 'love', dice_roll(1, 4));
    save_pet($userpets[$j], array('safety', 'love'));
  }

  $message = 'The wand begins to howl, at first quietly, but then increasingly loudly.  The howl turns into a terrible whine as you evacuate the house.  Finally, after a couple minutes, the wand abruptly stops.';
}
else if($i == 9)
{
  add_inventory($user['user'], '', '50-foot Red Ribbon', 'Summoned by a Wand of Wonder', $this_inventory['location']);
   
  $message = 'A ribbon begins to stream out of the wand, slowly at first, and then quickly.  After 50 feet the end of the ribbon finally pops out, and the whole thing comes to a rest on the floor.';
}
else if($i == 10)
{
  for($j = 0; $j < count($userpets); ++$j)
  {
    lose_stat($userpets[$j], 'safety', 1);
    save_pet($userpets[$j], array('safety'));
  }

  add_inventory($user['user'], '', 'Smoke', 'Summoned by a Wand of Wonder', $this_inventory['location']);

  $message = 'The wand belches fire for a few seconds, which you barely manage to point away from your belongings.';
}
else if($i == 11)
{
  $message = 'A voice booms: "This statement... is a lie!"';
}
else if($i == 12)
{
  $recently = $now - 60 * 60;

  $command = 'SELECT * FROM monster_posts WHERE creationdate>=' . $recently . ' ORDER BY rand() LIMIT 1';
  $this_post = $database->FetchSingle($command, 'fetching post to recite');

  if($this_post === false)
    $message = 'The wand grows a pair of lips which open, as it to speak, but then vanish without a word.';
  else
  {
    $post_body = format_text($this_post['body']);

    $message = 'The wand grows a pair of lips which say: "' . $post_body . '"';
  }
}
else if($i == 13)
{
  add_inventory($user['user'], '', '1-moneys Coin', 'Summoned by a Wand of Wonder', $this_inventory['location']);

  $message = 'The wand begins to quiver, and then shake violently.  A rumbling noise fills the house followed by a crash as a bookshelf falls over.  A cloud front sweeps in, blocking out the light just as the wand lurches from your grasp.  It floats in the air, spinning and shaking, glowing an increasingly bright red.  Steam whistles out of a single end, and then, all at once the clouds recede, the rumbling stops, and the wand ejects a tiny, shining, moneys coin before falling to the ground with a hollow clank.';
}
else if($i == 14)
{
  add_inventory($user['user'], '', 'The Cat that Walked by Himself', 'Summoned by a Wand of Wonder', $this_inventory['location']);

  $message = 'Several pieces of rolled-up paper slide themselves out of the wand, one at a time.  When this is done, the wand removes itself from your grip, and sets itself to work writing on each page with lightning speed.  Once every page has been written on, the wand sews and binds them, leaving you with a book entitled "The Cat that Walked by Himself."';
}
else if($i == 15)
{
  if(count($userpets) > 0)
  {
    $j = array_rand($userpets);
   
    add_inventory($user['user'], '', 'Urn', $userpets[$j]['petname'] . '\'s Ashes', $this_inventory['location']);
    lose_stat($userpets[$j], 'safety', dice_roll(3, 6));
    save_pet($userpets[$j], array('safety'));

    $message = $userpets[$j]['petname'] . ' shrieks in terror and points behind you.  When you turn around there\'s an urn labeled "' . $userpets[$j]['petname'] . '\'s Ashes".';
  }
  else
    $message = 'Nothing seems to happen.';
}
else if($i == 16)
{
  add_inventory($user['user'], '', 'Orange Juice', 'Summoned by a Wand of Wonder', $this_inventory['location']);
  add_inventory($user['user'], '', 'Prickly Green Juice', 'Summoned by a Wand of Wonder', $this_inventory['location']);
  add_inventory($user['user'], '', 'Coconut Juice', 'Summoned by a Wand of Wonder', $this_inventory['location']);
  add_inventory($user['user'], '', 'Limeade', 'Summoned by a Wand of Wonder', $this_inventory['location']);
  add_inventory($user['user'], '', 'Apple Juice', 'Summoned by a Wand of Wonder', $this_inventory['location']);
  add_inventory($user['user'], '', 'Pamplemousse Juice', 'Summoned by a Wand of Wonder', $this_inventory['location']);

  $message = 'Several glasses float in through the window and line themselves up in front of you.  The wand then tips, pouring a different liquid in to each one.';
}
else if($i == 17)
{
  for($j = 0; $j < count($userpets); ++$j)
  {
    lose_stat($userpets[$j], 'safety', dice_roll(1, 4));
    save_pet($userpets[$j], array('safety'));
  }

  add_inventory($user['user'], '', 'Steak', 'Summoned by a Wand of Wonder', $this_inventory['location']);
  add_inventory($user['user'], '', 'Steak', 'Summoned by a Wand of Wonder', $this_inventory['location']);
  add_inventory($user['user'], '', 'Leather', 'Summoned by a Wand of Wonder', $this_inventory['location']);
  add_inventory($user['user'], '', 'Leather', 'Summoned by a Wand of Wonder', $this_inventory['location']);
  add_inventory($user['user'], '', 'Blood', 'Summoned by a Wand of Wonder', $this_inventory['location']);
  add_inventory($user['user'], '', 'Blood', 'Summoned by a Wand of Wonder', $this_inventory['location']);

  $message = 'A cow appears before you, which the wand, in one motion, slaughters.';
}
else if($i == 18)
{
  add_inventory($user['user'], '', 'Elfin Bottle', 'Summoned by an Elf', $this_inventory['location']);

  $message = 'The wand summons an short Elf, who upon being summoned takes a step toward you and puts out his hand.  You stand for a moment, confused, until the Elf summons a small bottle in his hand, which he gives to you.  The Elf then bows, winks as he turns around, and vanishes.';
}
else if($i == 19)
{
  $message = 'The tip of the wand twinkles, revealing a Key Hole.';

  add_inventory($user['user'], '', 'Key Hole', 'Summoned by a Wand of Wonder', $destination);
}
else if($i == 20)
{
  require_once 'commons/globals.php';

  $avatargfx = get_global('avatargfx');
  $i = array_rand($avatargfx);

  $command = 'UPDATE monster_users SET `graphic`=' . quote_smart($avatargfx[$i]) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'wand_of_wonder.php:20');

  $message = 'A rainbow of colors washes over you.';
}
else if($i == 21)
{
  $command = 'UPDATE monster_inventory SET changed=' . $now . ' WHERE user=' . quote_smart($user['user']);
  $database->FetchNone($command, 'wand_of_wonder.php:21');

  $message = 'You open your eyes from a blink, which you would not usually be conscious of, except that when you open your eyes you notice that all your items have been rearranged.';
}
else if($i == 22)
{
  add_inventory($user['user'], '', 'Holy Water', 'Summoned by a Wand of Wonder', $this_inventory['location']);

  $message = 'A small vial slips in from under the door, and rolls itself to your feet.';
}
else if($i == 23)
{
  for($j = 0; $j < count($userpets); ++$j)
  {
    gain_love($userpets[$j], 1, true);
    save_pet($userpets[$j], array('love'));
  }

  $message = 'The wand lets off several bursts of confetti, which hangs in the air for a while before flickering out of existence.';
}
else if($i == 24)
{
  add_inventory($user['user'], '', 'Amethyst Rose Bush', 'Summoned by a Wand of Wonder', $this_inventory['location']);

  $message = 'A single flower pops out of the end of the wand, shortly followed by an entire plant.';
}
else if($i == 25)
{
  add_inventory($user['user'], '', 'Aging Root', 'Summoned by a Wand of Wonder', $this_inventory['location']);
  add_inventory($user['user'], '', 'Aging Root', 'Summoned by a Wand of Wonder', $this_inventory['location']);
  add_inventory($user['user'], '', 'Aging Root', 'Summoned by a Wand of Wonder', $this_inventory['location']);

  $message = 'Three fronds spring up from the carpet at your feet.  Picking each reveals an Aging Root.';
}
else if($i == 26)
{
  add_inventory($user['user'], '', 'Coconut', 'Summoned by a Wand of Wonder', $this_inventory['location']);

  $message = 'A Coconut falls out of your shirt pocket.';
}
else if($i == 27)
{
  $items = array('Gold Key', 'Silver Key', 'Silver Key', 'Copper Key', 'Copper Key', 'Copper Key');

  $item = $items[array_rand($items)];

  add_inventory($user['user'], '', $item, 'Summoned by a Wand of Wonder', $this_inventory['location']);

  $message = 'A single ' . $item . ' falls out of the tip of the wand, ringing as it hits the ground.';
}
else if($i == 28) // spawns a music note
{
  add_inventory($user['user'], '', 'Music Note', 'Summoned by a Wand of Wonder', $this_inventory['location']);

  $message = 'The wand begins to sing a beautiful tune, but it abrupty cut off as a physically-manifest note pops out of the end of the staff.';
}
else if($i == 29) // colorful pubbles make your pets happy
{
  for($j = 0; $j < count($userpets); ++$j)
  {
    gain_love($userpets[$j], dice_roll(1, 4), true);
    gain_safety($userpets[$j], dice_roll(1, 4), true);
    gain_esteem($userpets[$j], dice_roll(1, 4), true);
    save_pet($userpets[$j], array('love', 'safety', 'esteem'));
  }

  $message = 'For a few minutes the wand blows colorful bubbles of every size.';
}
else if($i == 30) // no effect
{
  $message = 'For a moment nothing happens, and then the wand laughs a terrible, evil laugh.';
}
else if($i == 31) // makes a butterfly pet
{
  $message = 'One hundred butterflies pour out of the wand.  Ninety-nine of them flutter away; only the tiniest among them is left behind.';

  $idnum = create_random_pet($user['user']);

  $command = 'UPDATE monster_pets SET graphic=\'whyabutterfly.gif\' WHERE idnum=' . $idnum . ' LIMIT 1';
  $database->FetchNone($command, 'wand_of_wonder.php:31');

  record_stat($user['idnum'], 'Summoned a Pet', 1);
}
else if($i == 32) // gains 1d4 charges
{
  $message = 'The wand hums and vibrates for a moment, seemingly to no effect.';
}
else if($i == 33) // 5-100 mixed berries
{
  $amount = dice_roll(5, 20);

  for($j = 0; $j < $amount; ++$j)
  {
    if(mt_rand(1, 7) == 1)
      add_inventory($user['user'], '', 'Goodberries', 'Summoned by a Wand of Wonder', $this_inventory['location']);
    else if(mt_rand(1, 2) == 1)
      add_inventory($user['user'], '', 'Redsberries', 'Summoned by a Wand of Wonder', $this_inventory['location']);
    else
      add_inventory($user['user'], '', 'Blueberries', 'Summoned by a Wand of Wonder', $this_inventory['location']);
  }

  $message = 'Mixed berries stream out of the wand, scattering themselves around the house.';
}
else if($i == 34) // add 4d6 movement points
{
  $amount = dice_roll(4, 6);
  $message = 'You hear the distinct sound of dice being rolled, followed by small explosions.  Odd.';
  $command = 'UPDATE monster_users SET mazemp=mazemp+' . $amount . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'wand_of_wonder.php:34');
}
else if($i == 35) // gives pyrestone :)
{
  $message = 'One of the house\'s windows breaks, apparently destroyed by a meteorite which has now implanted itself in the living room floor.';
  add_inventory($user['user'], '', 'Pyrestone', 'Summoned by a Wand of Wonder', $this_inventory['location']);
}
else if($i == 36) // lays an egg
{
  $message = 'The wand... lays an egg...';

  $eggitems = array('Egg', 'Speckled Egg', 'Blue Egg', 'Blue-Dyed Egg', 'Chocolate Egg', 'Gargantuan Egg', 'Gold-Dyed Egg',
    'Rainbow-Dyed Egg', 'Red-Dyed Egg', 'Rotten Egg', 'Silver-Dyed Egg', 'Speckled Egg', 'Tea Egg', 'Yellow-Dyed Egg');

  add_inventory($user['user'], '', $eggitems[array_rand($eggitems)], 'Summoned by a Wand of Wonder', $this_inventory['location']);
}
else if($i == 37) // summons a dog pet
{
  $message = 'The wand issues a shrill whistle.  Moments later, a baby dog appears from nowhere.';

  $idnum = create_random_pet($user['user']);

  $command = 'UPDATE monster_pets SET graphic=\'doggish.png\' WHERE idnum=' . $idnum . ' LIMIT 1';
  $database->FetchNone($command, 'wand_of_wonder.php:37');

  record_stat($user['idnum'], 'Summoned a Pet', 1);
}
else if($i == 38) // gives tornado-in-a-bottle
{
  $message = 'A glass bottle lands with a *tink* to the ground.';
  add_inventory($user['user'], '', 'Tornado-in-a-Bottle', 'Summoned by a Wand of Wonder', $this_inventory['location']);
}
else if($i == 39) // tap on the shoulder :P
{
  $message = 'You feel a tap on your left shoulder.  You totally fall for it.';
}
else if($i == 40) // phoenix down chest
{
  $message = 'The wand hands you tiny chest.  It has a lock on it, but it\'s already open.';
  add_inventory($user['user'], '', 'Tiny Unlocked Chest', 'Summoned by a Wand of Wonder', $this_inventory['location']);
}
else if($i == 41)
{
  $message = 'Something lands gently on your head, and stays there.  Taking it, you realize it\'s... a heart?  Not a human heart, but a red stone, cut and polished in the shape of a heart.';
  add_inventory($user['user'], '', 'Heart', 'Summoned by a Wand of Wonder', $this_inventory['location']);
}
else if($i == 42)
{
  $message = 'Two small, heart-shaped stones tumble out of the wand, one after the other.';
  add_inventory($user['user'], '', 'Heart', 'Summoned by a Wand of Wonder', $this_inventory['location']);
  add_inventory($user['user'], '', 'Heart', 'Summoned by a Wand of Wonder', $this_inventory['location']);
}
else if($i == 43)
{
  $message = '<img src="/gfx/books/wow-markings.png" />';
}

echo '<p>' . $message . '</p>';

if(mt_rand(1, 10) == 1)
{
  delete_inventory_byid($this_inventory['idnum']);

  echo '<p>The wand quivers slightly before puffing out of existence.</p>';
  $AGAIN_WITH_SAME = false;
  $AGAIN_WITH_ANOTHER = true;
}

record_stat($user['idnum'], 'Used a Wand of Wonder', 1);
?>
