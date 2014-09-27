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

$command = 'SELECT * FROM psypets_ideachart_complete WHERE idnum=' . $ideaid . ' LIMIT 1';
$idea = $database->FetchSingle($command, 'fetching idea details');

if($idea === false)
{
  header('Location: /todolist_completed.php');
  exit();
}

$poster = get_user_byid($idea['authorid'], 'display');
$mover = get_user_byid($idea['moverid'], 'display');

$is_manager = ($user['admin']['managewishlist'] == 'yes');

$statuses = array('implemented' => 'Implemented', 'obsolete' => 'Obsolete', 'voteout' => 'Voted out', 'shotdown' => 'Shot down by an admin');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; To-do List &gt; Completed Items &gt; <?= $idea['sdesc'] ?></title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="arrangewishes.php">To-do List</a> &gt; <a href="todolist_completed.php">Completed Items</a> &gt; <?= $idea['sdesc'] ?></h4>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo '<p>' . $error_message . '</p>';

render_idea_details($idea);
?>
     <p>
      &mdash; posted by <a href="residentprofile.php?resident=<?= link_safe($poster['display']) ?>"><?= $poster['display'] ?></a> <?= Duration($now - $idea['postdate'], 2) ?> ago<br />
      <span style="color: white;">&mdash;</span> marked as "<?= $statuses[$idea['status']] ?>" by <a href="residentprofile.php?resident=<?= link_safe($mover['display']) ?>"><?= $mover['display'] ?></a> <?= Duration($now - $idea['completedate'], 2) ?> ago
     </p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
