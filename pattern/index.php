<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$wiki = 'The_Pattern';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/mazelib.php';
require_once 'commons/messages.php';

if($user['show_pattern'] == 'no')
{
  header('Location: /myhouse.php');
  exit();
}

if($user['mazeloc'] == 0)
{
  $count = fetch_single('SELECT COUNT(idnum) AS c FROM psypets_maze');
  $max_count = floor($count['c'] * 3 / 4);

  $command = 'SELECT COUNT(idnum) AS c FROM psypets_maze WHERE idnum>' . $max_count . ' AND obstacle=\'none\' AND tile!=\'1111\'';
  $data = fetch_single($command, 'fetching possible tiles');

  $num_locations = (int)$data['c'];
  $picked_location = mt_rand(1, $num_locations);

  $tile = fetch_single('SELECT idnum FROM psypets_maze WHERE idnum>' . $max_count . ' AND obstacle=\'none\' AND tile!=\'1111\' LIMIT ' . $picked_location . ',1');
  if($tile === false)
    $newloc = 1;
  else
    $newloc = $tile['idnum'];

  maze_move_user($user, $newloc);
}

$this_tile = get_maze_byid($user['mazeloc']);

if($this_tile === false)
{
  echo "Uh oh:  You seem to be located somewhere that doesn't exist in the maze.  If this keeps happening, you should probably contact <a href=\"admincontact.php\">an administrator</a>.<br />\n";
  exit();
}

$x = $this_tile['x'];
$y = $this_tile['y'];
$z = $this_tile['z'];
$min_x = $x - 2;
$max_x = $x + 2;
$min_y = $y - 2;
$max_y = $y + 2;

$tiles = $database->FetchMultiple('
  SELECT *
  FROM psypets_maze
  WHERE
    x BETWEEN ' . $min_x . ' AND ' . $max_x . '
    AND y BETWEEN ' . $min_y . ' AND ' . $max_y . '
    AND z=' . $z . '
  LIMIT 25
');

$maze = array();

for($i = 0; $i < 25; ++$i)
  $maze[$i] = false;

foreach($tiles as $tile)
{
  $i = ($tile['y'] - $min_y) * 5 + $tile['x'] - $min_x;
  $maze[$i] = $tile;
}

$rooms['storage'] = 'Storage';
$rooms['home'] = 'Common';

if(strlen($house['rooms']) > 0)
{
  $m_rooms = explode(',', $house['rooms']);
  foreach($m_rooms as $room)
    $rooms['home/' . $room] = $room;
}

require_once 'commons/questlib.php';

$mazetiles = get_quest_value($user['idnum'], 'maze tiles');
$mazetiles_count = (int)$mazetiles['value'];

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Pattern</title>
<?php include 'commons/head.php'; ?>
  <style type="text/css">
   #compass
   {
     width: 150px;
     height: 150px;
     position: relative;
     background-image: url(//<?= $SETTINGS['static_domain'] ?>/gfx/maze/compass.png);
     margin: 85px 0 5px;
   }

   #themaze-controls
   {
     width: 150px;
   }
   
   #compass div
   {
     position: absolute;
     width: 50px;
     height: 50px;
   }

   #direction-north
   {
     left: 50px;
     top: 0px;
   }

   #direction-east
   {
     left: 100px;
     top: 50px;
   }

   #direction-south
   {
     left: 50px;
     top: 100px;
   }

   #direction-west
   {
     left: 0px;
     top: 50px;
   }

   #direction-up
   {
     left: 100px;
     top: 0px;
   }

   #direction-down
   {
     left: 100px;
     top: 100px;
   }
   
   #themaze
   {
     float: right;
     padding: 0;
     margin: 0 0 1em 1em;
     width: 490px;
     height: 320px;
     position: relative;
     border-left: 1px solid #ccc;
     padding-left: 10px;
   }
   
   #themaze-view
   {
     position: absolute;
     top: 0;
     right: 0;
   }
   
   #los_mask
   {
     position: absolute;
     top: 0;
     right: 0;
   }

   #themaze-view td
   {
     width: 64px;
     height: 64px;
     padding: 0;
     margin: 0;
     border: 0;
   }
   
   .maze-object
   {
     position: relative;
     z-index: 100;
   }
  </style>
  <script type="text/javascript">
  $(function() {
    $('#item-room').change(function() {
      $('#item-room').attr('disabled', 'disabled');
      $('#item-room-throbber').css({ display: 'inline' });

      $.post(
        '/pattern/change_item_room.json.php',
        {
          room: $('#item-room').val()
        },
        function(data)
        {
          $('#item-room-throbber').css({ display: 'none' });
          $('#item-room').val(data.room);
          $('#item-room').removeAttr('disabled');
        },
        'json'
      );
    });
  });
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
    <h4>The Pattern</h4>
    <ul class="tabbed">
     <li class="activetab"><a href="/pattern/">The Pattern</a></li>
     <li><a href="/pattern/exchange.php">Exchanges</a></li>
    </ul>
    <div id="themaze">
     <table id="themaze-view">
      <tr>
<?php
foreach($maze as $index=>$tile)
{
  if($index % 5 == 0 && $index > 0)
    echo '</tr><tr>';

  $rel_x = $index % 5;
  $rel_y = (int)($index / 5);
  $x = $rel_x + $min_x;
  $y = $rel_y + $min_y;
  $rrel_x = $rel_x - 2;
  $rrel_y = $rel_y - 2;

  if($index == 0 || $index == 4 || $index == 20 || $index == 24)
    echo '<td></td>';
  else if($tile === false)
    echo '<td style="background: url(/gfx/maze/none.png);"></td>';
  else
  {
    $background = $tile['tile'];

    echo '<td style="background: url(/gfx/maze/' . $background . '.png);" align="center" valign="center">';

    if($user['mazeloc'] == $tile['idnum'])
      echo '<img class="maze-object" src="/gfx/emote/hee.gif" width="16" height="16" title="You are here" />';
    else if($tile['obstacle'] != 'none')
    {
      $obstacle_item = get_item_byname($tile['obstacle']);

      echo item_display($obstacle_item, 'class="maze-object" title="' . htmlentities($tile['obstacle']) . '"');
    }
    else if($tile['players'] > 0)
      echo '<img class="maze-object" src="/gfx/maze/others.png" width="16" height="16" title="' . $tile['players'] . ' ' . ($tile['players'] == 1 ? 'person is' : 'people are') . ' here" />';
    
    if($tile['feature'] != 'none')
    {
      $title = ucwords(str_replace('_', ' ', $tile['feature']));
      echo '<img class="maze-object" src="/gfx/maze/' . $tile['feature'] . '.png" alt="' . $title . '" title="' . $title . '" />';
    }

    echo '</td>';
  }
}
?>
      </tr>
     </table>
     <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/maze/los_mask.png" width="320" height="320" alt="" id="los_mask" />
     <div id="themaze-controls">
<?php

if($user['mazemp'] == 0)
  echo '<p><em>You cannot move without MP!</em>  You must roll a die to receive more...</p>';
else
{
  if($maze[7] === false)
    $north = '<a href="/pattern/addtile.php?dir=n"><img src="//' . $SETTINGS['static_domain'] . '/gfx/maze/direction_n.png" width="50" height="50" alt="Add Tile" /></a>';
  else
    $north = '<a href="/pattern/travel.php?dir=n"><img src="//' . $SETTINGS['static_domain'] . '/gfx/maze/direction_n.png" width="50" height="50" alt="Travel" /></a>';

  if($maze[11] === false)
    $west = '<a href="/pattern/addtile.php?dir=w"><img src="//' . $SETTINGS['static_domain'] . '/gfx/maze/direction_w.png" width="50" height="50" alt="Add Tile" /></a>';
  else
    $west = '<a href="/pattern/travel.php?dir=w"><img src="//' . $SETTINGS['static_domain'] . '/gfx/maze/direction_w.png" width="50" height="50" alt="Travel" /></a>';

  if($maze[13] === false)
    $east = '<a href="/pattern/addtile.php?dir=e"><img src="//' . $SETTINGS['static_domain'] . '/gfx/maze/direction_e.png" width="50" height="50" alt="Add Tile" /></a>';
  else
    $east = '<a href="/pattern/travel.php?dir=e"><img src="//' . $SETTINGS['static_domain'] . '/gfx/maze/direction_e.png" width="50" height="50" alt="Travel" /></a>';

  if($maze[17] === false)
    $south = '<a href="/pattern/addtile.php?dir=s"><img src="//' . $SETTINGS['static_domain'] . '/gfx/maze/direction_s.png" width="50" height="50" alt="Add Tile" /></a>';
  else
    $south = '<a href="/pattern/travel.php?dir=s"><img src="//' . $SETTINGS['static_domain'] . '/gfx/maze/direction_s.png" width="50" height="50" alt="Travel" /></a>';

  if($this_tile['feature'] == 'ladder_up')
    $up = '<a href="/pattern/travel.php?dir=u"><img src="//' . $SETTINGS['static_domain'] . '/gfx/maze/direction_up.png" width="50" height="50" alt="Travel" /></a>';

  if($this_tile['feature'] == 'ladder_down')
    $down = '<a href="/pattern/travel.php?dir=d"><img src="//' . $SETTINGS['static_domain'] . '/gfx/maze/direction_down.png" width="50" height="50" alt="Travel" /></a>';
?>
<div id="compass">
<div id="direction-north"><?= $north ?></div>
<div id="direction-east"><?= $east ?></div>
<div id="direction-south"><?= $south ?></div>
<div id="direction-west"><?= $west ?></div>
<div id="direction-up"><?= $up ?></div>
<div id="direction-down"><?= $down ?></div>
</div>
<?php
  echo '<p style="text-align:center;">' . $user['mazemp'] . ' move' . ($user['mazemp'] != 1 ? 's' : '') . ' remaining</p>';
}
?>
     </div>
    </div>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET["msg"]));

if($error_message)
  echo '<p>' . $error_message . '</p>';

if($maze[12]['feature'] == 'gate')
{
  $this_gate = fetch_single('
    SELECT name
    FROM psypets_maze_gates
    WHERE
      x=' . $this_tile['x'] . '
      AND y=' . $this_tile['y'] . '
      AND z=' . $this_tile['z'] . '
    LIMIT 1
  ');

  $gates = fetch_multiple('
    SELECT *
    FROM psypets_maze_gates
    WHERE
      x!=' . $this_tile['x'] . ' OR
      y!=' . $this_tile['y'] . ' OR
      z!=' . $this_tile['z'] . '
  ');
?>
<h5><?= $this_gate['name'] ?></h5>
<p>A Gate stands here.  For 1 MP, you can travel through it, to another gate...</p>
<table>
 <thead>
 <tr class="titlerow">
  <th></th><th>Gate Name</th><th>Direction</th>
 </tr>
 </thead>
 <tbody>
<?php
  $rowclass = begin_row_class();

  foreach($gates as $gate)
  {
    $x_diff = $this_tile['x'] - $gate['x'];
    $y_diff = $this_tile['y'] - $gate['y'];
    $z_diff = $this_tile['z'] - $gate['z'];

    if($x_diff == 0)
      $slope = 10; // close enough to infinity as we need :P
    else
      $slope = $y_diff / $x_diff;

    if($slope >= 2 || $slope <= -2)
    {
      if($y_diff > 0)
        $direction = 'North';
      else
        $direction = 'South';
    }
    else if($slope >= 1/2)
    {
      if($y_diff > 0)
        $direction = 'Northwest';
      else
        $direction = 'Southeast';
    }
    else if($slope >= -1/2)
    {
      if($x_diff < 0)
        $direction = 'East';
      else
        $direction = 'West';
    }
    else if($slope > -2)
    {
      if($y_diff > 0)
        $direction = 'Northeast';
      else
        $direction = 'Southwest';
    }
    
    if($z_diff < 0)
      $direction .= ', ' . (-$z_diff) . ' floor' . ($z_diff != -1 ? 's' : '') . ' down';
    else if($z_diff > 0)
      $direction .= ', ' . ($z_diff) . ' floor' . ($z_diff != 1 ? 's' : '') . ' up';
    else
      $direction = ', on this floor';
?>
 <tr class="<?= $rowclass ?>">
  <td><a href="/pattern/gate.php?gate=<?= $gate['idnum'] ?>">Go</a></td>
  <td><?= $gate['name'] ?></td>
  <td><?= $direction ?></td>
 </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
 </tbody>
</table>
<?php
}
else if($maze[12]['feature'] == 'shop')
{
?>
<h5>Shop</h5>
<p>(You must have the items to trade in your Storage.)</p>
<form action="/pattern/shop.php" method="post">
<table>
 <thead>
  <tr class="titlerow"><th></th><th>Asking</th><th>Giving</th></tr>
 </thead>
 <tbody>
  <tr class="row">
   <td><input type="radio" name="exchange" value="1" /></td>
   <td>3 <?= item_text_link('Pyrium') ?>, and<br />3 <?= item_text_link('Chalk') ?></td>
   <td>25 MP (if you have 75 MP or less)</td>
  </tr>
  <tr class="altrow">
   <td><input type="radio" name="exchange" value="2" /></td>
    <td>12 <?= item_text_link('Red Dye') ?></td>
    <td>Change your title to "Bull of Minos"</td>
   </td>
  </tr>
  <tr class="row">
   <td><input type="radio" name="exchange" value="3" /></td>
   <td>4 <?= item_text_link('Celery and Peanut Butter') ?></td>
   <td>Teleport you to another, random shop</td>
  </tr>
  <tr class="altrow">
   <td><input type="radio" name="exchange" value="5" /></td>
   <td>20 <?= item_text_link('Paper') ?>, and<br />20 <?= item_text_link('Black Dye') ?></td>
   <td><?= item_text_link('Book of Creatures I') ?></td>
  </tr>
  <tr class="row">
   <td><input type="radio" name="exchange" value="4" /></td>
   <td>50 <?= item_text_link('Coal') ?>,<br />50 <?= item_text_link('Small Giamond') ?>, and<br />50 <?= item_text_link('Silver') ?></td>
   <td><?= item_text_link('Laoc\'s Spiritstaff') ?></td>
  </tr>
 </tbody>
</table>
<p><input type="submit" name="action" value="Trade" /></p>
</form>
<?php
}
else if($this_tile['feature'] == 'weird')
{
?>
<h5>Weird Portal</h5>
<p>There's a swirling, shimmering portal here...</p>
<?php
  if($user['mazemp'] > 0)
    echo '<ul><li><a href="/pattern/weird.php">Go in it! (1 MP)</a></li></ul>';
  else
    echo '<ul><li class="dim">Go in it! (1 MP)</li></ul>';
}

if($this_tile['feature'] == 'none' && $this_tile['obstacle'] == 'none')
{
  $data = fetch_single('
    SELECT COUNT(idnum) AS c
    FROM monster_inventory
    WHERE
      itemname=\'Magic Ladder\'
      AND user=' . quote_smart($user['user']) . '
      AND location=\'storage\'
  ');

  $ladders = (int)$data['c'];

  if($ladders > 0)
  {
    $tile_up = get_maze_bycoord($x, $y, $z - 1);
    $tile_down = get_maze_bycoord($x, $y, $z + 1);
?>
  <h5>Magic Ladder!</h5>
  <p>You have <?= $ladders > 1 ? $ladders : 'a' ?> Magic Ladder<?= $ladders != 1 ? 's' : '' ?> in Storage.  Would you like to use <?= $ladders == 1 ? 'it' : 'one' ?>?</p>
  <ul>
<?php
    if($tile_up === false || ($tile_up['feature'] == 'none' && $tile_up['obstacle'] == 'none'))
      echo '<li><a href="/pattern/ladder.php?dir=u">Let\'s go up!</a></li>';
    else
      echo '<li class="dim">Something is blocking you from building a ladder up.</li>';

    if($tile_down === false || ($tile_down['feature'] == 'none' && $tile_down['obstacle'] == 'none'))
      echo '<li><a href="/pattern/ladder.php?dir=d">Let\'s go down!</a></li>';
    else
      echo '<li class="dim">Something is blocking you from building a ladder down.</li>';
?>
  </ul>
<?php
  }
}

if($user['childlockout'] == 'no')
{

$command = 'SELECT idnum,authorid,message FROM psypets_maze_messages WHERE mazeloc=' . $this_tile['idnum'] . ' ORDER BY idnum ASC';
$messages = fetch_multiple($command, 'fetching messages at this location');
?>
<h5>Messages</h5>
<?php
if(count($messages) > 0)
{
  echo '<table>';
  $rowclass = begin_row_class();

  foreach($messages as $message)
  {
    $author = get_user_byid($message['authorid'], 'display,idnum');
    echo '<tr class="' . $rowclass . '"><th>' . resident_link($author['display']) . ':</th><td>' . $message['message'] . '</td><td><a href="/pattern/erasemessage.php?id=' . $message['idnum'] . '"><i>Erase (1 MP)</i></a></td></tr>';
    $rowclass = alt_row_class($rowclass);
  }

  echo '</table>';
}
?>
<form action="/pattern/message.php" method="post">
<p>Write a message: <input name="message" maxlength="40" /> <input type="submit" value="Requires 1 Chalk" class="bigbutton" />
</form>
<?php
}

if($this_tile['players'] > 1)
{
  $other_players = $this_tile['players'] - 1;

  $command = 'SELECT display FROM monster_users WHERE mazeloc=' . $this_tile['idnum'] . ' AND idnum!=' . $user['idnum'] . ' LIMIT ' . $other_players;
  $players = fetch_multiple($command, 'fetching other players');
?>
<h5>Other Residents Here (<?= $other_players ?>)</h5>
<ul>
<?php
  foreach($players as $player)
    echo '<li>' . resident_link($player['display']) . '</li>';
?>
</ul>
<?php
}
?>
<h5>BTW</h5>
<ul>
 <li>When I encounter obstacles, take my items from <select id="item-room"><?php
foreach($rooms as $key=>$room)
{
  if($key == $user['pattern_item_room'])
    echo '<option value="' . $key . '" selected="selected">' . $room . '</option>';
  else
    echo '<option value="' . $key . '">' . $room . '</option>';
}
?></select> <span id="item-room-throbber" style="display:none;"><img src="/gfx/throbber.gif" width="16" height="16" alt="" class="inlineimage" /></span></li>
<?php if($mazetiles_count > 0) echo '<li>I have placed a total of ' . $mazetiles_count . ' maze piece' . ($mazetiles_count == 1 ? '' : 's') . '</li>'; ?>
</ul>
<div style="clear:both;"></div>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
