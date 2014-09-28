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

if($admin['manageaccounts'] != 'yes')
{
  header('Location: /');
  exit();
}

$userid = (int)$_GET['userid'];

$this_user = get_user_byid($userid);

$plazaid = (int)$_GET['plazaid'];

$command = 'SELECT COUNT(a.idnum) AS posts,b.plaza FROM monster_posts AS a LEFT JOIN monster_threads AS b ON a.threadid=b.idnum WHERE a.createdby=' . $userid . ' GROUP BY b.plaza';
$counts = $database->FetchMultiple($command, 'fetching post counts');

if($plazaid > 0)
{
  $page = (int)$_GET['page'];
  if($page < 1)
    $page = 1;

  $command = 'SELECT a.* FROM monster_posts AS a RIGHT JOIN monster_threads AS b ON a.threadid=b.idnum WHERE a.createdby=' . $userid . ' AND b.plaza=' . $plazaid . ' ORDER BY idnum DESC LIMIT ' . (($page - 1) * 20) . ',20';
  $posts = $database->FetchMultiple($command, 'fetching posts from plaza section');
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Resident Plaza Use &gt; <?= $this_user['display'] ?></title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Resident Plaza Use &gt; <?= $this_user['display'] ?></h4>
     <table>
      <tr class="titlerow">
       <th>Plaza</th>
       <th>Posts</th>
      </tr>
<?php
$rowclass = begin_row_class();

foreach($counts as $count)
{
  $command = 'SELECT title FROM monster_plaza WHERE idnum=' . $count['plaza'] . ' LIMIT 1';
  $plazainfo = $database->FetchSingle($command, 'fetching plaza title');
  
  if($count['plaza'] == $plazaid)
    $plazatitle = $plazainfo['title'];
?>
      <tr class="<?= $rowclass ?>">
       <td><?= $plazainfo['title'] ?></td>
       <td><a href="/admin/residentplazause.php?userid=<?= $userid ?>&plazaid=<?= $count['plaza'] ?>"><?= $count['posts'] ?></a></td> 
      </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
     </table>
<?php
if($plazaid > 0)
{
  if(count($posts) > 0)
  {
?>
     <h5>Posts in <?= $plazatitle ?></h5> 
     <ul>
<?php
    if($page > 1)
      echo '<li><a href="adminresidentplazause.php?userid=' . $userid . '&plazaid=' . $plazaid . '&page=' . ($page + 1) . '">Previous 20</a></li>';
?>
      <li><a href="/admin/residentplazause.php?userid=<?= $userid ?>&plazaid=<?= $plazaid ?>&page=<?= $page + 1 ?>">Next 20</a></li>
     </ul>
     <table>
      <tr class="titlerow">
       <th>When</th>
       <th><nobr>Thread ID</nobr></th>
       <th width="100%"></th>
      </tr>
<?php
    $rowclass = begin_row_class();

    foreach($posts as $post)
    {
?>
      <tr class="<?= $rowclass ?>">
       <th><nobr><?= Duration($now - $post['creationdate'], 2) ?> ago</nobr></th>
       <th class="centered"><a href="/viewthread.php?threadid=<?= $post['threadid'] ?>"><?= $post['threadid'] ?></a></th>
       <th><?= $post['title'] ?></th>
      </tr>
      <tr class="<?= $rowclass ?>">
       <td colspan="3"><?= $post['body'] ?></td>
      </tr>
<?php
      $rowclass = alt_row_class($rowclass);
    }
?>
     </table>
     <ul>
<?php
    if($page > 1)
      echo '<li><a href="/admin/residentplazause.php?userid=' . $userid . '&plazaid=' . $plazaid . '&page=' . ($page + 1) . '">Previous 20</a></li>';
?>
      <li><a href="/admin/residentplazause.php?userid=<?= $userid ?>&plazaid=<?= $plazaid ?>&page=<?= $page + 1 ?>">Next 20</a></li>
     </ul>
<?php
  }
  else
    echo '<p>No posts in this section.</p>';
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
