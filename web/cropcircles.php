<?php
$wiki = 'Crop_Circles';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/messages.php';
require_once 'commons/questlib.php';
require_once 'commons/itemlib.php';

if($now_month != 10)
{
  header('Location: ./404.php');
  exit();
}

$npc_graphic = 'alien';

$defender_of_earth_quest = get_quest_value($user['idnum'], 'defender of earth');

if($defender_of_earth_quest === false)
{
  add_quest_value($user['idnum'], 'defender of earth', 0);
  $defender_of_earth_quest = get_quest_value($user['idnum'], 'defender of earth');
  if($defender_of_earth_quest === false)
    die('A terrible error has taken place.  Probably.');
}

$CONTENT_STYLE = 'background: #fff url(gfx/cropcircles.png) no-repeat scroll top left;';

if($now_day >= 31 - 14)
{
  $command = 'SELECT a.idnum,a.petname,a.toolid FROM monster_pets AS a LEFT JOIN monster_inventory AS b ON a.toolid=b.idnum WHERE a.user=' . quote_smart($user['user']) . ' AND a.location=\'home\' AND b.itemname=\'Alien Costume\'';
  $my_pets = $database->FetchMultiple($command, 'fetching disguised pets');

  $num_my_pets = count($my_pets);

  if($_GET['dialog'] == 'whattodo')
  {
    $dialog = '<p>Die...!</p>';
    $options[] = '<a href="myhouse.php">Run away!</a>';
  }
  else if($_GET['dialog'] == 'peace')
  {
    $dialog = '<p>Peace?  No peace.</p>';
    $options[] = '<a href="cropcircles.php?dialog=whattodo">Ask what it wants you to do</a>';
  }
  else if($_GET['dialog'] == 'talk' && $num_my_pets > 0 && $now_day < 29)
  {
    $dialog = '<p>Oh!  You enslaved that human, right?  Good, good... you had me scared for a moment there.  Heh.</p>';
    $options[] = '<a href="cropcircles.php?dialog=talk2">(dot, dot, dot)</a>';
  }
  else if($_GET['dialog'] == 'talk2' && $num_my_pets > 0 && $now_day < 29)
  {
    $dialog = '<p>Too scared to talk, eh?  Heh, yeah, I\'m a little nervous myself, since the humans beat us last year by disguising their pets in Alien Costumes and ambushing us...</p>' .
              '<p>Ah, but don\'t worry about it.  Just keep an eye out, and if you see anyone suspicious, let your commanding officer know about it.  But for Ki Ri Kashu\'s sake, do <strong>not</strong> zap them with your <a href="encyclopedia2.php?i=2502">Alien Taser</a>!  If you\'re wrong, and zap one of our own, you\'ll be court-martialed for sure!</p>' .
              '<p>Sorry, I don\'t mean to scare you.  It\'ll be fine, you\'ll see.  When the shipment of <a href="encyclopedia2.php?i=2495">Serum</a> comes in on the 29th, we\'ll finally go on the offensive.  You\'ll have so much fun zaaping humans, you won\'t have time to worry!  Ha, ha!</p>' .
              '<p>Anyway, I better get back to my patrol.  See you around.</p>';
  }
  else if($_POST['action'] == 'Infiltrate' && $now_day == 29)
  {
    $petids = array();

    foreach($userpets as $i=>$pet)
    {
      if($pet['toolid'] > 0 && $pet['trickedaliens'] == 'no')
      {
        $tool = get_inventory_byid($pet['toolid'], 'itemname');
        if($tool['itemname'] == 'Alien Costume')
        {
          $petids[] = $pet['idnum'];
          set_pet_badge($pet, 'masterspy');
          $userpets[$i]['trickedaliens'] = 'yes';
        }
      }
    }

    if(count($petids) > 0)
    {
      $command = 'UPDATE monster_pets SET trickedaliens=\'yes\' WHERE idnum IN (' . implode(',', $petids) . ') LIMIT ' . count($petids);
      $database->FetchNone($command, 'marking pets as successful');

      $dialog = '<p>Here to pick up supplies for the big raid?  Alrighty, then.</p>' .
                '<p><i>(You received ' . count($petids) . ' Serum' . (count($petids) == 1 ? '' : 's') . '.  Find it in your ' . $user['incomingto'] . '.)</i></p>' .
                '<p>Beautiful, isn\'t it?  Ha, ha!  With this, the humans don\'t stand a chance!  Sector WS-313 will finally once again be ours!</p>' .
                '<p>You know what I can\'t wait for?  A nice leg of Desikh!  It\'s been <em>so long</em> since I had a good bit of Desikh.  Like, what, 8,000 years now?  More?  The replicated stuff back home just isn\'t the same.</p>' .
                '<p>You look young, so maybe you don\'t remember, but if ' . $user['display'] . ' is keeping one as a pet, you should really give it a try.  Cook it for at least an hour, to be sure that the juices-- ... sorry, I\'m going on again, aren\'t I?</p>' .
                '<p>Anayway, you have your Serum.  Good luck!  The Humans are only our first obstacle.  Then we have to deal with those Abandondero we left behind.  Ki Ri Kashu, I\'m not looking forward to that...</p>' .
                '<p><i>(Your pet' . (count($petids) == 1 ? '' : 's') . ' received the Master Spy Badge!)</i></p>';

      add_inventory_quantity($user['user'], '', 'Serum', 'Stolen from the Crop Circle Aliens', $user['incomingto'], count($petids));
    }
  }

  if($now_day >= 30 && $defender_of_earth_quest['value'] != date('Y'))
  {
    $command = 'SELECT idnum FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=\'storage\' AND itemname=\'Alien Taser\' LIMIT 1';
    $item = $database->FetchSingle($command, 'fetching taser from storage');

    if($item !== false)
    {
      if($_GET['dialog'] == 'zaap')
      {
        update_quest_value($defender_of_earth_quest['idnum'], date('Y'));
        $dialog = '<p>Gack!  Deceit!  You\'ll regret this, Human!</p>';
        $npc_graphic = 'alien_zaaped';

        $badges = get_badges_byuserid($user['idnum']);
        if($badges['defenderofearth'] == 'no')
        {
          $extra = '<p><i>(You won the Defender of (Hollow) Earth Badge.)</i></p>';
          set_badge($user['idnum'], 'defenderofearth');
        }
        else
        {
          require_once 'commons/fireworklib.php';

          $supply = get_firework_supply($user);

          gain_firework($supply, 10);

          $command = 'UPDATE monster_users SET fireworks=' . quote_smart(render_firework_data_string($supply)) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
          $database->FetchNone($command, 'giving firework to player');

          $extra = '<p><i>(A "firework" has been readied!  Find a Plaza post to send it off to!)</i></p>';
        }
      }
      else
        $options[] = '<a href="cropcircles.php?dialog=zaap">Zaap the Crop Circle Alien with an Alien Taser!</a>';
    }
  }

  if(strlen($dialog) == 0 && $defender_of_earth_quest['value'] != date('Y'))
  {
    if($now_day < 29)
    {
      $dialog = '<p>Tsk!  A human!  It won\'t go as well for you this year as it did last!  Oh no!  We\'re much more prepared, this time!</p>';
      $extra = '<p><i>The Alien cocks its gun.</i></p>';
      $options[] = '<a href="cropcircles.php?dialog=peace">Ask if there can be a peace between you</a>';

      if($num_my_pets > 0)
      {
        foreach($my_pets as $this_pet)
          $options[] = '<a href="cropcircles.php?dialog=talk&petid=' . $this_pet['idnum'] . '">Have ' . $this_pet['petname'] . ' talk to the Alien</a>';
      }
    }
    else
    {
      $dialog = '<p>Get out of here, human!</p>';
      $extra = '<p><i>The Alien cocks its gun.</i></p>';
    }
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Crop Circles</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Crop Circles</h4>
     <?= ($check_message ? '<p style="color:blue;">$check_message</p>' : '') ?>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if(strlen($dialog) > 0)
{
  $saw_aliens = get_quest_value($user['idnum'], 'close encounter');
  if($saw_aliens === false)
    add_quest_value($user['idnum'], 'close encounter', 1);
?>
<img src="gfx/npcs/<?= $npc_graphic ?>.png" align="right" width="120" height="80" alt="(An alien!)" />
<?php
  include 'commons/dialog_open.php';

  echo $dialog;

  include 'commons/dialog_close.php';
}
else
  echo '<p><i>There\'s nothing here... well, other than the lack of crop that forms the crop circles.</i></p>';

echo $extra;

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

// is it october 29th?
if($now_day == 29)
{
  $num_spies = 0;

  foreach($userpets as $pet)
  {
    if($pet['toolid'] > 0 && $pet['trickedaliens'] == 'no')
    {
      $tool = get_inventory_byid($pet['toolid'], 'itemname');
      if($tool['itemname'] == 'Alien Costume')
      {
        if($num_spies == 0)
          echo '<form action="cropcircles.php" method="post"><p>Send the following pets to infiltrate the Crop Circle Aliens (only pets disguised in Alien Costumes can do this):</p><table>';
?>
 <tr><td><img src="gfx/pets/<?= $pet['graphic'] ?>" alt="" width="48" height="48" /></td><td><?= $pet['petname'] ?></td></tr>
<?php
        $num_spies++;
      }
    }
  }

  if($num_spies > 0)
    echo '</table><p><input type="submit" name="action" value="Infiltrate" /></p></form>';
  else
    echo '<p><i>(Only pets in Alien Costumes who have not already earned the Master Spy badge may infiltrate the aliens.)</i></p>';
}

echo '<img src="gfx/shim.gif" height="250" width="1" alt="" />';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
