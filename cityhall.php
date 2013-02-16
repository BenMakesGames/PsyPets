<?php
$wiki = 'City_Hall';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/questlib.php';
require_once 'commons/threadfunc.php';
require_once 'commons/utility.php';

define('MAGPIE_OUTPUT_ENCODING', 'UTF-8');

require_once 'commons/magpierss/rss_fetch.inc';

require_once 'commons/leonids.php';

$city_hall_tutorial_quest = get_quest_value($user['idnum'], 'tutorial: city hall');
if($city_hall_tutorial_quest === false)
  $no_tip = true;

if(substr($user['graphic'], 0, 5) == 'weth/')
{
  $questvalues = get_quest_values_byuserid($user['idnum']);
  
  if(!array_key_exists('WeTH talk', $questvalues))
  {
    header('Location: ./wethtalk.php');
    exit();
  }
}

$badges = get_badges_byuserid($user['idnum']);

$year = (365 * 24 * 60 * 60);
$age = ($now - $user['signupdate']) / $year;

if($badges['sixmonthaccount'] == 'no' && $age >= .5)
{
  $badge_text = '<p>Six months sure fly by, don\'t they?</p><p><i>(You won the Novice Badge!)</i></p>';
  set_badge($user['idnum'], 'sixmonthaccount');
}
else if($badges['oneyearaccount'] == 'no' && $age >= 1)
{
  $badge_text = '<p>Happy one-year anniversary, ' . $user['display'] . '!</p><p><i>(You won the Junior Badge!)</i></p>';
  set_badge($user['idnum'], 'oneyearaccount');
}
else if($badges['twoyearaccount'] == 'no' && $age >= 2)
{
  $badge_text = '<p>Thanks for your dedication and hard work these last two years!</p><p><i>(You won the Two-Year Badge!)</i></p>';
  set_badge($user['idnum'], 'twoyearaccount');
}
else if($badges['threeyearaccount'] == 'no' && $age >= 3)
{
  $badge_text = '<p>It\'s been a fun three years, ' . $user['display'] . '!</p><p><i>(You won the Veteran Badge!)</i></p>';
  set_badge($user['idnum'], 'threeyearaccount');
}
else if($badges['fouryearaccount'] == 'no' && $age >= 4)
{
  $badge_text = '<p>It\'s hard to believe you\'ve been with us for four full years!  Thanks for everything!</p><p><i>(You won the Senior Badge!)</i></p>';
  set_badge($user['idnum'], 'fouryearaccount');
}
else if($badges['fiveyearaccount'] == 'no' && $age >= 5)
{
  $badge_text = '<p>You\'ve been with us for five years!  It\'s been great, and we look forward to working with you for as long as you\'ll stay!</p><p><i>(You won the Expert Badge!)</i></p>';
  set_badge($user['idnum'], 'fiveyearaccount');
}

// mark the last time they read the message board
$user['newcityhallpost'] = 'no';
$command = 'UPDATE monster_users SET newcityhallpost=\'no\' WHERE user=' . quote_smart($user['user']) . ' LIMIT 1';
$database->FetchNone($command, 'cityhall.php');

$allowed = array('broadcast' => true, 'comic' => true, 'ramble' => true, 'event' => true, 'routine' => true, 'important' => true, 'severe' => true);

if($_POST['action'] == 'filter')
{
  $filterurl = '';
  foreach($allowed as $filter=>$value)
  {
    if($_POST[$filter] == 'yes' || $_POST[$filter] == 'on')
    {
      if(strlen($filterurl) == 0)
        $filterurl .= "filter=$filter";
      else
        $filterurl .= ",$filter";
    }
    else
      $allowed[$filter] = false;
  }

  $filterurl .= '&';
}
else if(strlen($_GET['filter']) > 0)
{
  $view = take_apart(',', $_GET['filter']);

  $filterurl = '';
  foreach($allowed as $filter=>$value)
  {
    if(in_array($filter, $view))
    {
      if(strlen($filterurl) == 0)
        $filterurl .= "filter=$filter";
      else
        $filterurl .= ",$filter";
    }
    else
      $allowed[$filter] = false;
  }
  
  $filterurl .= '&';
}

$allow_me = array();

foreach($allowed as $filter=>$value)
{
  if($value)
    $allow_me[] = $filter;
}

$announcement_count = $database->FetchSingle('
	SELECT COUNT(*) AS c
	FROM psypets_news
	WHERE category ' . $database->In($allow_me) . '
');

$announcements_count = $announcement_count['c'];

$num_announcements = 10;
$num_pages = floor($announcements_count / $num_announcements) + 1;

$start_page = (int)$_GET['page'];

if($start_page < 1 || $start_page > $num_pages)
  $start_page = 1;

$rss = @fetch_rss('http://' . $SETTINGS['wiki_domain'] . '/index.php?title=Special:Recentchanges&feed=rss');

$command = 'SELECT graphic,display FROM monster_users WHERE lastactivity>0 AND is_npc=\'no\' ORDER BY idnum DESC LIMIT 10';
$newmembers = $database->FetchMultiple($command, 'fetching newest members');

$quest_totem = get_quest_value($user['idnum'], 'totem quest');

if($quest_totem === false)
{
  require_once 'commons/totemlib.php';

  $mytotem = get_totem_byuserid($user['idnum']);
  $totems = take_apart(',', $mytotem['totem']);
  $height = count($totems);
  
  if($height >= 15)
  {
    add_quest_value($user['idnum'], 'totem quest', 1);
    psymail_user($user['user'], 'mmansur', 'Do you have a moment?', 'I found this strange Silly Totem the other day, and I was hoping you could take a look at it.  It has some markings on the side, and... well, I don\'t want to jump to any conclusions.  Do you think you could take a look at it and let me know what you think?<br /><br />Thanks!', 1);
    add_inventory($user['user'], 'p:0', 'Silly Totem with Markings', 'Matalie Mansur sent this item to you.', 'storage/incoming');
    flag_new_incoming_items($user['user']);
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; City Hall</title>
<?php include 'commons/head.php'; ?>
  <link href="rss_news.xml" rel="alternate" type="application/rss+xml" title="<?= $SETTINGS['site_name'] ?> Latest News" />
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<?php
if($city_hall_tutorial_quest === false)
{
  include 'commons/tutorial/cityhall.php';
  add_quest_value($user['idnum'], 'tutorial: city hall', 1);
}
?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : '') ?>
     <h4>City Hall &gt; Bulletin Board <a href="/rss_news.xml"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/famfamfam/feed.png" width="16" height="16" alt="RSS Feed" class="inlineimage" /></a></h4>
     <ul class="tabbed">
      <li class="activetab"><a href="/cityhall.php">Bulletin Board</a></li>
      <li><a href="/help/">Help Desk</a></li>
      <li><a href="/cityhall_106.php">Room 106</a></li>
<?php
if($quest_totem['value'] >= 4)
  echo '<li><a href="cityhall_210.php">Room 210</a></li>';
?>
     </ul>
<?php
if($error_message)
  echo "     <p class=\"failure\">$error_message</p>\n";

if($success_message)
  echo "     <p class=\"success\">$success_message</p>\n";

$page_list = paginate($num_pages, $start_page, "cityhall.php?" . $filterurl . "page=%s");

if(strlen($badge_text) > 0)
{
  echo '<br />';
  echo '<img src="/gfx/npcs/receptionist.png" align="right" width="350" height="275" alt="(Claire the City Hall receptionist)" />';
  include 'commons/dialog_open.php';
  echo $badge_text;
  include 'commons/dialog_close.php';
  echo '<ul><li><a href="/cityhall.php">View city hall message board</a></li></ul>';
}
else
{
  if($user['admin']['mailpsypets'] == "yes")
    echo '<ul><li><a href="/writenewspost.php">Post new City Hall message</a></li></ul>';
?>
     <table>
     <tr><td valign="top">
<?php
  require_once 'commons/polllib.php';

  $current_poll = get_global('currentpoll');

  $poll = get_poll_byid($current_poll);
?>
<div style="border: 1px solid #888; background-color: #f8f8f8; padding: 0.25em 0.5em; margin-bottom: 1em;"><table border="0" cellspacing="0" cellpadding="4" style="margin: 0; padding: 0;">
 <tr>
  <td colspan="7" style="border-top: 1px solid #ccc;">
   <b>Current Poll:</b> <a href="pollstandalone.php"><?= $poll['title'] ?></a>
  </td>
 </tr>
 </tr>
  <td colspan="7" style="border-top: 1px solid #ccc;">
   <b><?= $SETTINGS['author_resident_name'] ?>'s Current Project:</b> <?php echo include 'immediategoal.php'; ?>
  </td>
 </tr>
</table>
</div>
     <form action="/cityhall.php" method="post">
     <input type="hidden" name="action" value="filter" />
     <ul class="filter">
      <li style="color:#399; font-weight: bold;"><input type="checkbox" name="broadcast" id="broadcast"<?= $allowed['broadcast'] ? " checked" : "" ?> /> <label for="broadcast">Broadcasting</label></li>
      <li style="color:#6c6; font-weight: bold;"><input type="checkbox" name="comic" id="comic"<?= $allowed['comic'] ? " checked" : "" ?> /> <label for="comic">Comics</label></li>
      <li style="color:#639; font-weight: bold;"><input type="checkbox" name="ramble" id="ramble"<?= $allowed['ramble'] ? " checked" : "" ?> /> <label for="ramble">Ramblings</label></li>
      <li style="color:#369; font-weight: bold;"><input type="checkbox" name="routine" id="routine"<?= $allowed['routine'] ? " checked" : "" ?> /> <label for="routine">Routine</label></li>
      <li style="color:#963; font-weight: bold;"><input type="checkbox" name="important" id="important"<?= $allowed['important'] ? " checked" : "" ?> /> <label for="important">Important</label></li>
      <li style="color:#933; font-weight: bold;"><input type="checkbox" name="severe" id="severe"<?= $allowed['severe'] ? " checked" : "" ?> /> <label for="severe">Urgent!</label></li>
      <li><input type="submit" value="Filter" /></li>
     </ul>
     </form>
     <?= $page_list ?>
<?php
$announcements = $database->FetchMultiple('
	SELECT *
	FROM psypets_news
	WHERE `category` ' . $database->In($allow_me) . '
	ORDER BY idnum DESC
	' . $database->Page($start_page, $num_announcements) . '
');

foreach($announcements as $general_post)
{
  $thread = get_thread_byid($general_post['threadid']);

  $author = get_user_byid($general_post['author'], 'display,graphic');
  $category = $general_post['category'];
?>
   <div class="cityhallpost category_<?= $category ?>">
    <h5><?= format_text($general_post['subject']) ?></h5>
    <img src="gfx/avatars/<?= $author['graphic'] ?>" alt="" width="48" height="48" align="right" />
		<div><?= format_text($general_post['message']) ?></div>
    <div class="signature">
     posted by <?= resident_link($author['display']) ?> on <?= local_time($general_post['date'], $user['timezone'], $user['daylightsavings']) ?>
<?php
  if($thread !== false)
  {
    $updatedby = get_user_byid($thread['updateby'], 'display');
    echo '<br />&rarr; <a href="/viewthread.php?threadid=' . $thread['idnum'] . '">Talk about it in the forums (' . $thread['replies'] . ' comment' . ($thread['replies'] != 1 ? 's' : '') . ')</a>' .
         ($thread['replies'] > 0 ? (', ' . ($thread['replies'] > 1 ? 'the last ' : '') . 'by <a href="/residentprofile.php?resident=' . link_safe($updatedby['display']) . '">' . $updatedby['display'] . '</a>') : '');
  }
?>
    </div>
   </div>
<?php
}
?>
     <?= $page_list ?>
     </td><td valign="top" style="padding-left: 1em;">
<h5><nobr>New Arrivals</nobr></h5>
<table>
<?php
foreach($newmembers as $member)
{
?>
<tr>
<td><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/avatars/<?= $member['graphic'] ?>" width="48" height="48" alt="" /></td>
<td><a href="/residentprofile.php?resident=<?= link_safe($member['display']) ?>"><?= $member['display'] ?></a></td>
</tr>
<?php
}
?>
</table>
<h5><nobr>Recent PsyHelp Updates</nobr></h5>
<?php
if(count($rss->items) == 0)
  echo '<nobr>Feed not available at this time. Sorry:|</nobr>';
else
{
?>
<ul class="plainlist">
<?php
  foreach ($rss->items as $item)
  {
    $title = $item['title'];
    $special_page = (substr($title, 0, 8) == 'Special:');
    $user_page = (substr($title, 0, 5) == 'User:' || substr($title, 0, 10) == 'User talk:');
    $group_page = (substr($title, 0, 6) == 'Group:' || substr($title, 0, 11) == 'Group talk:');
    if(!$special_page && !$user_page && !$group_page && $titles[$title] !== true)
    {
      $titles[$title] = true;
      if(html_strlen($title) > 34)
        $title = html_substr($title, 0, 32) . ' ...';
  		$href = $item['link'];
  		echo '  <li><a href="' . $href . '"><nobr>' . $title . '</nobr></a></li>' ."\n";
    }
  }
?>
</ul>
<?php
}

require_once 'commons/changeloglib.php';

$changelog = get_latest_changelog();
?>
<h5>Changelog (<a href="/changelog.php">view all</a>)</h5>
<ul class="plainlist">
<?php
foreach($changelog as $entry)
{
  echo '<li><nobr>';
  if(date('d', $entry['timestamp']) == $now_day)
    echo 'Today';
  else
    echo date('M j', $entry['timestamp']);

  $title = $entry['summary'];

  if(html_strlen($title) > 28)
    $title = html_substr($title, 0, 26) . ' ...';

  echo ': ' . $title . '</nobr></li>';
}
?>
</ul>
     </td></tr>
     </table>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
