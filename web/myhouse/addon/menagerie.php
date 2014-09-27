<?php
require_once 'commons/init.php';

$whereat = 'home';
$wiki = 'Menagerie';
$require_petload = 'no';

$THIS_ROOM = 'Menagerie';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/zoolib.php';
require_once 'commons/utility.php';

if(!addon_exists($house, 'Menagerie'))
{
  header('Location: /myhouse.php');
  exit();
}

$first_visit = false;

$zoo = get_zoo_byuser($user['idnum'], $user['locid']);
if($zoo === false)
{
  create_zoo($user['idnum'], $user['locid']);
  $zoo = get_zoo_byuser($user['idnum'], $user['locid']);
  if($zoo === false)
  {
    echo "Failed to load your menagerie.  Try reloading this page; if the problem persists, contact <a href=\"admincontact.php\">an administrator</a>.<br />\n";
    exit();
  }

  $first_visit = true;
}

$monsters = take_apart(',', $zoo['monsters']);

if($_GET['sortby'] == 'levela')
{
  $zoo['sortby'] = 'level ASC';
  $command = 'UPDATE psypets_zoos SET sortby=' . quote_smart($zoo['sortby']) . ' WHERE userid=' . $zoo['userid'] . ' LIMIT 1';
  fetch_none($command, 'changing menagerie sort-order');
}
else if($_GET['sortby'] == 'leveld')
{
  $zoo['sortby'] = 'level DESC';
  $command = 'UPDATE psypets_zoos SET sortby=' . quote_smart($zoo['sortby']) . ' WHERE userid=' . $zoo['userid'] . ' LIMIT 1';
  fetch_none($command, 'changing menagerie sort-order');
}

if($_GET['action'] == 'consume')
{
  $monster_id = (int)$_GET['monster'];

  $i = array_search($monster_id, $monsters);
  if($i !== false)
  {
    unset($monsters[$i]);

    $zoo['monsters'] = implode(',', $monsters);

    $command = 'UPDATE psypets_zoos SET monsters=\'' . $zoo['monsters'] . '\' WHERE userid=' . $user['idnum'] . ' LIMIT 1';
    fetch_none($command, 'removing animal from zoo');

    $command = 'SELECT `name` FROM monster_prey WHERE idnum=' . $monster_id . ' LIMIT 1';
    $monster_data = fetch_single($command, 'fetching animal name');
    
    $command = 'UPDATE monster_users SET title=' . quote_smart($monster_data['name']) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    fetch_none($command, 'updating title');

    $message .= '<p class="success">Your title has been changed to "' . $monster_data['name'] . '"!</p>';
  }
  else
    $message .= '<p class="failure">You do not have that animal in your Menagerie.</p>';
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Menagerie</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Menagerie</h4>
<?php
echo $message;

room_display($house);

if(count($monsters) == 0)
{
  echo '     <p>The menagerie is empty.</p>';
}
else
{
  $command = 'SELECT COUNT(idnum) AS c FROM monster_prey';
  $data = fetch_single($command, 'fetching monster count');

  $rowclass = begin_row_class();
?>
     <h5>Animal Displays (<?= count($monsters) . '/' . $data['c'] ?>)</h5>
<?php
if(!addon_exists($house, 'Tower') || !addon_exists($house, 'Library'))
  echo '<p>If you had a Tower and Library, you might be able to learn more about the monsters your pets have captured.</p>';
?>
     <p>You may set your title to the name of any captured animal, however doing so will consume that animal!</p>
     <table>
      <tr class="titlerow">
       <th></th>
       <th></th>
       <th>Animal</th>
<?php
  if($zoo['sortby'] == 'level DESC')
    echo '       <th>Strength&nbsp;<a href="/myhouse/addon/menagerie.php?sortby=levela" alt="Sort ascending" class="activesort">&#9650;</a></th>';
  else if($zoo['sortby'] == 'level ASC')
    echo '       <th>Strength&nbsp;<a href="/myhouse/addon/menagerie.php?sortby=leveld" alt="Sort descending" class="activesort">&#9660;</a></th>';
?>
       <?php if(addon_exists($house, 'Tower') && addon_exists($house, 'Library')): ?><th>Other Properties</td><?php endif; ?>
       <th></th>
      </tr>
<?php
	$monsters = $database->FetchMultiple('SELECT * FROM monster_prey WHERE idnum IN (' . $zoo['monsters'] . ') ORDER BY ' . $zoo['sortby']);

  foreach($monsters as $monster)
  {
    if(strlen($monster['graphic']) > 0)
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
       <td><a href="/myhouse/addon/menagerie.php?action=consume&monster=<?= $monster['idnum'] ?>">Use as Title</a></td>
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
