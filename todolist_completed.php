<?php
$require_login = 'yes';
$wiki = 'To-do List';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/messages.php';
require_once 'commons/formatting.php';

$sort_options = array(
  0 => 'postdate DESC',
  1 => 'postdate ASC',
  2 => 'completedate DESC',
  3 => 'completedate ASC',
  4 => 'status ASC',
  5 => 'status DESC',
);

$statuses = array(
  'implemented' => 'Implemented',
  'obsolete' => 'Obsolete',
  'voteout' => 'Voted out',
  'shotdown' => 'Shot down by an admin',
  'duplicate' => 'Duplicate entry',
  'against-philosophies' => 'Does not fit within design philosophies'
);

$sort_index = 2;

if(array_key_exists('sort', $_GET))
{
  if(array_key_exists($_GET['sort'], $sort_options))
    $sort_index = $_GET['sort'];
}

$sortby = $sort_options[$sort_index];

$wishlist_time = microtime(true);

$command = 'SELECT * FROM psypets_ideachart_complete ORDER BY ' . $sortby;
$ideas = $database->FetchMultiple($command, 'fetching to do list');

$wishlist_time = (microtime(true) - $wishlist_time);

$footer_note = '<br />Took ' . round($wishlist_time, 4) . 's fetching and sorting the To-do List.';

$is_manager = ($user['admin']['managewishlist'] == 'yes');

if($user['wishlist_complete'] == 'yes')
{
  $command = 'UPDATE monster_users SET wishlist_complete=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'removing to-do list flag for this page');
  
  $user['wishlist_complete'] = 'no';
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; To-do List &gt; Completed Wishes</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>To-do List &gt; Completed Items</h4>
<?php
if($is_manager)
  echo '<ul><li><a href="/admin/todo_totals.php">Manage To-do List</a></li></ul>';
?>
     <ul class="tabbed">
      <li><a href="arrangewishes.php">Your Vote</a></li>
      <li class="activetab"><a href="todolist_completed.php">Completed Items</a></li>
     </ul>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo '<p>' . $error_message . '</p>';

if($sort_index == 0)
  $postdate_link = '<a href="todolist_completed.php?sort=1">&#9650;</a>';
else if($sort_index == 1)
  $postdate_link = '<a href="todolist_completed.php?sort=0">&#9660;</a>';
else
  $postdate_link = '<a href="todolist_completed.php?sort=0">&#9651;</a>';

if($sort_index == 2)
  $completedate_link = '<a href="todolist_completed.php?sort=3">&#9660;</a>';
else if($sort_index == 3)
  $completedate_link = '<a href="todolist_completed.php?sort=2">&#9650;</a>';
else
  $completedate_link = '<a href="todolist_completed.php?sort=2">&#9661;</a>';

if($sort_index == 4)
  $status_link = '<a href="todolist_completed.php?sort=5">&#9660;</a>';
else if($sort_index == 5)
  $status_link = '<a href="todolist_completed.php?sort=4">&#9650;</a>';
else
  $status_link = '<a href="todolist_completed.php?sort=4">&#9661;</a>';
?>
     <table>
      <tr class="titlerow">
       <th>To-do</th>
       <th class="centered">Posted <?= $postdate_link ?></th>
       <th class="centered">Completed <?= $completedate_link ?></th>
       <th class="centered">Fate <?= $status_link ?></th>
      </tr>
<?php
$rowclass = begin_row_class();

foreach($ideas as $idea)
{
?>
      <tr class="<?= $rowclass ?>">
       <td><a href="todocompleteddetails.php?id=<?= $idea['idnum'] ?>"><?= $idea['sdesc'] ?></a></td>
       <td class="centered"><?= Duration($now - $idea['postdate'], 2) ?> ago</td>
       <td class="centered"><?= Duration($now - $idea['completedate'], 2) ?> ago</td>
       <td class="centered"><?= $statuses[$idea['status']] ?></td>
      </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
     </table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
