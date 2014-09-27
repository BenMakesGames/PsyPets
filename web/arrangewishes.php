<?php
require_once 'commons/init.php';

$wiki = 'To-do List';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/todolistlib.php';

$tag = trim($_GET['tag']);

if(strlen($tag) > 0)
{
	$data = $database->FetchSingle('
		SELECT COUNT(*) AS c
		FROM psypets_ideachart
		LEFT JOIN	psypets_ideachart_tags ON psypets_ideachart.idnum=psypets_ideachart_tags.ideaid
		WHERE psypets_ideachart_tags.tag=' . quote_smart($tag) . '
	');

	$num_items = $data['c'];
	$num_pages = ceil($num_items / 20);
	$page = (int)$_GET['page'];
	if($page < 1 || $page > $num_pages)
		$page = 1;

	$ideas = $database->FetchMultipleBy(
		'
			SELECT psypets_ideachart.*
			FROM psypets_ideachart
			LEFT JOIN	psypets_ideachart_tags ON psypets_ideachart.idnum=psypets_ideachart_tags.ideaid
			WHERE psypets_ideachart_tags.tag=' . quote_smart($tag) . '
			ORDER BY psypets_ideachart.idnum DESC
			LIMIT ' . (($page - 1) * 20) . ',20
		',
		'idnum'
	);

	$pages = paginate($num_pages, $page, '?tag=' . $tag . '&amp;page=%s');
}
else
{
	$data = $database->FetchSingle('
		SELECT COUNT(*) AS c
		FROM psypets_ideachart
	');

	$num_items = $data['c'];
	$num_pages = ceil($num_items / 20);
	$page = (int)$_GET['page'];
	if($page < 1 || $page > $num_pages)
		$page = 1;

	$ideas = $database->FetchMultipleBy(
		'
			SELECT *
			FROM psypets_ideachart
			ORDER BY idnum DESC
			LIMIT ' . (($page - 1) * 20) . ',20
		',
		'idnum'
	);

	$pages = paginate($num_pages, $page, '?page=%s');
}

$command = 'SELECT ideaid,votes FROM psypets_ideavotes WHERE residentid=' . $user['idnum'];
$my_votes = $database->FetchMultipleBy($command, 'ideaid', 'fetching my votes'); 

foreach($ideas as $id=>$wish)
{
  $votes[$id] = (int)$my_votes[$id]['votes'];

	$these_tags = $database->FetchMultiple(
		'SELECT * FROM psypets_ideachart_tags	WHERE ideaid=' . $id
	);
	
	foreach($these_tags as $this_tag)
		$tags[$id][] = '<a href="?tag=' . urlencode($this_tag['tag']) . '">' . $this_tag['tag'] . '</a>';
}

if($user['wishlistupdate'] == 'yes')
{
  $command = 'UPDATE monster_users SET wishlistupdate=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'removing wish list notification icon, because that is what we do here');

  $user['wishlistupdate'] = 'no';
}

$is_manager = ($user['admin']['managewishlist'] == 'yes');

$tag_cloud = $database->FetchMultiple('
	SELECT tag,COUNT(idnum) AS weight
	FROM psypets_ideachart_tags
	GROUP BY tag
	ORDER BY tag ASC
');

foreach($tag_cloud as $this_tag)
	$weights[] = $this_tag['weight'];

$tag_max = max($weights);
$tag_min = min($weights);
$tag_range = $tag_max - $tag_min + 1;

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; To-do List <?= (strlen($tag) > 0 ? '(' . $tag . ')' : '') ?> &gt; Your Vote</title>
<?php include "commons/head.php"; ?>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/todolist.js"></script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>To-do List <?= (strlen($tag) > 0 ? '(' . $tag . ')' : '') ?> &gt; Your Vote</h4>
		 <p>I (<?= User::Link($SETTINGS['author_resident_name']) ?>) use the To-do List to keep track of changes I want to make.  Feel free to leave you opinion on a change; I'll try to focus my efforts on changes which recieve a lot of positive review, and may think twice or ask your opinions about those which receive largely negative review.</p>
<?php
if($is_manager)
  echo '<ul><li><a href="/admin/todo_totals.php">Manage To-do List</a></li></ul>';
?>
     <ul class="tabbed">
      <li class="activetab"><a href="/arrangewishes.php">Your Vote</a></li>
      <li><a href="/todolist_completed.php">Completed Items</a></li>
     </ul>
<div style="margin-left:10px; width:200px; padding:5px; border-left: 1px dashed #666; float: right; text-align: center;">
<h5>Fluffy Tag Cloud</h5>
<?php
foreach($tag_cloud as $tag)
{
	$size = (int)(($tag['weight'] - $tag_min) * 20 / $tag_range) + 10;
	echo '<span style="font-size:' . $size . 'px; vertical-align:middle;"><a href="?tag=' . urlencode($tag['tag']) . '">' . $tag['tag'] . '</a></span> ';
}
?>
</div>
<?php
if(count($ideas) > 0)
{
?>
<p><i>(Newest items are displayed first.)</i></p>
<?= $pages ?>
<table>
 <tr class="titlerow">
  <th class="centered" style="width:100px;">Vote</th>
  <th>Item</th>
  <th></th>
 </tr>
<?php

  $rowclass = begin_row_class();

  foreach($votes as $id=>$vote)
  {
    if($ideas[$id]['postdate'] > $now - 3 * 24 * 60 * 60)
      $new = ' <i style="color:red;">new!</i>';
    else
      $new = '';

    $vote = array(
      -2 => ($vote == -2 ? '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/grr.png" alt="super bad!" class="inlineimage" />' : '<a href="#" onclick="todo_vote(' . $id . ', -2); return false;"><img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/grr.png" alt="super bad!" class="transparent_image inlineimage" /></a>'),
      -1 => ($vote == -1 ? '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/mmf.gif" alt="no, thank you." class="inlineimage" />' : '<a href="#" onclick="todo_vote(' . $id . ', -1); return false;"><img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/mmf.gif" alt="no, thank you." class="transparent_image inlineimage" /></a>'),
      0 => ($vote == 0 ? '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/neutral.png" alt="whatever." class="inlineimage" />' : '<a href="#" onclick="todo_vote(' . $id . ', 0); return false;"><img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/neutral.png" alt="whatever." class="transparent_image inlineimage" /></a>'),
      1 => ($vote == 1 ? '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/hee.gif" alt="yes, please." class="inlineimage" />' : '<a href="#" onclick="todo_vote(' . $id . ', 1); return false;"><img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/hee.gif" alt="yes, please." class="transparent_image inlineimage" /></a>'),
      2 => ($vote == 2 ? '<img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/yeah.gif" alt="want!" class="inlineimage" />' : '<a href="#" onclick="todo_vote(' . $id . ', 2); return false;"><img src="//' . $SETTINGS['static_domain'] . '/gfx/emote/yeah.gif" alt="want!" class="transparent_image inlineimage" /></a>'),
    );

    echo '
      <tr class="' . $rowclass . '">
       <td><div id="vote' . $id . '" class="centered">
        ' . $vote[2] . ' ' . $vote[1] . ' ' . $vote[0] . ' ' . $vote[-1] . ' ' . $vote[-2] . '
       </div></td>
       <td>
			  ' . $ideas[$id]['sdesc'] . $new . '<br />
			  <i class="size8">' . implode(', ', $tags[$id]) . '</i>
			 </td>
       <td><nobr>(<a href="/tododetails.php?id=' . $id . '">read more...</a>)</nobr></td>
      </tr>
    ';
    
    $rowclass = alt_row_class($rowclass);
  }
?>
</table>
<?= $pages ?>
<?php
}
else
	echo '<p>There are no To-do List entries.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
