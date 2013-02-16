<?php
$wiki = 'Totem_Pole_Garden';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/totemlib.php';

if($user['show_totemgardern'] == 'no')
{
  header('Location: /myhouse.php');
  exit();
}

$st_patricks = (date('M d') == 'Mar 17');

$page = (int)$_GET['page'];
$maxpage = ceil(get_num_totems() / 20);

if($page < 1)
  $page = 1;
else if($page > $maxpage)
  $page = $maxpage;

$poles = get_totems_byscore(($page - 1) * 20, 20);

$pages = paginate($maxpage, $page, '/totemgardenview.php?page=%s');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Totem Pole Garden &gt; Browse</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="totemgarden.php">The Totem Pole Garden</a> &gt; Browse</h4>
     <ul class="tabbed">
      <li><a href="totemgarden.php">Information</a></li>
      <li class="activetab"><a href="totemgardenview.php">Browse Garden</a></li>
      <li><a href="mahjong.php">Mahjong Exchange</a></li>
<?= $st_patricks ? '<li class="stpatrick"><a href="/stpatricks.php?where=totem">St. Patrick\'s Day Competition</a></li>' : '' ?>
     </ul>
<?php
// TOTEM POLE GARDEN NPC MATALIE
echo '<a href="npcprofile.php?npc=Matalie Mansur"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/totemgirl.jpg" align="right" width="350" height="501" alt="(Totem Pole aficionado Matalie)" /></a>';

include 'commons/dialog_open.php';
?>
     <p>This is a list of all the totems currently being worked on.  I got bored one day and sorted them by their rating... hope you find it useful!</p>
<?php
include 'commons/dialog_close.php';
?>
     <ul><li><a href="totempoles.php">Show me my Totem Pole</a></li></ul>
     <?= $pages ?>
     <table>
      <tr class="titlerow">
       <th></th>
       <th>Resident</th>
       <th>Rating</th>
       <th>Height</th>
      </tr>
<?php
$row_class = begin_row_class();

$place = $page * 20 - 19;

foreach($poles as $pole)
{
  $totem_user = get_user_byid($pole['userid']);
  $height = substr_count($pole['totem'], ',') + 1;

  if($totem_user === false)
    $display = '<i class="dim">[departed #' . $pole['userid'] . ']</i>';
  else
    $display = '<a href="/totempoles.php?resident=' . link_safe($totem_user['display']) . '">' . $totem_user['display'] . '</a>';
?>
      <tr class="<?= $row_class ?>">
       <td class="centered">#<?= $place ?></td>
       <td><?= $display ?></td>
       <td class="centered"><?= totem_rating($pole['rating']) ?></td>
       <td class="centered"><?= $height ?></td>
      </tr>
<?php
  $place++;

  $row_class = alt_row_class($row_class);
}
?>
     </table>
     <?= $pages ?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
