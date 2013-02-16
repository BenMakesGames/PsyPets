<?php
$child_safe = false;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/formatting.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/checkpet.php';
require_once 'commons/userlib.php';
require_once 'commons/utility.php';
require_once 'commons/grouplib.php';
require_once 'commons/tiles.php';

$TILE_ITEM_NAME = 'Town Square';

$groupid = (int)$_GET['id'];
$group = get_group_byid($groupid);

if($group === false)
{
  header('Location: ./groupindex.php');
  exit();
}

$ranks = get_group_ranks($groupid);
$members = explode(',', $group['members']);
$organizer = get_user_byid($group['leaderid'], 'idnum,display,graphic');

$a_member = is_a_member($group, $user['idnum']);

if($a_member)
{
  $rankid = get_member_rank($group, $user['idnum']);
  $can_edit_map = (rank_has_right($ranks, $rankid, 'mapper') || $group['leaderid'] == $user['idnum']);
}
else
  $can_edit_map = false;

if(!$can_edit_map)
{
  header('Location: ./grouptown.php?id=' . $groupid);
  exit();
}

$x = (int)$_GET['x'];
$y = (int)$_GET['y'];

if($x < 0 || $x > 19 || $y < 0 || $y > 14)
{
  header('Location: ./grouptown.php?id=' . $groupid);
  exit();
}

if($_POST['action'] == 'edit' && $user['user'] == 'telkoth')
  EditTile($groupid, $x, $y, $_POST['newbase'], $_POST['newdecor']);

$tile = GetTile($groupid, $x, $y);

if($tile === false)
{
  CreateMap($groupid);

  $tile = GetTile($groupid, $x, $y);

  if($tile === false)
    die('Error loading this group\'s town map.  LAME.  (Also, an admin should be notified if this problem persists >_>');
}

if($_POST['action'] == 'place')
{
  $itemid = (int)$_POST['tileid'];
  
  $command = 'SELECT data FROM monster_inventory WHERE idnum=' . $itemid . ' AND itemname=' . quote_smart($TILE_ITEM_NAME) . ' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\' LIMIT 1';
  $item = $database->FetchSingle($command, 'fetching tile');

  if($item !== false)
  {
    $data = $item['data'];

    $okay = true;

    if($data{0} == 'd')
      $tile['decor'] = substr($data, 1);
    else if($data{0} == 'b')
      $tile['base'] = substr($data, 1);
    else
      $okay = false;

    if($okay)
    {
      EditTile($groupid, $x, $y, $tile['base'], $tile['decor']);
    
      delete_inventory_byid($itemid);

      $command = 'UPDATE psypets_groups SET towntiles=towntiles+1 WHERE idnum=' . $groupid . ' LIMIT 1';
      $database->FetchNone($command, 'updating tile count');

      header('Location: ./grouptown.php?id=' . $groupid);
      exit();
    }
  }
}

include 'commons/html.php';
?>
 <head>
<?php include "commons/head.php"; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $group['name'] ?> &gt; Town</title>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><?= $group['name'] ?>  &gt; Town</h4>
<?php
$activetab = 'town';
include 'commons/grouptabs.php';
?>
     <table>
      <tr>
       <th>Decor</th>
       <td><?= $tile['decor'] == 0 ? 'none' : '<img src="gfx/town/' . $TILES_DECOR[$tile['decor']] . '.png" width="24" height="24" />' ?></td>
      </tr>
      <tr>
       <th>Base</th>
       <td><?= $tile['base'] == 0 ? 'none' : '<img src="gfx/town/' . $TILES_BASE[$tile['base']] . '.png" width="24" height="24" />' ?></td>
      </tr>
     </table>
<h5>Place a Tile?</h5>
<?php
$command = 'SELECT idnum,data FROM monster_inventory WHERE itemname=' . quote_smart($TILE_ITEM_NAME) . ' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\'';
$items = $database->FetchMultiple($command, 'fetching tiles');

if(count($items) == 0)
  echo '<p>You don\'t have any ' . $TILE_ITEM_NAME . 's in your Storage.</p>';
else
{
  echo '<form action="grouptownedit.php?id=' . $groupid . '&x=' . $x . '&y=' . $y . '" method="post">' .
       '<table border="0" cellspacing="0" cellpadding="4">' .
       '<tr class="titlerow"><th></th><th>Tile</th><th>Type</th></tr>';

  $row = begin_row_class();

  foreach($items as $item)
  {
    $tile_data = $item['data'];
    if(strlen($tile_data) == 0)
    {
      $tile_data = GenerateTile();
      $command = 'UPDATE monster_inventory SET data=' . quote_smart($tile_data) . ' WHERE idnum=' . $item['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'setting tile data');
    }

    if($tile_data{0} == 'b')
    {
      $type = 'Base';
      $graphic = $TILES_BASE[substr($tile_data, 1)];
    }
    else if($tile_data{0} == 'd')
    {
      $type = 'Decor';
      $graphic = $TILES_DECOR[substr($tile_data, 1)];
    }

    echo '<tr class="' . $row . '">' .
         '<td><input type="radio" name="tileid" value="' . $item['idnum'] . '" /></td>' .
         '<td><img src="gfx/town/' . $graphic . '.png" alt="" width="48" height="48" /></td>' .
         '<td>' . $type . '</td>' .
         '</tr>';

    $row = alt_row_class($row);
  }
  
  echo '</table>' .
       '<p>Once a tile is placed, it can never be removed!  You can, however, cover up a tile with another.</p>' .
       '<p><input type="hidden" name="action" value="place" /><input type="submit" value="Place Tile" /></p>' .
       '</form>';
}

if($user['user'] == 'telkoth')
{
?>
<h5>h4x!</h5>
<form action="grouptownedit.php?id=<?= $groupid ?>&x=<?= $x ?>&y=<?= $y ?>" method="post">
<p>New decor: <input name="newdecor" value="<?= $tile['decor'] ?>" /></p>
<p>New base: <input name="newbase" value="<?= $tile['base'] ?>" /></p>
<p><input type="hidden" name="action" value="edit" /><input type="submit" value="Edit" /></p>
</form>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
