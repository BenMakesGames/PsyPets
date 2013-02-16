<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$whereat = 'home';
$wiki = 'Attic';
$THIS_ROOM = 'Attic';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';

if(!addon_exists($house, 'Attic'))
{
  header('Location: /myhouse.php');
  exit();
}

$command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE itemname=\'Skeleton Key\' AND user=' . quote_smart($user['user']) . ' AND location LIKE \'home%\' AND location NOT LIKE \'home/$%\'';
$data = fetch_single($command, 'fetching key count');

$keys = (int)$data['c'];

if($_POST['action'] == 'Unlock')
{
  $num_keys = (int)$_POST['keys'];
  
  if($keys == 0)
    $message = '<p><span class="failure">You do not have any keys...</span></p>';
  else if($num_keys < 1)
    $message = '<p><span class="failure">Er... how many, did you say?</span></p>';
  else if($num_keys > $keys)
    $message = '<p><span class="failure">You do not have that many keys, unfortunately...</span></p>';
  else
  {
    $possible_items = array(
      'Dirty Linen' => 20,                 // 20%
      'Maze Piece Summoning Scroll' => 20, // 20%
      'The Masque of the Red Death' => 10, // 10%
      'Menagerie Blueprint' => 9,          //  9%
      'The Gourmet\'s Love-Song' => 7,     //  7%
      'Book of Creatures III' => 6,        //  6%
      'The Raven' => 5,                    //  5%
      'Sparkling Doodilly' => 5,           //  5%
      'Shiny Thingamadig' => 5,            //  5%
      'Portrait of Nobility' => 4,         //  4%
      'World Map #4' => 3,                 //  3%
      'Oar Array Blueprint' => 3,          //  3%
      'Phoenix Down' => 2,                 //  2%
      'Child\'s Play' => 1,                //  1%
    );

    $items = array();
    $keys_used = 0;

    while($num_keys > 0 && ($used = delete_inventory_fromhome($user['user'], 'Skeleton Key', 1)) == 1)
    {
      $keys--;
      $num_keys--;
      $keys_used++;

      $a = rand(mt_rand(1, 2), 4);  // average of 2.75

      for($i = 0; $i < $a; ++$i)
      {
        $index = rand(1, 100);

        foreach($possible_items as $itemname=>$percent)
        {
          if($index <= $percent)
          {
            $items[] = $itemname;
            break;
          }
          else
            $index -= $percent;
        }
      }
    }

    if($keys_used > 0)
    {
      asort($items);

      foreach($items as $item)
        add_inventory($user['user'], '', $item, 'Found in ' . $user['display'] . "'s Attic", 'home');

      $message = '<p><span class="success">Opening the chest' . ($keys_used > 1 ? 's' : '') . ' reveals: ' . implode(', ', $items) . '.</span></p>';

      require_once 'commons/statlib.php';
      record_stat($user['idnum'], 'Mysterious Chests Opened', $keys_used);
    }
  }
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Attic</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Attic</h4>
<?php
room_display($house);
echo $message;
?>
     <p>The Attic adds 50 units of free space to your Storage (100 is usually when you start paying fees).</p>
     <h5>Mysterious Chests</h5>
     <p>Several dusty, old chests sits amidst your other belongings.  Where did these come from?</p>
<?php
if($keys == 0)
  echo '<p>They\'re locked, and unfortunately you do not have any keys with which to unlock any.  <i>(Items in Storage are not accessible by house add-ons.)</i></p>';
else
{
?>
     <p>They're locked.  Fortunately, you have <?= $keys ?> Skeleton Key<?= ($keys != 1 ? "s" : "") ?> on-hand.</p>
     <form method="post">
     <p>Quantity: <input type="text" name="keys" value="1" maxlength="3" size="3" /> <input type="submit" name="action" value="Unlock" /></p>
     </form>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
