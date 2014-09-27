<?php
if($_GET['resident'] == $SETTINGS['site_ingame_mailer'])
{
  header('Location: /cityhall.php');
  exit();
}

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/formatting.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/userlib.php';
require_once 'commons/utility.php';
require_once 'commons/totemlib.php';

$profile_user = get_user_bydisplay($_GET['resident']);

if($profile_user === false)
{
  header('Location: /directory.php');
  exit();
}

if(($profile_user['activated'] != 'yes' || $profile_user['disabled'] != 'no') && $user['admin']['manageaccounts'] !== 'yes')
{
  header('Location: /directory.php');
  exit();
}

if($user['newcomment'] == 'yes' && $profile_user['idnum'] == $user['idnum'])
{
  $database->FetchNone('UPDATE monster_users SET newcomment=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1');
}

$data = $database->FetchSingle('SELECT COUNT(*) FROM psypets_profilecomments WHERE userid=' . $profile_user['idnum']);

$comment_count = $data['COUNT(*)'];

$max_pages = ceil($comment_count / 20);
$page = (int)$_GET['page'];

if($page < 1 || $page > $max_pages)
  $page = 1;

$comments = $database->FetchMultiple('SELECT * FROM psypets_profilecomments WHERE userid=' . $profile_user['idnum'] . ' ORDER BY idnum DESC LIMIT ' . (($page - 1) * 20) . ',20');

$pages = paginate($max_pages, $page, '/viewcomments.php?resident=' . $_GET['resident'] . '&page=%s');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $profile_user['display'] ?>'s Profile &gt; Comments</title>
<?php
include 'commons/head.php';
?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/residentprofile.php?resident=<?= link_safe($profile_user['display']) ?>"><?= $profile_user['display'] ?>'s Profile</a> &gt; Comments</h4>
<?php
if(count($comments) == 0)
  echo '<p>No one has left any comments on this profile.</p>';
else
{
  echo $pages;

  $first = true;
  foreach($comments as $comment)
  {
    $author = get_user_byid($comment['authorid'], 'display,graphic,is_a_whale');

    if($author === false)
      $author = array('graphic' => '../shim.gif', 'display' => '<i style="dim">Departed #' . $comment['authorid'] . '</i>');
    else
      $author['display'] = resident_link($author['display']);
?>
<table>
 <tr>
  <td valign="top" class="centered">
   <img src="<?= user_avatar($author) ?>" width="48" height="48" alt="" /><br />
<?php
if($profile_user['idnum'] == $user['idnum'])
  echo '   [<a href="/deletecomment.php?commentspage&amp;idnum=' . $comment['idnum'] . '" onclick="return confirm(\'Really delete this comment?\');">delete</a>]';
?>
  </td>
  <td valign="top">
   <p><b><?= $author['display'] ?> says:</b> <?= format_text($comment['comment']) ?></p>
   <p class="nomargin"><i class="dim">Posted <?= local_time($comment['timestamp'], $user['timezone'], $user['daylightsavings']) ?></p>
  </td>
 </tr>
</table>
<?php
  }

  echo $pages;
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
