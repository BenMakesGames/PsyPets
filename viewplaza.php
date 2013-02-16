<?php
/*
header('Location: ./plazaupdate.php');
exit();
*/
$require_petload = 'no';
$child_safe = false;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/threadfunc.php';
require_once 'commons/userlib.php';
/*
if($user['idnum'] != 1)
{
  header('Location: ./plazaupdate.php');
  exit();
}
*/
if($_GET['selfsearch'] == 1)
  $filter = 'AND createdby=' . $user['idnum'] . ' ';
else
  $filter = '';

// ---

$plazaid = (int)$_GET['plaza'];

$command = "SELECT * FROM monster_plaza WHERE idnum=$plazaid LIMIT 1";
$plazainfo = $database->FetchSingle($command, 'viewplaza.php?idnum=' . $plazaid);

if($plazainfo === false)
{
  header('Location: /plaza.php');
  exit();
}

if(substr($plazainfo['title'], 0, 1) == '#')
{
  header('Location: /plaza.php');
  exit();
}

// figure out where to start from

$data = $database->FetchSingle('
	SELECT COUNT(idnum) AS qty
	FROM monster_threads
	WHERE plaza=' . $database->Quote($_GET['plaza']) . ' ' . $filter
);

$thread_count = $data['qty'];

$num_threads = 20;
$num_pages = floor($thread_count / $num_threads) + 1;

if(!is_numeric($_GET['page']))
  $start_page = 1;
else if($_GET['page'] > $num_pages)
  $start_page = 1;
else
  $start_page = (int)$_GET["page"];

$watched_thread = $database->FetchMultipleBy('
	SELECT * FROM monster_watching
	WHERE user=' . $database->Quote($user['user']) . '
', 'threadid');

$search_time = microtime(true);

$plaza_threads = $database->FetchMultiple('
	SELECT *
	FROM monster_threads
	WHERE
		plaza=' . $plazaid . '
		' . $filter . '
	ORDER BY sticky DESC, updatedate DESC
	' . $database->Page($start_page, $num_threads) . '
');

$search_time = microtime(true) - $search_time;

$footer_note = '<br />Took ' . round($search_time, 4) . 's fetching the plaza threads.';

// see if we have a watched_thread entry for this plaza
if(count($watched_thread[-$plazainfo['idnum']]) > 0)
{
	//  if so, update it!
  $database->FetchNone('
		UPDATE monster_watching
    SET lastread=' . $now . '
    WHERE threadid=' . (-$plazaid) . '
    AND user=' . $database->Quote($user['user']) . '
		LIMIT 1
	');
}
else
{
	// if not, add one
	$database->FetchNone('
		INSERT INTO monster_watching
    (`user`, `threadid`, `lastread`)
    VALUES
    (
			' . $database->Quote($user['user']) . ',
			' . (-(int)$plazainfo['idnum']) . ',
			' . (int)$now . '
		)
	');
}

$move_requests = array();

$mods = explode(',', $plazainfo['admins']);
 
if(in_array($user['idnum'], $mods))
{
  $command = 'SELECT * FROM monster_watchermove WHERE destination=' . $plazainfo['idnum'] . ' ORDER BY timestamp DESC';
  $move_requests = $database->FetchMultiple($command, 'viewplaza.php?idnum=' . $plazaid);

  $command = "SELECT * FROM monster_reports WHERE plazaid=$plazaid ORDER BY reports DESC";
  $reports = $database->FetchMultiple($command, 'viewplaza.php?idnum=' . $plazaid);
}

if($plazainfo['groupid'] > 0)
{
  include 'commons/grouplib.php';

  $group = get_group_byid($plazainfo['groupid']);
  $ranks = get_group_ranks($groupid);
  $members = explode(',', $group['members']);

  $a_member = is_a_member($group, $user['idnum']);
  $rankid = get_member_rank($group, $user['idnum']);
}

$can_make_thread = ($plazainfo['locked'] == 'no' && ($plazainfo['groupid'] == 0 || $a_member) && $plazainfo['newthreadlock'] == 'no');

include 'commons/html.php';
?>
 <head>
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $plazainfo['groupid'] == 0 ? 'Plaza Forums &gt; ' . $plazainfo["title"] : $plazainfo['title'] . ' &gt; Forum' ?></title>
  <script type="text/javascript">
   function togglestickies(save_cookie)
   {
     var e = document.getElementById('stickies');
     var l = document.getElementById('showhidestickies');

     if(e.style.display == 'none')
     {
       e.style.display = '';
       l.innerHTML = '&#9650; &#9650; &#9650;';
     }
     else
     {
       e.style.display = 'none';
       l.innerHTML = '&#9660; &#9660; &#9660;';
     }

     if(save_cookie)
       document.cookie = 'forum_<?= $plazainfo['idnum'] ?>' + '=' + e.style.display;
   }
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="plaza.php">Plaza Forums</a> &gt; <?= $plazainfo['title'] ?></h4>
<?php
if($plazainfo['groupid'] > 0)
{
  require_once 'commons/grouplib.php';

  $groupid = $plazainfo['groupid'];

  $group = get_group_byid($groupid);

  $organizer = get_user_byid($group['leaderid'], 'idnum');

  $activetab = 'forum';
  include 'commons/grouptabs.php';
}

if(in_array($user['idnum'], $mods))
{
?>
     <p><i>You are a watcher for this plaza.</i></p>
     <ul>
      <li><a href="/watchrecount.php?plazaid=<?= $plazainfo['idnum'] ?>">Recount plaza posts</a></li>
<?php
  if($plazainfo['locked'] == 'yes')
  {
    if($admin['manageplaza'] == 'yes')
      echo "<li>This plaza is locked; <a href=\"lockplaza.php?plaza=" . $plazainfo["idnum"] . "\">unlock this plaza</a></li>";
    else
      echo '<li>This plaza is locked</li>';
  }
  else if($admin['manageplaza'] == 'yes')
    echo '<li><a href="lockplaza.php?plaza=' . $plazainfo['idnum'] . '">Lock this plaza</a></li>';

  echo '</ul>';

  if(count($move_requests) > 0)
  {
?>
     <h5>Move Requests</h5>
     <table>
      <tr class="titlerow">
       <th>Time</th>
       <th>Requester</th>
       <th>Thread</th>
       <th>Action</th>
      </tr>
<?php
    foreach($move_requests as $request)
    {
      $threadinfo = get_thread_byidnum($request['threadid']);
      $requester = get_user_byid($request['watcher'], 'display');
?>
      <tr>
       <td><?= local_time($request['timestamp'], $user['timezone'], $user['daylightsavings']) ?></td>
       <td><a href="/residentprofile.php?resident=<?= link_safe($requester['display']) ?>"><?= $requester['display'] ?></a></td>
       <td><a href="/viewthread.php?threadid=<?= $threadinfo["idnum"] ?>"><?= $threadinfo['title'] ?></a></td>
       <td>[ <a href="/watchacceptmove.php?requestid=<?= $request["idnum"] ?>">accept</a> | <a href="/watchdeclinemove.php?requestid=<?= $request["idnum"] ?>">decline</a> ]</td>
      </tr>
<?php
    }
?>
     </table>
     <h5><?= $plazainfo['title'] ?></h5>
<?php
  }

  if(count($reports) > 0)
  {
?>
     <form action="/admin/reports.php?plazaid=<?= $plazaid ?>" method="post">
     <table>
      <tr class="titlerow">
       <th></th>
       <th>Reports</th>
       <th>Plaza</th>
       <th>Title</th>
      </tr>
<?php
    $bgcolor = begin_row_class();

    foreach($reports as $report)
    {
      $command = 'SELECT title,plaza FROM monster_threads WHERE idnum=' . quote_smart($report['threadid']) . ' LIMIT 1';
      $this_thread = $database->FetchSingle($command, 'fetching reported thread');

      $command = 'SELECT title FROM monster_plaza WHERE idnum=' . $this_thread['plaza'] . ' LIMIT 1';
      $this_plaza = $database->FetchSingle($command, 'fetching plaza info');
?>
      <tr class="<?= $bgcolor ?>">
       <td><input type="checkbox" name="<?= $report['threadid'] ?>" /></td>
       <td class="righted"><?= $report['reports'] ?></td>
       <td><?= $this_plaza['title'] ?></td>
       <td><a href="/viewthread.php?threadid=<?= $report['threadid'] ?>"><?= $this_thread['title'] ?></a></td>
      </tr>
<?php

      $bgcolor = alt_row_class($bgcolor);
    }
?>
     </table>
     <p><input type="hidden" name="action" value="clear" /><input type="submit" value="Clear Reports" class="bigbutton" /></p>
     </form>
<?php
  }

  echo '<hr />';
}

//include 'commons/bcmessage.php';

echo '<p>' . format_text($plazainfo['guidelines']) . '</p>';

if($plazainfo['locked'] == 'yes')
  echo '<p><img src="/gfx/lock.gif" width="16" height="16" /> This plaza is locked!</p>';

// options!
echo '<ul>';

if($plazainfo['groupid'] == 0)
  echo '<li><a href="/plaza/search.php?plaza=' . $_GET['plaza'] . '">Search ' . $plazainfo['title'] . '</a></li>';

if($can_make_thread)
  echo '<li><a href="/newthread.php?plaza=' . $_GET['plaza'] . '">New topic</a></li>';

if($_GET['selfsearch'] == 1)
  echo '<li><a href="/viewplaza.php?plaza=' . $plazainfo['idnum'] . '">Show all threads</a></li>';
else
  echo '<li><a href="/viewplaza.php?plaza=' . $plazainfo['idnum'] . '&selfsearch=1">Show only threads I started</a></p>';

echo '</ul>';

if($_GET['selfsearch'] == 1)
  $page_list = paginate($num_pages, $start_page, '/viewplaza.php?plaza=' . $_GET['plaza'] . '&page=%s&selfsearch=1');
else
  $page_list = paginate($num_pages, $start_page, '/viewplaza.php?plaza=' . $_GET['plaza'] . '&page=%s');

echo $page_list;
?>
     <table>
     <thead>
      <tr class="titlerow">
       <th></th>
       <th width="100%">Title</th>
<?php
if($plazainfo['show_thumbs'] == 'yes') echo '<th></th>';
?>
       <th>Author</th>
       <th class="centered">Replies</th>
       <th class="centered">Views</th>
       <th class="centered">Last&nbsp;Post</th>
      </tr>
     </thead>
<?php
$bgcolor = begin_row_class();
$last_thread_sticky = false;

$list = 0;

foreach($plaza_threads as $thread)
{
  $list++;

  if($thread['sticky'] == 'no')
  {
    if($list == 1)
      echo '<tbody>';

    if($last_thread_sticky)
    {
?>
    </tbody>
    <tbody>
     <tr style="border-top: 1px solid #888; border-bottom: 1px solid #888;">
      <th colspan="6" class="centered"><a href="#" onclick="togglestickies(true); return false;"><span id="showhidestickies" class="size7">&#9650; &#9650; &#9650;</span></a></th>
     </tr>
<?php
      $last_thread_sticky = false;
    }
  }
  else
  {
    if($list == 1)
      echo '<tbody id="stickies">';

    $last_thread_sticky = true;
  }
?>
<?php
  $flags = '';

  if($thread['highlight'] > 0)
    $flags .= '<img src="//' . $SETTINGS['static_domain'] . '/gfx/forum/' . $THREAD_HIGHLIGHTS[$thread['highlight']] . '" width="16" height="16" alt="" />';

  if($thread['locked'] == 'yes')
    $flags .= '<img src="/gfx/lock.gif" width="16" height="16" alt="Locked" />';
  else if($thread['updatedate'] < ($now - 6 * 30 * 24 * 60 * 60) && $thread['sticky'] == 'no')
    $flags .= '<img src="/gfx/lock_soft.png" width="16" height="16" alt="Locked (old)" />';

  if(strlen($flags) == 0)
    $flags .= '<img src="/gfx/shim.gif" width="16" height="16" alt="" />';

  // read the users related to this post
  $creator = get_user_byid($thread['createdby'], 'display');
  $updator = get_user_byid($thread['updateby'], 'display');

  if(strlen($creator['display']) > 0)
    $creatorlink = '<a href="/residentprofile.php?resident=' . $creator['display'] . '">' . $creator['display'] . '</a>';
  else
    $creatorlink = '<i style="color:#888;">[departed #' . $thread['createdby'] . ']</i>';

  if(strlen($updator["display"]) > 0)
    $updatorlink = '<a href="userprofile.php?user=' . $updator['display'] . '">' . $updator['display'] . '</a>';
  else
    $updatorlink = '<i style="color:#888888;">[departed #' . $thread['updateby'] . ']</i>';

  if($thread['updatedate'] > $watched_thread[$thread['idnum']]['lastread'])
    $extra_class = ' unread';
  else
    $extra_class = '';
?>
      <tr class="<?= $bgcolor . $extra_class ?>">
       <td><nobr><?= $flags ?></nobr></td>
       <td>
        <a href="/viewthread.php?threadid=<?= $thread['idnum'] ?>"><?= $sticky . format_text($thread['title']) ?></a><br />
<?php
  $options = array();

  if($watched_thread[$thread['idnum']]['lastread'] > 0 && $thread['updatedate'] > $watched_thread[$thread['idnum']]['lastread'])
    $options[] = '<a href="/jumptolatestpost.php?threadid=' . $thread['idnum'] . '">first unread post</a>';

  if($thread['replies'] > 19)
    $options[] = '<a href="/viewthread.php?threadid=' . $thread['idnum'] . '&page=' . floor($thread['replies'] / 20 + 1) . '">last page</a>';

  if(count($options) > 0)
    echo '<span class="size8">[ ' . implode(', ', $options) . ' ]</span>';

  echo '</td>';

  if($plazainfo['show_thumbs'] == 'yes')
  {
    if($thread['opening_post_id'] > 0)
    {
      $command = 'SELECT COUNT(voterid) AS c FROM psypets_post_thumbs WHERE postid=' . $thread['opening_post_id'] . ' AND vote>0';
      $thumbs = $database->FetchSingle($command, 'fetching votes for opening post');

      if($thumbs['c'] > 0)
        echo '<td><nobr>' . $thumbs['c'] . '<img src="//' . $SETTINGS['static_domain'] . '/gfx/forum/thumbup.png" alt="thumbs up" class="inlineimage" /></nobr></td>';
      else
        echo '<td></td>';
    }
    else
      echo '<td></td>';
  }
?>
       <td><nobr><?= $creatorlink ?></nobr></td>
       <td align="center"><?= $thread['replies'] ?></td>
       <td align="center"><?= $thread['views'] ?></td>
       <td align="center">
        <nobr>by <?= $updatorlink ?></nobr><br />
        <nobr><?= Duration($now - $thread['updatedate'], 2) ?> ago</nobr>
       </td>
      </tr>
<?php
  $bgcolor = alt_row_class($bgcolor);
}
?>
     </tbody>
     </table>
     <script type="text/javascript">
<?php
$value = $_COOKIE['forum_' . $plazainfo['idnum']];

if($value == 'none')
  echo 'togglestickies(false);' . "\n";
?>
     </script>
<?php
echo $page_list;
?>
     <ul>
<?php
if($can_make_thread)
  echo '      <li><a href="/newthread.php?plaza=' . $_GET['plaza'] . '">New Topic</a></li>';
?>
     </ul>
<?php
if($plazainfo['groupid'] == 0)
  echo '     <h5><a href="/plaza.php">Plaza Forums</a> &gt; ' . $plazainfo['title'] . '</h5>';
else
  echo '     <h5><a href="/grouppage.php?id=' . $plazainfo['groupid'] . '">' . $plazainfo['title'] . '</a> &gt; Forum</h5>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
