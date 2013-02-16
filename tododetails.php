<?php
$require_login = 'no';
$wiki = 'To-do List';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/messages.php';
require_once 'commons/formatting.php';
require_once 'commons/todolistlib.php';

$ideaid = (int)$_GET['id'];

$command = 'SELECT * FROM psypets_ideachart WHERE idnum=' . $ideaid . ' LIMIT 1';
$idea = $database->FetchSingle($command, 'fetching idea details');

if($idea === false)
{
  header('Location: /todolist.php');
  exit();
}

$poster = get_user_byid($idea['authorid'], 'display');

$is_manager = ($user['admin']['managewishlist'] == 'yes');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; To-do List &gt; <?= $idea['sdesc'] ?></title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/arrangewishes.php">To-do List</a> &gt; <?= $idea['sdesc'] ?></h4>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo '<p>' . $error_message . '</p>';

if($is_manager)
  echo '<ul><li><a href="/admin/todoedit.php?id=' . $ideaid . '">Edit this entry</a></li></ul>';

render_idea_details($idea);

if($user['admin']['clairvoyant'] == 'yes')
  echo '<p class="dim">Note for admins: posted by <a href="/residentprofile.php?resident=' . link_safe($poster['display']) . '">' . $poster['display'] . '</a> ' . Duration($now - $idea['postdate'], 2) . ' ago</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
