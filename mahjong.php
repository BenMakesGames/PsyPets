<?php
$wiki = 'Totem_Pole_Garden#Mahjong Exchange';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/itemlib.php';
require_once 'commons/formatting.php';
require_once 'commons/totemlib.php';
require_once 'commons/questlib.php';

if($user['show_totemgardern'] == 'no')
{
  header('Location: /404.php');
  exit();
}

$mahjong_tiles = array(
  '1 Circle' => 1,      // 1700 - 1 point
  '2 Circle' => 2,      // 118 - 6 points
  '3 Circle' => 3,      // 73 - 7 points
  '4 Circle' => 4,      // 56 - 7 points
  '5 Circle' => 6,      // 38 - 7 points
  '6 Circle' => 2,      // 632 - 2 points - dropped by a Lost Samurai
  '7 Circle' => 3,      // 119 - 6 points
  '8 Circle' => 4,      // 52 - 7 points
  '9 Circle' => 6,      // 53 - 7 points

  '1 Character' => 4,   // 96 - 7 points
  '2 Character' => 3,   // 394 - 3 points
  '3 Character' => 3,   // 529 - 3 points
  '4 Character' => 3,   // 598 - 2 points
  '5 Character' => 3,   // 513 - 3 points
  '6 Character' => 2,   // 789 - 2 points
  '7 Character' => 2,   // 781 - 2 points
  '8 Character' => 2,   // 735 - 2 points
  '9 Character' => 2,   // 735 - 2 points

  '1 Bamboo' => 1,      // 1493 - 1 point - craft
  '2 Bamboo' => 3,      // 287 - 3 points - pawn
  '3 Bamboo' => 3,      // 267 - 3 points - pawn
  '4 Bamboo' => 3,      // 241 - 3 points - pawn
  '5 Bamboo' => 2,      // 945 - 2 points - lumberjacking
  '6 Bamboo' => 3,      // 269 - 3 points - pawn
  '7 Bamboo' => 3,      // 394 - 3 points - lumberjacking
  '8 Bamboo' => 3,      // 245 - 3 points - pawn
  '9 Bamboo' => 4,      // 245 - 4 points - lumberjacking

  'Autumn Season' => 3, // 263 - 4 points
  'Spring Season' => 3, // 316 - 4 points
  'Summer Season' => 3, // 199 - 4 points
  'Winter Season' => 3, // 212 - 4 points

  'Chrysanthemum Flower' => 4, // 124 - 5 points
  'Bamboo Flower' => 4, // 134 - 5 points
  'Orchid Flower' => 4, // 140 - 5 points
  'Plum Flower' => 4,   // 113 - 5 points

  'East Wind' => 4,     // 291 - 4 points
  'North Wind' => 4,    // 284 - 4 points
  'South Wind' => 4,    // 268 - 4 points
  'West Wind' => 4,     // 299 - 4 points

  'Green Dragon' => 3,  // 444 - 3 points
  'White Dragon' => 8,  // 47 - 7 points
  'Red Dragon' => 10,    // 50 - 7 points
);

$to_day = (int)(time() / (24 * 60 * 60));

$mahjong_seed = get_quest_value($user['idnum'], 'mahjong seed');

if($mahjong_seed === false)
{
  add_quest_value($user['idnum'], 'mahjong seed', mt_rand());
  
  $mahjong_seed = get_quest_value($user['idnum'], 'mahjong seed');
}

// get the allowed tiles
srand($mahjong_seed['value']);

$allowed = array_rand($mahjong_tiles, 10);

srand();
mt_srand();
// ---

$mahjong_time = get_quest_value($user['idnum'], 'last mahjong supply');
$mahjong_count = get_quest_value($user['idnum'], 'mahjong tile count');
$mahjong_points = get_quest_value($user['idnum'], 'mahjong points');

if($mahjong_time === false)
{
  add_quest_value($user['idnum'], 'last mahjong supply', 0);
  add_quest_value($user['idnum'], 'mahjong tile count', 0);
  add_quest_value($user['idnum'], 'mahjong points', 0);

  header('Location: /mahjong.php');
  exit();
}

$last_day = (int)($mahjong_time['value'] / (24 * 60 * 60));

$points = (int)$mahjong_points['value'];
$tiles = (int)$mahjong_count['value'];

if($to_day > $last_day && $points > 0)
{
  if($points <= 5)
  {
    $prizes = array('4-Sided Die', '12-hour Food Box', 'Zinc');
    $description = 'I didn\'t do as well as I would have hoped, but it was still an improvement over my usual games!';
  }
  else if($points <= 10)
  {
    $prizes = array('6-Sided Die', '24-hour Food Box', 'Brass');
    $description = 'I didn\'t do as well as I would have hoped, but it was still an improvement over my usual games!';
  }
  else if($points <= 15) // lame day max
  {
    $prizes = array('8-Sided Die', '36-hour Food Box', 'Child\'s Play');
    $description = 'I did alright!  Could have been better, but it was definitely an improvement over my usual games!';
  }
  else if($points <= 20) // typical max
  {
    $prizes = array('Stud Wall Blueprint', '48-hour Food Box', 'Child\'s Play');
    $description = 'I did alright!  Could have been better, but it was definitely an improvement over my usual games!';
  }
  else if($points <= 25)
  {
    $prizes = array('Stud Wall Blueprint', 'Honeycomb', 'Child\'s Play');
    $description = 'Our little plan worked!  Hehehe!';
  }
  else if($points <= 30) // 5 & 9 of circles max
  {
    $prizes = array('Stud Wall Blueprint', 'Honeycomb', 'Happy Fun Time Potion');
    $description = 'Our little plan worked!  Hehehe!';
  }
  else if($points <= 35)
  {
    $prizes = array('Potion Ticket', 'Honeycomb', 'Happy Fun Time Potion');
    $description = 'I didn\'t think this would work out so well, but wow!  Thanks a lot!';
  }
  else if($points <= 40) // white dragon max
  {
    $prizes = array('Potion Ticket', 'Magic Pixie Dust', 'Happy Fun Time Potion');
    $description = 'I didn\'t think this would work out so well, but wow!  Thanks a lot!';
  }
  else if($points <= 45)
  {
    $prizes = array('Potion Ticket', 'Magic Pixie Dust', 'Chameleon Scroll');
    $description = 'Oh my god, it was <em>amazing</em>!  You should have seen the look on Lakisha\'s face!  Hehe!';
  }
  else /*if($points <= 50) // red dragon max */
  {
    $prizes = array('Loaf', 'Magic Pixie Dust', 'Chameleon Scroll');
    $description = 'Oh my god, it was <em>amazing</em>!  You should have seen the look on Lakisha\'s face!  Hehe!';
  }

  $item = $prizes[array_rand($prizes)];

  update_quest_value($mahjong_seed['idnum'], mt_rand());
  update_quest_value($mahjong_time['idnum'], 0);
  update_quest_value($mahjong_count['idnum'], 0);
  update_quest_value($mahjong_points['idnum'], 0);

  add_inventory($user['user'], '', $item, 'Given to ' . $user['display'] . ' by Matalie Mansur', 'storage/incoming');

  $dialog = '
    <p>' . $user['display'] . '!  ' . $description . '</p>
    <p>Here, take this.  You deserve it!</p>
    <p><i>(You received ' . $item . '!  Find it in <a href="incoming.php">Incoming</a>.)</i></p>
    <p>If you\'d like to help out for my next game, too, I\'m looking for the following:</p><ul>
  ';

  foreach($allowed as $itemname)
    $dialog .= '<li>' . item_text_link($itemname) . '</li>';

  $dialog .= '</ul><p>And thanks, again!</p>';
}
else if($_POST['action'] == 'Give' && $mahjong_count['value'] < 5)
{
  $max = 5 - $mahjong_count['value'];
  
  $itemids = array();
  
  foreach($_POST as $key=>$value)
  {
    if($key{0} == 'i' && ($value == 'yes' || $value == 'on'))
      $itemids[] = (int)substr($key, 1);
  }

  $num_ids = count($itemids);

  if($num_ids == 0)
  {
    $dialog = '<p>Hm?  What?</p>';
  }
  else if($num_ids + $tiles > 5)
  {
    $dialog = '<p>Thanks for the offer, but I really don\'t need more than ' . (5 - $tiles) . ' tiles.</p>';
  }
  else
  {
    $command = 'SELECT itemname FROM monster_inventory WHERE idnum IN (' . implode(', ', $itemids) . ') AND user=' . quote_smart($user['user']) . ' AND location=\'storage\' AND itemname IN (\'' . implode('\', \'', array_keys($mahjong_tiles)) . '\') LIMIT ' . $num_ids;
    $items = $database->FetchMultiple($command, 'fetching items');

    $dialog = $command;

    $num_items = count($items);

    $command = 'DELETE FROM monster_inventory WHERE idnum IN (' . implode(', ', $itemids) . ') AND user=' . quote_smart($user['user']) . ' AND location=\'storage\' AND itemname IN (\'' . implode('\', \'', array_keys($mahjong_tiles)) . '\') LIMIT ' . $num_ids;
    $database->FetchNone($command, 'deleting items');

    $affected = $database->AffectedRows();

    if($affected > 0)
    {
      // if we somehow delete less than we expected, only count as many as we deleted
      if($affected < $num_items)
        $items = array_slice($items, 0, $affected);

      foreach($items as $item)
      {
        if(in_array($item['itemname'], $allowed))
          $points += $mahjong_tiles[$item['itemname']];
        else
          $points += ceil($mahjong_tiles[$item['itemname']] / 2);

        $tiles++;
      }

      update_quest_value($mahjong_time['idnum'], $now);
      update_quest_value($mahjong_count['idnum'], $tiles);
      update_quest_value($mahjong_points['idnum'], $points);

      $dialog = '<p>Neat!  Thanks, ' . $user['display'] . '!';

      if($tiles < 5)
        $dialog .= '</p><p>If you\'ve got more tiles, that\'d be great - I could use ' . (5 - $tiles) . ' more - but if not, don\'t worry about!  I\'m sure this will be enough!</p>';
      else
        $dialog .= '  I think this will do it.  You\'ve been a huge help!</p>';

      $dialog .= '<p>As my accomplice, it\'d only be fair of me to share some of the rewards, too!  Hehe!  Stop by tomorrow, and we\'ll go through my winnings!</p>';
    }
    else
      ;//$dialog = '<p>Hm?  Which tiles?</p>';
  }
}
else
{
  $dialog = '<p>Oh, how did you know I like Mahjong?  Lakisha and I play all the time... but she always kicks my butt.</p><p><em>This time</em>, however, I have a plan that\'ll guarantee my victory!  Hehehe!';

  if($tiles < 5)
  {
    $dialog .= '  Think you can help me out?</p>';

    $dialog .= '<p>Any Mahjong tiles you can give me would be great, but I <em>really need</em> the following.  And if you have multiple copies of any of them, that\'s totally alright, but I only need ' . (5 - $mahjong_count['value']) . ' more tiles.</p><ul>';

    foreach($allowed as $itemname)
      $dialog .= '<li>' . item_text_link($itemname) . '</li>';

    $dialog .= '</ul>';

    if($tiles > 0)
      $dialog .= '<p>Thanks for your help so far!  Come back tomorrow.  I\'ll try to have something for you then.</p>';
  }
  else
    $dialog .= '  Thanks for helping me out!  Come back tomorrow.  I\'ll try to have something for you then.</p>';
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Totem Pole Garden &gt; Mahjong</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Totem Pole Garden</h4>
     <ul class="tabbed">
      <li><a href="/totemgarden.php">Information</a></li>
      <li><a href="/totemgardenview.php">Browse Garden</a></li>
      <li class="activetab"><a href="/mahjong.php">Mahjong Exchange</a></li>
<?= $st_patricks ? '<li class="stpatrick"><a href="/stpatricks.php?where=totem">St. Patrick\'s Day Competition</a></li>' : '' ?>
     </ul>
<?php
// TOTEM POLE GARDEN NPC MATALIE
echo '<a href="/npcprofile.php?npc=Matalie+Mansur"><img src="/gfx/npcs/totemgirl.jpg" align="right" width="350" height="501" alt="(Totem Pole aficionado Matalie)" /></a>';

include 'commons/dialog_open.php';

echo $dialog;

include 'commons/dialog_close.php';

echo $extra;

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

if($tiles < 5)
{
  $command = 'SELECT itemname,idnum FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND location=\'storage\' AND itemname IN (\'' . implode('\', \'', array_keys($mahjong_tiles)) . '\') ORDER BY itemname ASC';
  $tiles = $database->FetchMultiple($command, 'fetching tiles in storage');

  if(count($tiles) == 0)
    echo '<p>You have no Mahjong tiles in Storage.</p>';
  else
  {
    echo '
      <form method="post">
      <table>
       <tr class="titlerow">
        <th></th><th></th><th>Item</th><th>Bonus</th>
       </tr>
    ';

    $rowclass = begin_row_class();

    foreach($tiles as $tile)
    {
      $details = get_item_byname($tile['itemname']);
    
      echo '
        <tr class="' . $rowclass . '">
         <td><input type="checkbox" name="i' . $tile['idnum'] . '" /></td>
         <td>' . item_display_extra($details) . '</td>
         <td>' . $tile['itemname'] . '</td>
         <td class="centered">' . (in_array($tile['itemname'], $allowed) ? '<b class="success">yes</b>' : '<span class="dim">no</span>') . '</td>
        </tr>
      ';
      
      $rowclass = alt_row_class($rowclass);
    }

    echo '
      </table>
      <p><input type="submit" name="action" value="Give" /></p>
      </form>
    ';
  }
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
