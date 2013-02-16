<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";

if($admin['manageitems'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

if($_GET['edit'] == 'prey')
{
  $param = 'prey';
  $tab = 'prey';

  $monsters = $database->FetchMultiple(('
    SELECT *
    FROM monster_prey
    WHERE activity=\'hunt\'
    ORDER BY level ASC
  ');
}
else if($_GET['edit'] == 'fish')
{
  $param = 'prey';
  $tab = 'fish';

  $monsters = $database->FetchMultiple(('
    SELECT *
    FROM monster_prey
    WHERE activity=\'fish\'
    ORDER BY level ASC
  ');
}
else
{
  $param = 'monster';
  $tab = 'monster';

  $monsters = $database->FetchMultiple(('
    SELECT *
    FROM monster_monsters
    ORDER BY level ASC
  ');
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Monster & Prey Editor</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Monster & Prey Editor</h4>
     <ul class="tabbed">
      <li<?= $tab == 'monster' ? ' class="activetab"' : '' ?>><a href="/admin/monstereditor.php?edit=monster">Monsters</a></li>
      <li<?= $tab == 'prey' ? ' class="activetab"' : '' ?>><a href="/admin/monstereditor.php?edit=prey">Prey</a></li>
      <li<?= $tab == 'fish' ? ' class="activetab"' : '' ?>><a href="/admin/monstereditor.php?edit=fish">"Fish"</a></li>
     </ul>
     <ul>
      <li><a href="/admin/newmonster.php?edit=<?= $param ?>">New <?= $param ?></a></li>
      <li><a href="/admin/monstereditor.php?edit=<?= $param ?>&amp;check=1">Check for bad loot</a></li>
     </ul>
<table>
<tr class="titlerow">
 <th></th>
 <th>Level</th>
 <th></th>
 <th>Name & Type</th>
 <th>Loot</th>
 <th><nobr>Drop Rate</nobr></th>
 <th>(Expected)</th>
</tr>
<?php
$bgcolor = begin_row_class();

foreach($monsters as $monster)
{
  $prizes = take_apart(',', $monster['prizes']);

  if($_GET['check'] == 1)
  {
    $real_prizes = array();

    foreach($prizes as $prize)
    {
      $rate = explode('|', $prize);
      $itemname = $rate[1];

      $item = get_item_byname($itemname);
      if($item === false)
        $real_prizes[] = '<span class="failure">' . $prize . '</span>';
      else
        $real_prizes[] = $prize;
    }
  }
  else
    $real_prizes = $prizes;
?>
<tr class="<?= $bgcolor ?>">
 <td valign="top"><a href="/admin/editmonster.php?<?= $param ?>=<?= urlencode($monster['name']) ?>">edit</a></td>
 <td valign="top" class="centered"><?php
  echo $monster['level'];

  if($monster['needs_key'] > 0)
    echo '<br />(key #' . $monster['needs_key'] . ')';
?></td>
 <td valign="top" class="centered"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/monsters/<?= $monster['graphic'] ?>" width="48" height="48" alt="" /></td>
 <td valign="top"><nobr><?= $monster['name'] ?></nobr><br /><i><?= $monster['type'] ?></i></td>
 <td valign="top"><?php
  $drop_rate = 1.00;
  foreach($real_prizes as $prize)
  {
    $rate = explode('|', $prize);

    $drop_rate *= (1 - ($rate[0] / 1000));
    echo ($rate[0] / 10) . "% - <a href=\"/encyclopedia2.php?item=" . link_safe($rate[1]) . "\">" . $rate[1] . "</a><br />";
  }
 ?></td>
 <td valign="top" align="right"><?= 100 - round($drop_rate * 100) ?>%</td>
 <td valign="top" align="right">(<?= 70 + $monster['level'] ?>%)</td>
</tr>
<?php
  $bgcolor = alt_row_class($bgcolor);
}
?>
</table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
