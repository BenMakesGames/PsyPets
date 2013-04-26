<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/userlib.php';
require_once 'commons/questlib.php';
require_once 'commons/threadfunc.php';

if($admin['manageaccounts'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$year = date('Y');

$command = 'SELECT a.userid,a.value,b.user,b.display FROM psypets_questvalues AS a LEFT JOIN monster_users AS b ON a.userid=b.idnum WHERE name=\'stpat bank ' . $year . '\' ORDER BY value DESC LIMIT 25';
$top_25_bank = $database->FetchMultiple($command, 'top 25 bank donators');

$command = 'SELECT a.userid,a.value,b.user,b.display FROM psypets_questvalues AS a LEFT JOIN monster_users AS b ON a.userid=b.idnum WHERE name=\'stpat totem ' . $year . '\' ORDER BY value DESC LIMIT 25';
$top_25_totem = $database->FetchMultiple($command, 'top 25 totem pole garden donators');

$command = 'SELECT SUM(value) AS t FROM psypets_questvalues WHERE name=\'stpat bank ' . $year . '\'';
$bank_data = $database->FetchSingle($command, 'bank total');

$command = 'SELECT SUM(value) AS t FROM psypets_questvalues WHERE name=\'stpat totem ' . $year . '\'';
$totem_data = $database->FetchSingle($command, 'totem total');

if($bank_data['t'] == $totem_data['t'])
  $bank_data['t']++;

$message = '';

if($bank_data['t'] > $totem_data['t'])
{
  $poster = 'lpawlak';

  $message .=
		'<p>Hohohohoho!  Looks like I won!  Good thing, too: my bathroom, especially, is a complete wreck.</p>' .
    '<p>The top 25 donators should stop by The Bank to pick up their rewards, regardless of who they donated to - I agreed, out of compassion, to handle the distribution of the rewards.</p>' .
    '<p>The top 10 will receive an additional prize, as well!</p>' .
    '<h3>St. Patrick\'s Day ' . $year . ' Competition</h3>' .
    '<ul>' .
			'<li>Lakisha Pawlak received a total of ' . $bank_data['t'] . ' items!</li>' .
			'<li>Matalie Mansur received a total of ' . $totem_data['t'] . ' items!</li>' .
    '</ul>'
  ;
}
else
{
  $poster = 'mmansur';

  $message .=
    '<p>Phew!  I won!  And I couldn\'t have done it without you guys!  Thanks!</p>' .
    '<p>The top 25 donators should stop by The Bank to pick up their rewards, regardless of who they donated to - Lakisha agreed to handle the distribution of the rewards, since my Totem Pole Garden is too far away for some people.</p>' .
    '<p>The top 10 will receive an additional prize, as well!</p>' .
    '<p>Thanks again, everyone!</p>' .
    '<h3>St. Patrick\'s Day ' . $year . ' Competition</h3>' .
    '<ul>' .
      '<li>Matalie Mansur received a total of ' . $totem_data['t'] . ' items!</li>' .
      '<li>Lakisha Pawlak received a total of ' . $bank_data['t'] . ' items!</li>' .
    '</ul>'
  ;
}

$message .= '<h4>Top 25 Bank Donators</h4><ol>';

foreach($top_25_bank as $i=>$donator)
{
  // the top 25 get a reward; the top 10 get two!
  if($_GET['action'] == 'post')
    add_quest_value($donator['userid'], 'stpat bank ' . $year . ' reward', ($i < 10 ? 2 : 1));

  $message .= '<li>' . $donator['value'] . ' items from ' . resident_link($donator['display']) . '</li>';
}

$message .= '</ol><h4>Top 25 Totem Pole Garden Donators</h4><ol>';

foreach($top_25_totem as $i=>$donator)
{
  // the top 25 get a reward; the top 10 get two!
  if($_GET['action'] == 'post')
    add_quest_value($donator['userid'], 'stpat totem ' . $year . ' reward', ($i < 10 ? 2 : 1));

  $message .= '<li>' . $donator['value'] . ' items from ' . resident_link($donator['display']) . '</li>';
}

$message .= '</ol>';

if($_GET['action'] == 'post')
{
  $npc_account = get_user_byuser($poster, 'idnum');

  $subject = 'St. Patrick\'s Day ' . $year . ' Competition';
  $total_title = 'event: ' . $subject;

  $post_id = news_post($npc_account['idnum'], 'event', $subject, $message);

  $command = 'SELECT * FROM monster_plaza WHERE title=\'City Hall News\' LIMIT 1';
  $news_plaza = $database->FetchSingle($command, 'writenewspost.php');

  $command = 'INSERT INTO `monster_threads` ' .
             '(`plaza`, `title`, `creationdate`, `updatedate`, `createdby`, `updateby`) VALUES ' .
             '(' . $news_plaza['idnum'] . ', ' . quote_smart($total_title) . ", '" . $now . "', '" . $now . "', '" . $npc_account['idnum'] . "', '" . $npc_account['idnum'] . "')";
  $database->FetchNone($command, 'newthread.php');

  $thread_id = $database->InsertID();

  $command = 'UPDATE monster_plaza ' .
             'SET replies=replies+1, `updatedate`=' . $now . ' ' .
             'WHERE idnum=' . $news_plaza['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'newthread.php');

  create_post($thread_id, 'says', $total_title, '', $message, $npc_account['idnum']);

  $command = 'UPDATE psypets_news SET threadid=' . $thread_id . ' WHERE idnum=' . $post_id . ' LIMIT 1';
  $database->FetchNone($command, 'updating thread');

  $command = 'UPDATE monster_users SET newcityhallpost=\'yes\'';
  $database->FetchNone($command, 'alerting residents of post');

  header('Location: /cityhall.php');
  exit();
}
else
{
  echo $message;

  echo '<ul><li><a href="?action=post">Post to City Hall</a></li></ul>';
  echo '<p>Everything is taken care of when you post to City Hall; the top 25 and 10 will have quest values set, which are used at bank.php</p>';
}
