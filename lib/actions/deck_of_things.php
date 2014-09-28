<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/itemlib.php';
require_once 'commons/petlib.php';
require_once 'commons/statlib.php';

$destination = $this_inventory['location'];

if(substr($destination, 0, 8) == 'storage/')
  $destination = 'storage';

if(count($userpets) == 0)
{
  echo '<p>The deck is ethereal to your touch!  <i>(You don\'t meet the requirements to use it.)</i></p>';
}
else if($_GET['agreed'] != '1')
{
?>
 <p>The Deck of Many things is capable of riches and wonders, but also death.  Use it carefully or not at all.</p>
 <ul><li><a href="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>&agreed=1">Draw a card anyway</a></li></ul>
<?php
}
else
{
  $i = rand() % 22;

  if($i == 0)
  {
    give_money($user, (rand() % 10 + 1) * 100, 'Created by the Deck of Many Things');
    $desc = 'gave money';
    $description = 'It\'s "The Merchant".';
  }
  else if($i == 1)
  {
    if($user['money'] <= 500)
    {
      add_inventory($user['user'], '', 'Fluff', 'Created by the Deck of Many Things', $this_inventory['location']);
      $desc = 'created fluff';
      $description = 'It\'s "The Lesser Fluff Elemental".';
    }
    else
    {
      take_money($user, mt_rand(500, 1000), 'Consumed by the Deck of Many Things');
      $desc = 'took money';
      $description = 'It\'s "The Taxman".';
    }
  }
  else if($i == 2)
  {
    $then = time() - (8 * 60 * 60);

    $command = 'SELECT COUNT(*) AS c FROM monster_posts WHERE creationdate>' . $then;
    $data = $database->FetchSingle($command, 'fetching number of posts');

    $num_posts = (int)$data['c'];

    $rand_post = mt_rand(0, $num_posts - 1);

    $command = 'SELECT idnum FROM monster_posts WHERE creationdate>' . $then . ' LIMIT ' . $rand_post . ',1';
    $post = $database->FetchSingle($command, 'DoMT:2');

    $command = "UPDATE monster_posts SET egg='gold' WHERE idnum=" . $post['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'DoMT:2b');

    $desc = 'created a gold egg';

    $description = 'It\'s "The Chicken".';
  }
  else if($i == 3)
  {
    $petindex = array_rand($userpets);

    $userpets[$petindex]['dead'] = 'magical';

    save_pet($userpets[$petindex], array('dead'));

    $desc = "killed a pet";

    $description = 'It\'s "The Reaper".';
  }
  else if($i == 4)
  {
    $petindex = array_rand($userpets);
   
    $stat = $PET_SKILLS[array_rand($PET_SKILLS)];
   
    $userpets[$petindex][$stat]++;
   
    save_pet($userpets[$petindex], array($stat));

    $desc = 'increased a random stat on a pet';

    $description = 'It\'s "The Professor".';
  }
  else if($i == 5 || $i == 6)
  {
    $desc = 'nothing';
    $description = 'It\'s "The Ghost".';
  }
  else if($i == 7)
  {
    $item_array = array('Pumpkin Pie', 'Brush', 'Figurine #7', 'Blue Egg on White Bread', 'Goodberries', 'Hamlet: Act V Scene II');

    add_inventory($user['user'], '', $item_array[array_rand($item_array)], 'Created by the Deck of Many Things', $this_inventory['location']);

    $desc = 'created a random item';
    $description = 'It\'s "The Poor Trader".';
  }
  else if($i == 8)
  {
    $item_array = array('Mars', 'Pitchfork', 'Surprise Dish', 'Chicken Kiev', 'Soramimi Cake', 'Rotten Egg', 'The Butterfly That Stamped',
                        'Log', 'Dream Catcher', 'Meat Lover\'s Pizza Supreme', 'Sprinkled Donut', 'Broccoli', 'Balloon Animal');

    add_inventory($user['user'], '', $item_array[array_rand($item_array)], 'Created by the Deck of Many Things', $this_inventory['location']);

    $desc = 'created a random item';
    $description = 'It\'s "The Wealthy Trader".';
  }
  else if($i == 9)
  {
    create_random_pet($user['user']);
    $desc = 'created a random pet';
    $description = 'It\'s "The Stork".';

    record_stat($user['idnum'], 'Summoned a Pet', 1);
  }
  else if($i == 10)
  {
    // destroy house (set house size to 50
    $desc = 'no effect - fix this?';
    $description = 'It\'s "The Ghost".';
  }
  else if($i == 11)
  {
    // randomly change the pet's name

    $petindex = array_rand($userpets);
    $petname  = random_name($userpets[$petindex]['gender']);
   
    $command  = 'UPDATE monster_pets SET petname=' . quote_smart('Crazy ' . $petname) . ' WHERE idnum=' . $userpets[$petindex]["idnum"] . ' LIMIT 1';
    $database->FetchNone($command, 'DoMT:11');

    $desc = "pet name change";

    $description = 'It\'s "The Jester".';
  }
  else if($i == 12)
  {
    add_inventory($user['user'], '', 'Demon', 'Summoned by the Deck of Many Things', $destination);
    $desc = 'summoned a demon';
    $description = 'It\'s "The Demon".';

    record_stat($user['idnum'], 'Summoned a Demon', 1);
  }
  else if($i == 13)
  {
    $summon_to = $this_inventory['location'];
    if(substr($summon_to, 0, 8) == 'storage/')
      $summon_to = 'storage';

    add_inventory($user['user'], '', 'Familiar', 'Summoned by the Deck of Many Things', $destination);
    $desc = "summoned a familiar";
    $description = 'It\'s "The Familiar".';

    record_stat($user['idnum'], 'Summoned a Familiar', 1);
  }
  else if($i == 14)
  {
    // randomly change the pet's graphic

    $graphics = get_global('petgfx');

    $petindex = array_rand($userpets);
    $graphic = $graphics[rand() % count($graphics)];

    $command = "UPDATE monster_pets SET graphic='$graphic' WHERE idnum=" . $userpets[$petindex]["idnum"] . " AND protected='no' LIMIT 1";
    $database->FetchNone($command, 'DoMT:14');

    $desc = "pet graphic change";
    $description = 'It\'s "The Mirror".';
  }
  else if($i == 15)
  {
    // fill me in!
    $description = 'It\'s "The Ghost".';
  }
  else if($i == 16)
  {
    // create a bunch of debris and rubble

    $n = rand(1, 10);
    $debris = array("Debris", "Rubble" , "Ruins");

    for($j = 0; $j < $n; ++$j)
    {
      $d = array_rand($debris);

      add_inventory($user["user"], '', $debris[$d], "Created by the Deck of Many Things", $this_inventory['location']);
    }

    $desc = "created debris, rubble, and ruins";
    $description = 'It\'s "The Tower".';
  }
  else if($i == 17)
  {
    // duplicate for a new resident
    $description = 'It\'s "The Ghost".';
  }
  else if($i == 18)
  {
    add_inventory($user["user"], '', 'Maze Piece Summoning Scroll', 'Created by the Deck of Many Things', $this_inventory['location']);
    $desc = 'summoned a maze piece';
    $description = 'It\'s "The Labyrinth".';
  }
  else if($i == 19)
  {
    $details = $database->FetchSingle('
      SELECT itemname
      FROM monster_items
      WHERE itemname LIKE \'%Caviar%\'
      ORDER BY RAND()
      LIMIT 1
    ');

    add_inventory($user['user'], '', $details['itemname'], 'Created by the Deck of Many Things', $this_inventory['location']);
    $desc = 'created ' . $details['itemname'];
    $description = 'It\'s "' . $details['itemname'] . '".';
  }
  else if($i == 20)
  {
    add_inventory($user["user"], '', 'Hungry Cherub (level 0)', 'Created by the Deck of Many Things', $this_inventory['location']);
    $description = 'It\'s "The Cherub".';
  }
  else if($i == 21)
  {
    // toggle your license to commerce
   
    if($user['license'] == 'no')
    {
      $command = 'UPDATE monster_users SET license=\'yes\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'granting LtC');
      $command = 'UPDATE psypets_badges SET ltc=\'yes\' WHERE userid=' . $user['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'granting LtC badge');

      $desc = 'gave you a license to commerce';
      $description = 'It\'s "The Banker".';
    }
    else
    {
      $command = 'UPDATE monster_users SET license=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'stealing LtC');
      $command = 'UPDATE psypets_badges SET ltc=\'no\' WHERE userid=' . $user['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'stealing LtC badge');

      $desc = "took away your license to commerce";
      $description = 'It\'s "The Banker".';
    }
  }

  if(rand() % 10 == 0 && $i != 18)
  {
    // move to a new resident

    $recently = time() - (60 * 60 * 24);
    $command = "SELECT * FROM monster_users WHERE lastactivity>=$recently AND idnum!=" . $user["idnum"] . " ORDER BY rand() LIMIT 1";
    $new_user = $database->FetchSingle($command, 'fetching new owner');

    if($new_user !== false)
    {
      $command = "UPDATE monster_inventory SET user=" . quote_smart($new_user['user']) . ", location='storage/incoming', message2='' WHERE idnum=" . $this_inventory['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'moving DoMT to new owner');

      flag_new_incoming_items($new_user['user']);

      $vanishes = true;
      $desc = 'found a new owner';
    }
  }

  echo '<p>You draw a card from the deck... ' . $description . '</p>';

  if($vanishes === true)
    echo '<p>The deck releases its weight from your hands before fading away!</p>';
}

record_stat($user['idnum'], 'Used a Deck of Many Things', 1);
?>
