<?php
require_once 'commons/init.php';

$require_login = 'no';
$require_petload = 'no';
$reading_tos = true;

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";

if($_GET['order'] == 'title asc')
  $order = 'title asc';
else if($_GET['order'] == 'names asc')
  $order = 'names asc';
else
  $order = 'idnum desc';

$command = 'SELECT COUNT(*) AS c FROM monster_graphics WHERE rights IN (\'pd_released\', \'pd_found\')';
$data = fetch_single($command, 'fetching graphic count');

$count = $data['c'];

$max_pages = (int)(($count - 1) / 20) + 1;

if(is_numeric($_GET['page']) && (int)$_GET['page'] == $_GET['page'] && $_GET['page'] > 0 && $_GET['page'] <= $max_pages)
  $page = (int)$_GET['page'];
else
  $page = 1;

$command = "SELECT * FROM monster_graphics WHERE rights IN ('pd_released', 'pd_found') ORDER BY $order LIMIT " . (($page - 1) * 20) . ",20";
$notices = fetch_multiple($command, 'fetching copyright notices');

$pages = paginate($max_pages, $page, '/meta/copyright_smallgfx_pd.php?order=$order&page=%s');

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; Help &gt; Copyright Information &gt; Public Domain Item, Pet and Avatar Graphics</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/help/">Help</a> &gt; Copyright Information &gt; Public Domain Item, Pet and Avatar Graphics</h4>
<ul class="tabbed">
 <li><a href="/meta/copyright.php">General Copyright Information</a></li>
 <li class="activetab"><a href="/meta/copyright_smallgfx.php">Item, Pet and Avatar Graphics</a></li>
 <li><a href="/meta/copyright_mahjong.php">Mahjong Graphics</a></li>
 <li><a href="/meta/copyright_largegfx.php">NPC Graphics</a></li>
 <li><a href="/meta/copyright_code.php">Code Libraries</a></li>
</ul>
<ul class="tabbed">
 <li><a href="copyright_smallgfx.php">Protected Graphics</a></li>
 <li class="activetab"><a href="/meta/copyright_smallgfx_pd.php">Public Domain Graphics</a></li>
</ul>
     <p>The graphics listed here are in the public domain, either found elsewhere and reused here, or released here by their artists.  If you believe any of the information here to be inaccurate, please <a href="/contactme.php">let me know</a>.</p>
     <p>Sort by: <a href="/meta/copyright_smallgfx_pd.php">Unsorted</a> | <a href="/meta/copyright_smallgfx_pd.php?order=names asc">Author(s)</a> | <a href="/meta/copyright_smallgfx_pd.php?order=title asc">Title</a></p>
     <?= $pages ?>
     <table>
<?php
$bgcolor = begin_row_class();

foreach($notices as $notice)
{
?>
      <tr class="<?= $bgcolor ?>">
       <td valign="top" align="center">
        <?= (strlen($notice['graphic']) > 0) ? '<img src="/' . $notice['graphic'] . '" />' : '' ?><br /><i class="dim">#<?= $notice['idnum'] ?></i>
       </td>
       <td valign="top">
<?php
  if($notice['rights'] == 'reserved')
  {
?>
        <p><i><?= $notice["title"] ?></i> &copy; <?= $notice['year'] ?> <?= str_replace(',', ', ', $notice["names"]) ?><br /><span class="size8">all rights reserved; used here with permission from the artist</span></p>
        <?= (strlen($notice["text"]) > 0) ? '<p><i>' . $notice['text'] . '</i></p>' : '' ?>
<?php
  }
  else if($notice['rights'] == 'pd_released')
  {
?>
        <p><i><?= $notice["title"] ?></i> by <?= str_replace(',', ', ', $notice["names"]) ?><br /><span class="size8">released into the public domain by the artist; no rights reserved</span></p>
        <?= (strlen($notice["text"]) > 0) ? '<p><i>' . $notice['text'] . '</i></p>' : '' ?>
<?php
  }
  else if($notice['rights'] == 'pd_found')
  {
    if($notice['source'] != '')
      $source = ':<br />' . $notice['source'];
    else
      $source = '';
?>
        <p><i><?= $notice["title"] ?></i><?= strlen($notice['names']) > 0 ? ' by ' . str_replace(',', ', ', $notice["names"]) : '' ?><br /><span class="size8">found in the public domain<?= $source ?></span></p>
        <?= (strlen($notice["text"]) > 0) ? '<p><i>' . $notice['text'] . '</i></p>' : '' ?>
<?php
  }
  else if($notice['rights'] == 'unlimited')
  {
?>
        <p><i><?= $notice["title"] ?></i> &copy; <?= $notice['year'] ?> <?= str_replace(',', ', ', $notice["names"]) ?><br /><span class="size8"><a href="/meta/copyright_contracts.php?item=<?= $notice['idnum'] ?>">most rights reserved</a> (click for details)</span></p>
        <?= (strlen($notice["text"]) > 0) ? '<p><i>' . $notice['text'] . '</i></p>' : '' ?>
<?php
  }
?>
       </td>
      </tr>
<?php
   $bgcolor = alt_row_class($bgcolor);
}
?>
     </table>
     <?= $pages ?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
