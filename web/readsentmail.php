<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/maillib.php';
require_once 'commons/messages.php';

$mailid = (int)$_GET['mail'];

$mail = get_mail_byid($mailid);

if($mail['from'] != $user['user'])
{
  header('Location: ./post.php');
  exit();
}  

$to_user = get_user_byuser($mail['to'], 'display,graphic');

if($to_user === false)
{
  $to_user['graphic'] = '../shim.gif';
  $to_user['displaylink'] = '<i class="dim">[departed]</i>';
}
else
  $to_user['displaylink'] = '<a href="residentprofile.php?resident=' . link_safe($to_user['display']) . '">' . $to_user['display'] . '</a>';

$items = $database->FetchMultiple('SELECT idnum,subject,`to`,`new` FROM monster_mail WHERE `from`=' . quote_smart($user['user']) . ' ORDER BY date DESC');

$previtem = false;
$prevmail = false;
$nextmail = false;

foreach($items as $item)
{
  if($item['idnum'] == $mail['idnum'])
    $prevmail = $previtem;
  else if($previtem['idnum'] == $mail['idnum'])
  {
    $nextmail = $item;
    break;
  }

  $previtem = $item;
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s Sent Mail &gt; <?= $mail['subject'] ?></title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="post_sent.php"><?= $user['display'] ?>'s Sent Mail</a> &gt; <?= $mail['subject'] ?></h4>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";

if($_POST['message'])
  echo '<p class="failure">' . $_POST['message'] . '</p>';
?>
  <table>
   <tr class="leftbar">
    <td><img src="gfx/avatars/<?= $to_user['graphic'] ?>" width="48" height="48" alt="" /></td>
    <td width="100%">
     <table class="nomargin">
      <tr>
       <th>To:</th>
       <td width="100%"><?= $to_user['displaylink'] ?></td>
      </tr>
      <tr>
       <th>Subject:</th>
       <td><?= format_text($mail['subject']) ?></td>
      </tr>
      <tr>
       <th>Sent:</th>
       <td width="100%"><?= duration($now - $mail['date'], 2) ?> ago</td>
      </tr>
     </table>
    </tr>
   </tr>
   <tr>
    <td colspan="2"><?= format_text($mail['message']) ?></td>
   </tr>
   <tr class="leftbar">
    <td colspan="2"><?php
if($mail['attachments'] > 0)
  echo '<p><i>' . $mail['attachments'] . ' item' . ($mail['attachments'] == 1 ? ' was' : 's were') . ' sent along with this letter.  Find them in <a href="incoming.php">Incoming</a>.</i></p>';
else
  echo '<img src="gfx/shim.gif" width="1" height="1" alt="" /></td>';
?></tr>
  </table>
<?php
if($prevmail !== false || $nextmail !== false)
{
  echo '<ul>';

  if($prevmail !== false)
  {
    $to = get_user_byuser($prevmail['to'], 'display');

    echo '<li>Newer: <a href="readsentmail.php?mail=' . $prevmail['idnum'] . '" accesskey="," title="ALT + ,">' . $prevmail['subject'] . '</a>, to <a href="residentprofile.php?resident=' . link_safe($to['display']) . '">' . $to['display'] . '</a></li>';
  }

  if($nextmail !== false)
  {
    $to = get_user_byuser($nextmail['to'], 'display');

    echo '<li>Older: <a href="readsentmail.php?mail=' . $nextmail['idnum'] . '" accesskey="," title="ALT + ,">' . $nextmail['subject'] . '</a>, to <a href="residentprofile.php?resident=' . link_safe($to['display']) . '">' . $to['display'] . '</a></li>';
  }

  echo '</ul>';
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
