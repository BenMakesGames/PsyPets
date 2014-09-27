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

if($admin['deletespam'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$target = get_user_byid((int)$_GET['userid']);
if($target === false)
{
  header('Location: ./adminresident.php');
  exit();
}

if($_GET['action'] == 'delete')
  $_POST = $_GET;

if($_POST['action'] == 'delete')
{
  $postids = array();

  foreach($_POST as $key=>$value)
  {
    if(substr($key, 0, 2) == 'p_' && ($value == 'on' || $value == 'yes'))
    {
      $postid = (int)substr($key, 2);
      $reports[] = '<ul><li>' . $postid . implode('</li><li>', delete_post_byidnum($postid)) . '</li></ul>';
    }
  }
}

$command = 'SELECT COUNT(*) FROM monster_posts WHERE createdby=' . $target['idnum'];
$data = $database->FetchSingle($command, 'fetching resident posts count');

$num_pages = ceil($data['COUNT(*)'] / 20);

$page = (int)$_GET['page'];
if($page < 1)
  $page = 1;
else if($page > $num_pages)
  $page = $num_pages;

$command = 'SELECT * FROM monster_posts WHERE createdby=' . $target['idnum'] . ' ORDER BY idnum DESC LIMIT ' . (($page - 1) * 20) . ',20';
$posts = $database->FetchMultiple(($command, 'fetching residents posts');

$page_list = paginate($num_pages, $page, '/admin/spamcontrol.php?userid=' . $target['idnum'] . '&page=%s');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &nbsp; Administrative Tools &nbsp; Spam Control</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Spam Control</h4>
<?php
if(count($reports) > 0)
  echo '<ul><li>' . implode('</li></ul><ul><li>', $reports) . '</li></ul>';
?>
     <form method="post">
     <?= $page_list ?>
     <table>
<?php
foreach($posts as $post)
{
?>
      <tr class="titlerow">
       <td><input type="checkbox" name="p_<?= $post['idnum'] ?>" /></td>
       <th><a href="/jumptopost.php?postid=<?= $post['idnum'] ?>"><?= strlen($post['title']) > 0 ? $post['title'] : '[untitled]' ?></a></th>
      </tr>
      <tr>
       <td></td>
       <td><?= $post['body'] ?></td>
      </tr>
<?php
}
?>
     </table>
     <?= $page_list ?>
     <p><input type="hidden" name="action" value="delete" /><input type="submit" value="Delete as Spam" onclick="return confirm('Really delete the selected posts as spam?');" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
