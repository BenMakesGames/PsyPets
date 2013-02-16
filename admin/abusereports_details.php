<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/threadfunc.php';

if($admin['abusewatcher'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$threadid = (int)$_GET['threadid'];

$command = 'SELECT * FROM psypets_abusereports WHERE threadid=' . $threadid;
$reports = $database->FetchMultiple($command, 'fetching plaza abuse reports');

if(count($reports) == 0)
{
  header('Location: /admin/abusereports.php');
  exit();
}

if($_POST['submit'] == 'Clear Reports')
{
  $command = 'DELETE FROM psypets_abusereports WHERE threadid=' . $threadid . ' LIMIT ' . count($reports);
  $database->FetchNone($command, 'deleting reports');
  
  header('Location: /admin/abusereports.php');
  exit();
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Abuse Reports &gt; Post #<?= $threadid ?></title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; <a href="/admin/abusereports.php">Abuse Reports</a> &gt; Post #<?= $threadid ?></h4>
<ul>
 <li><a href="/jumptopost.php?postid=<?= $threadid ?>">View reported post</a></li>
</ul>
<table>
 <tr class="titlerow">
  <th>When</th>
  <th>Reporter</th>
  <th>Comment</th>
  <th>Original&nbsp;Post</th>
 </tr>
<?php
$rowclass = begin_row_class();

foreach($reports as $report)
{
  $resident = get_user_byid($report['reporter'], 'user,display');
  if($resident === false)
    $resident_display = '<i class="dim">[Departed #' . $report['reporter'] . ']</i>';
  else
    $resident_display = '<a href="residentprofile.php?resident=' . link_safe($resident['display']) . '">' . $resident['display'] . '</a>';
?>
<tr class="<?= $rowclass ?>">
 <td valign="top"><nobr><?= Duration($now - $report['timestamp'], 2) ?> ago</nobr></td>
 <td valign="top"><nobr><?= $resident_display ?></nobr></td>
 <td valign="top"><?= $report['comment'] ?></td>
 <td valign="top"><?= $report['original_text'] ?></td>
</tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
</table>
<form method="post"><p><input type="submit" name="submit" value="Clear Reports" class="bigbutton" /></p></form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
