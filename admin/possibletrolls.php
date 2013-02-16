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

if($admin['manageaccounts'] != 'yes' && $admin['abusewatcher'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

if($_POST['action'] == 'Delete All')
{
  $database->FetchNone('TRUNCATE psypets_possible_trolling');
}
else if($_POST['action'] != '')
{
  foreach($_POST as $key=>$value)
  {
    if(substr($key, 0, 3) == 'id_')
      $report_ids[] = substr($key, 3);
  }

  $message_list[] = implode(',', $report_ids);

  if($_POST['action'] == 'Clean and Delete')
  {
    require_once 'commons/spamchecker.php';
    
    $bayesian_filter = new spamchecker();

    $clean_posts = $database->FetchMultiple('SELECT text FROM psypets_possible_trolling WHERE idnum IN (' . implode(',', $report_ids) . ') LIMIT ' . count($report_ids));
    
    foreach($clean_posts as $post)
      $bayesian_filter->train($post['text'], false);
  }

  $database->FetchNone('DELETE FROM psypets_possible_trolling WHERE idnum IN (' . implode(',', $report_ids) . ') LIMIT ' . count($report_ids));
}

$data = $database->FetchSingle('SELECT COUNT(idnum) AS c FROM psypets_possible_trolling');
$num_posts = $data['c'];

$num_pages = ceil($num_posts / 20);

$page = (int)$_GET['page'];

if($page < 1 || $page > $num_pages)
  $page = 1;

$reports = $database->FetchMultiple('SELECT * FROM psypets_possible_trolling ORDER BY idnum ASC LIMIT ' . (($page - 1) * 20) . ',20');

$pages = paginate($num_pages, $page, '?page=%s');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Bayesian Troll Detection</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Bayesian Troll Detection</h4>
<?php
if(count($reports) > 0)
{
  echo $pages;
?>
     <form method="post">
     <table>
      <tr class="titlerow"><th></th><th>When</th><th>Who</th><th>Post ID</th><th>Text</th></tr>
<?php
  $rowclass = begin_row_class();

  foreach($reports as $report)
  {
    $author = get_user_byid($report['userid'], 'display');
?>
      <tr class="<?= $rowclass ?>">
       <td><input type="checkbox" name="id_<?= $report['idnum'] ?>" /></td>
       <td class="centered"><?= duration($now - $report['timestamp'], 2) ?> ago</td>
       <td><?= resident_link($author['display']) ?></td>
       <td><a href="/jumptopost.php?postid=<?= $report['postid'] ?>"><?= $report['postid'] ?></a></td>
       <td><?= $report['text'] ?></td>
      </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
     </table>
     <p><input type="submit" name="action" value="Delete" /> <input type="submit" name="action" value="Clean and Delete" class="bigbutton" /></p>
     </form>
     <?= $pages ?>
     <form method="post">
     <p><input type="submit" name="action" value="Delete All" /></p>
     </form>
<?php
}
else
  echo '<p>None detected.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
