<?php
require_once 'commons/init.php';

$whereat = 'home';
$wiki = 'Dungeon';
$THIS_ROOM = 'Dungeon';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/dungeonlib.php';
require_once 'commons/utility.php';
require_once 'commons/moonphase.php';

if(!addon_exists($house, 'Dungeon'))
{
  header('Location: /myhouse.php');
  exit();
}

$first_visit = false;

$dungeon = get_dungeon_byuser($user["idnum"], $user["locid"]);
if($dungeon === false)
{
  create_dungeon($user["idnum"], $user["locid"]);
  $dungeon = get_dungeon_byuser($user["idnum"], $user["locid"]);
  if($dungeon === false)
  {
    echo "Failed to load your dungeon.  Try reloading this page; if the problem persists, contact <a href=\"admincontact.php\">an administrator</a>.<br />\n";
    exit();
  }

  $first_visit = true;
}

$monsters = take_apart(',', $dungeon['monsters']);

$month = date('n');
$day = date('j');

if($_POST['action'] == 'open')
{
  if($month == 12 && $day < 26)
  {
    $random_items = array(
      'Spice Rack', 'Goodberries', 'Poinsettia',
      'Snappy Bricks', 'Tiny Ornithopter', 'Candy Cane', 'Holly',
      'Gold Star Stickers', 'Mintberry Swirls', 'Silly Green Hat',
      'Red Stocking', 'Gold Laurel', 'Cornbread', 'Pinecone Charm',
      'Silver Bell', 'Green Stocking',
    );
  
    $item_list = array(
      false,
      false,
      false,
      'Red Christmas Tree Ornament',
      false,
      false,
      false, // 7th of December
      'Icicle Lights',
      false,
      false,
      false,
      'Snowglobe',
      false,
      false, // 14th of December
      'Red Tinsel',
      false,
      false,
      'Green Tinsel',
      false,
      'Silver Christmas Tree Ornament',
      false, // 21st of December
      'Honey Wine',
      false,
      'Gold Christmas Tree Ornament',
      'Potted Pine',
    );

    require_once 'commons/questlib.php';

    $can_open = true;

    $advent_day = get_quest_value($user['idnum'], 'advent calendar');
    if($advent_day === false)
      add_quest_value($user['idnum'], 'advent calendar', $day);
    else
    {
      if($day == $advent_day['value'])
        $can_open = false;
      else
        update_quest_value($advent_day['idnum'], $day);
    }

    if($can_open === true)
    {
      $itemname = $item_list[$day - 1];
      if($itemname === false)
        $itemname = $random_items[array_rand($random_items)];

      $opendoor_message = '<p class="success">You open the door and peer in to the darkness.  Your eyes, finally adjusting, find ' . $itemname . ' inside!</p><p><i>(It\'s been put into your Common room.)</i></p>';

      add_inventory($user['user'], '', $itemname, 'Found inside ' . $user['display'] . '\'s Dungeon', 'home');
    }
    else
      $opendoor_message = '<p class="failure">It\'s apparently locked from the other side...</p>';
  }
  else
    $opendoor_message = '<p class="failure">It\'s apparently locked from the other side...</p>';
}

if($_GET['sortby'] == 'levela')
{
  $dungeon['sortby'] = 'level ASC';
  $command = 'UPDATE psypets_dungeons SET sortby=' . quote_smart($dungeon['sortby']) . ' WHERE userid=' . $dungeon['userid'] . ' LIMIT 1';
  fetch_none($command, 'changing dungeon sort-order');
}
else if($_GET['sortby'] == 'leveld')
{
  $dungeon['sortby'] = 'level DESC';
  $command = 'UPDATE psypets_dungeons SET sortby=' . quote_smart($dungeon['sortby']) . ' WHERE userid=' . $dungeon['userid'] . ' LIMIT 1';
  fetch_none($command, 'changing dungeon sort-order');
}

$fullmoon = is_full_moon();

if($_GET['action'] == 'consume')
{
  $monster_id = (int)$_GET['monster'];

  $i = array_search($monster_id, $monsters);
  if($i !== false)
  {
    unset($monsters[$i]);

    $dungeon['monsters'] = implode(',', $monsters);

    $command = 'UPDATE psypets_dungeons SET monsters=\'' . $dungeon['monsters'] . '\' WHERE userid=' . $user['idnum'] . ' LIMIT 1';
    fetch_none($command, 'removing animal from zoo');

    $command = 'SELECT `name`,`lycanthrope` FROM monster_monsters WHERE idnum=' . $monster_id . ' LIMIT 1';
    $monster_data = fetch_single($command, 'fetching monster name');

    if($fullmoon && $monster_data['lycanthrope'] != '')
      $monster_data['name'] = $monster_data['lycanthrope'];

    $command = 'UPDATE monster_users SET title=' . quote_smart($monster_data['name']) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    fetch_none($command, 'updating title');

    $message .= '<p class="success">Your title has been changed to "' . $monster_data['name'] . '"!</p>';
  }
  else
    $message .= '<p class="failure">You do not have that monster in your Dungeon.</p>';
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Dungeon</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Dungeon</h4>
<?php
echo $message;

room_display($house);

if($first_visit)
  echo '<p>The Dungeon is opened for the first time when you flick a switch concealed within the antechamber.</p><p>As you step inside the torches shiver at a passing gust of wind that is so perfectly timed you can only assume it\'s there for dramatic effect.</p>';
?>
<h5>Mysterious Door</h5>
<?php
if(strlen($opendoor_message) > 0)
  echo $opendoor_message;
else
{
  if($month == 12 && $day < 26)
  {
     echo '<p>There is set, in the back wall of the dungeon, a plain wooden door, and-- <strong>hold on a sec</strong>... there\'s a <em>wreath</em> on it!  Who put that on there?</p>';
?>
     <form method="post">
     <input type="hidden" name="action" value="open" />
     <p><input type="submit" value="Open Door" /></p>
     </form>
<?php
  }
  else
    echo '<p>There is set, in the back wall of the dungeon, a plain wooden door, and though it bears no lock, it refuses to open.</p>';
}

$num_monsters = count($monsters);

if($num_monsters == 0)
  echo '<p>The dungeon is empty.</p>';
else
{
  $command = 'SELECT COUNT(idnum) AS c FROM monster_monsters';
  $data = fetch_single($command, 'fetching monster count');
  $data['c'] = $data['c'] - 1;
  
  if($num_monsters > $data['c'])
    $num_monsters = $data['c'];

  $rowclass = begin_row_class();
?>
     <h5>Dungeon Cells (<?= $num_monsters . '/' . $data['c'] ?>)</h5>
<?php
if(!addon_exists($house, 'Tower') || !addon_exists($house, 'Library'))
  echo '<p>If you had a Tower and Library, you might be able to learn more about the monsters your pets have captured.</p>';
?>
     <p>You may set your title to the name of any captured monster, however doing so will consume that monster!</p>
     <table>
      <tr class="titlerow">
       <th></th>
       <th></th>
       <th>Monster</th>
<?php
  if($dungeon['sortby'] == 'level DESC')
    echo '       <th>Strength&nbsp;<a href="/myhouse/addon/dungeon.php?sortby=levela" alt="Sort ascending" class="activesort">&#9660;</a></th>';
  else if($dungeon['sortby'] == 'level ASC')
    echo '       <th>Strength&nbsp;<a href="/myhouse/addon/dungeon.php?sortby=leveld" alt="Sort descending" class="activesort">&#9650;</a></th>';
?>
       <?php if(addon_exists($house, 'Tower') && addon_exists($house, 'Library')): ?><th>Other Properties</td><?php endif; ?>
       <th></th>
      </tr>
<?php
	$monsters = $database->FetchMultiple('SELECT * FROM monster_monsters WHERE idnum IN (' . $dungeon['monsters'] . ') ORDER BY ' . $dungeon['sortby']);

  foreach($monsters as $monster)
  {
    if($fullmoon && $monster['lycanthrope'] != '')
    {
      $monster['name'] = $monster['lycanthrope'];
      $monster['type'] = 'lycanthrope';
      $graphic = '<img src="//' . $SETTINGS['static_domain'] . '/gfx/monsters/were_' . $monster['graphic'] . '" alt="" />';
    }
    else if(strlen($monster['graphic']) > 0)
      $graphic = '<img src="//' . $SETTINGS['static_domain'] . '/gfx/monsters/' . $monster['graphic'] . '" alt="" />';
    else
      $graphic = '';

?>
      <tr class="<?= $rowclass ?>">
       <td class="centered"><?= $graphic ?></td>
       <td><?php
    if($monster['description'] != '')
      echo '<img src="/gfx/petlog_new.png" width="18" height="16" alt="" onmouseover="Tip(\'' . tip_safe($monster['description']) . '\', WIDTH, 250)" />';
?></td>
       <td><?= $monster['name'] ?><br /><i><?= $monster['type'] ?></i></td>
       <td class="centered"><?= $monster['level'] ?></td>
<?php
    if(addon_exists($house, 'Tower') && addon_exists($house, 'Library'))
    {
      echo '<td>';
    
      if($monster['min_stealth'] > 0)
        echo 'Evasive<br />';
      if($monster['min_stamina'] > 0)
        echo 'Lives in a harsh environment<br />';
      if($monster['min_athletics'] > 0)
        echo 'Athletic<br />';
      if($monster['min_wits'] > 0)
        echo 'Crafty<br />';
      if($monster['is_vampire'] == 'yes')
        echo 'Is a vampire<br />';
      if($fullmoon && $monster['lycanthrope'] != '')
        echo 'Is a werecreature<br />';
      if(!$fullmoon && $monster['lycanthrope'] != '')
        echo 'Is a werecreature (during a full moon)<br />';
      if($monster['is_berries'] == 'yes')
        echo 'Is made of berries<br />';
      if($monster['is_flying'] == 'yes')
        echo 'Flies<br />';
      if($monster['is_burny'] == 'yes')
        echo 'Burns<br />';
      if($monster['is_sensitive_to_cold'] == 'yes')
        echo 'Is sensitive to the cold<br />';
      if($monster['is_in_space'] == 'yes')
        echo 'Lives in space!<br />';
      if($monster['is_deep_sea'] == 'yes')
        echo 'Lives in the deep sea<br />';

      echo '</td>';
    }
?>
       <td><a href="/myhouse/addon/dungeon.php?action=consume&monster=<?= $monster['idnum'] ?>">Use as Title</a></td>
      </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
     </table>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
