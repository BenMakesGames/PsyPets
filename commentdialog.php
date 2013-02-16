<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/grammar.php';
require_once 'commons/utility.php';

$res1 = get_user_bydisplay($_GET['1'], 'idnum,display,graphic,is_a_whale');
$res2 = get_user_bydisplay($_GET['2'], 'idnum,display,graphic,is_a_whale');

if($res1 === false || $res2 === false)
{
  header('Location: ./directory.php');
  exit();
}

$count_time = microtime(true);

$command = 'SELECT COUNT(*) FROM psypets_profilecomments WHERE (userid=' . $res1['idnum'] . ' AND authorid=' . $res2['idnum'] . ') OR (userid=' . $res2['idnum'] . ' AND authorid=' . $res1['idnum'] . ')';
$data = $database->FetchSingle($command, 'commentdialog.php');

$count_time = microtime(true) - $count_time;

$num_comments = (int)$data['COUNT(*)'];

if($num_comments > 0)
{
  $page = (int)$_GET['page'];
  $num_pages = ceil($num_comments / 20);

  if($page < 1)
    $page = 1;

  $search_time = microtime(true);

  $command = '(SELECT idnum,userid,authorid,comment FROM psypets_profilecomments WHERE userid=' . $res1['idnum'] . ' AND authorid=' . $res2['idnum'] . ') ' .
             'UNION ' .
             '(SELECT idnum,userid,authorid,comment FROM psypets_profilecomments WHERE userid=' . $res2['idnum'] . ' AND authorid=' . $res1['idnum'] . ') ' .
             'ORDER BY idnum DESC LIMIT ' . (($page - 1) * 20) . ',20';

  $yay = $database->FetchMultiple($command, 'commentdialog.php');

  $search_time = microtime(true) - $search_time;
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $res1['display'] ?>'s Profile &gt; Comments With <?= $res2['display'] ?></title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="residentprofile.php?resident=<?= link_safe($res1['display']) ?>"><?= $res1['display'] ?>'s Profile</a> &gt; Comments With <?= $res2['display'] ?></h4>
     <ul><li><a href="residentprofile.php?resident=<?= link_safe($res2['display']) ?>">View <?= $res2['display'] ?>'s profile</a></li></ul>
<?php
if($num_comments > 0)
{
  $pages = paginate($page, $num_pages, 'commentdialog.php?1=' . link_safe($res1['display']) . '&2=' . link_safe($res2['display']) . '&page=%s');

  echo $pages;

  foreach($yay as $comment)
  {
    if($comment['authorid'] == $res1['idnum'])
      $author = &$res1;
    else
      $author = &$res2;
?>
<table border="0" cellspacing="0" cellpadding="4">
 <tr>
  <td valign="top" class="centered">
   <img src="<?= user_avatar($author) ?>" width="48" height="48" alt="" />
  </td>
  <td valign="top"><b><?= $author['display'] ?> says:</b> <?= format_text($comment['comment']) ?></td>
 </tr>
</table>
<?php
  }

  echo $pages;
}

$footer_note = '<br />Took ' . round($count_time, 4) . 's counting the comments, and ' . round($search_time, 4) . 's fetching them.';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
