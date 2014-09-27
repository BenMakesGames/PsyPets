<?php
$IGNORE_MAINTENANCE = true;

require_once 'commons/init.php';

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

$command = 'SELECT COUNT(idnum) AS count,threadid FROM psypets_abusereports WHERE type=\'post\' GROUP BY threadid';
$reported_posts = $database->FetchMultiple($command, 'fetching mail abuse reports');

$command = 'SELECT idnum,reporter FROM psypets_abusereports WHERE type=\'mail\'';
$psymails = $database->FetchMultiple($command, 'fetching mail abuse reports');

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Abuse Reports</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Abuse Reports</h4>
<h5>Plaza Abuse</h5>
<?php
if(count($reported_posts) > 0)
{
?>
<table>
 <tr class="titlerow">
  <th>Post</th>
  <th>View</th>
 </tr>
<?php
  $rowclass = begin_row_class();

  foreach($reported_posts as $report)
  {
?>
<tr class="<?= $rowclass ?>">
 <td><a href="/jumptopost.php?postid=<?= $report['threadid'] ?>">Post #<?= $report['threadid'] ?></a></td>
 <td><a href="/admin/abusereports_details.php?threadid=<?= $report['threadid'] ?>"><?= $report['count'] ?> report<?= $report['count'] != 1 ? 's' : '' ?></td>
</tr>
<?php
  }

  echo '</table>';
}
else
  echo '<p>No posts reported.</p>';

echo '<h5>PsyMail Abuse</h5>';

if(count($psymails) > 0)
{
?>
<table>
 <tr class="titlerow">
  <th>Reporter</th>
  <th>View</th>
 </tr>
<?php
  $rowclass = begin_row_class();

  foreach($psymails as $psymail)
  {
    $author = get_user_byid($psymail['reporter'], 'display');
?>
<tr class="<?= $rowclass ?>">
 <td><?= resident_link($author['display']) ?></td>
 <td><a href="/admin/abusereports_maildetails.php?idnum=<?= $psymail['idnum'] ?>">1 report</td>
</tr>
<?php
  }

  echo '</table>';
}
else
  echo '<p>None reported.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
