<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/maillib.php';
require_once 'commons/messages.php';

$mailid = (int)$_GET['mail'];

$mail = get_mail_byid($mailid);

if($mail['to'] != $user['user'])
{
  header('Location: /post.php?msg=149:No such mail exists%3f');
  exit();
}  

if(strlen($user['mailboxes']) > 0)
  $mailbox_folders = explode(',', $user['mailboxes']);
else
  $mailbox_folders = array();

if($mail['new'] == 'yes')
{
  $command = 'UPDATE monster_mail ' .
             "SET `new`='no' " .
             'WHERE idnum=' . (int)$_GET['mail'] . ' LIMIT 1';
  $database->FetchNone($command, 'readmail.php');
}

$from_user = get_user_byuser($mail['from'], 'display,graphic');

if($from_user === false)
{
  $from_user['graphic'] = '../shim.gif';
  $from_user['displaylink'] = '<i class="dim">[departed]</i>';
}
else
  $from_user['displaylink'] = '<a href="residentprofile.php?resident=' . link_safe($from_user['display']) . '">' . $from_user['display'] . '</a>';

if($_POST['submit'] == 'Delete')
{
  $command = 'DELETE FROM monster_mail ' .
             'WHERE idnum=' . quote_smart($_GET['mail']) . ' AND `to`=' . quote_smart($user['user']) . ' LIMIT 1';
  $database->FetchNone($command, 'readmail.php');

  header('Location: /post.php' . ($mail['location'] == 'post' ? '' : '?mailbox=' . $mail['location']));
  exit();
}
else if($_POST['submit'] == 'Reply')
{
  header('Location: /writemail.php?replyto=' . $mailid);
  exit();
}
else if($_POST['submit'] == 'Forward')
{
  header('Location: /writemail.php?forward=' . $mailid);
  exit();
}
else if($_POST['submit'] == 'Move To')
{
  if(in_array($_POST['whereto'], $mailbox_folders))
  {
    $mail['location'] = $_POST['whereto'];

    $command = 'UPDATE monster_mail SET `location`=' . quote_smart($mail['location']) . ' ' .
               'WHERE idnum=' . $mail['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'readmail.php');
  }
  else if($_POST['whereto'] == $user['email'])
  {
    if(email_psymail($mail, $user['email']))
      delete_mail_by_id($mail['idnum']);
    else
      $_POST['message'] = 'Failed to send one or more e-mails.  Lame.';

    header('Location: /post.php');
    exit();
  }
  else
    $_POST['message'] = 'The selected folder does not exist.';
}

$items = fetch_multiple('SELECT idnum,subject,`from`,`new` FROM monster_mail WHERE `to`=' . quote_smart($user['user']) . ' AND location=' . quote_smart($mail['location']) . ' ORDER BY date DESC');

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
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s Mailbox<?= $mail['location'] == 'post' ? '' : ' &gt; ' . $mail['location'] ?> &gt; <?= $mail['subject'] ?></title>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="post.php"><?= $user['display'] ?>'s Mailbox</a><?= $mail['location'] == 'post' ? '' : ' &gt; <a href="post.php?mailbox=' . $mail['location'] . '">' . $mail['location'] . '</a>' ?> &gt; <?= $mail['subject'] ?></h4>
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
       <td><img src="gfx/avatars/<?= $from_user['graphic'] ?>" width="48" height="48" alt="" /></td>
       <td width="100%">
        <table class="nomargin">
         <tr>
          <th>From:</th>
          <td width="100%"><?= $from_user['displaylink'] ?></td>
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
  echo '<i>' . $mail['attachments'] . ' item' . ($mail['attachments'] == 1 ? ' was' : 's were') . ' sent along with this letter.  Find ' . ($mail['attachments'] == 1 ? 'it' : 'them') . ' in <a href="incoming.php">Incoming</a>.</i>';
else
  echo '<img src="gfx/shim.gif" width="1" height="1" alt="" /></td>';
?></tr>
     </table>
     <table>
      <form action="readmail.php?mail=<?= $_GET["mail"] ?>" method="post">
      <tr>
       <td><input type="submit" name="submit" value="Delete" style="width:100px;" /></td>
       <td><input type="submit" name="submit" value="Reply" style="width:100px;" /></td>
       <td><input type="submit" name="submit" value="Forward" style="width:100px;" /></td>
       <td>
        <input type="submit" name="submit" value="Move To" style="width:100px;" />
        <select name="whereto">
<?php
  if($mail['location'] != 'post')
    echo '<option value="post">Inbox</option>';

  foreach($mailbox_folders as $folder)
  {
    if($folder == $mail['location'])
      continue;

    echo "         <option value=\"$folder\">$folder</option>\n";
  }

  echo '      <option value="' . $user['email'] . '">' . $user['email'] . '</option>';
?>
        </select>
       </td>
      </tr>
      </form>
     </table>
<?php
if($prevmail !== false || $nextmail !== false)
{
  echo '<ul>';

  if($prevmail !== false)
  {
    $from = get_user_byuser($prevmail['from'], 'display');

    echo '<li>Newer: ' . ($prevmail['new'] == 'yes' ? '<b>' : '') . '<a href="readmail.php?mail=' . $prevmail['idnum'] . '" accesskey="," title="ALT + ,">' . $prevmail['subject'] . '</a>, from <a href="residentprofile.php?resident=' . link_safe($from['display']) . '">' . $from['display'] . '</a>' . ($prevmail['new'] == 'yes' ? '</b>' : '') . '</li>';
  }

  if($nextmail !== false)
  {
    $from = get_user_byuser($nextmail['from'], 'display');

    echo '<li>Older: ' . ($nextmail['new'] == 'yes' ? '<b>' : '') . '<a href="readmail.php?mail=' . $nextmail['idnum'] . '" accesskey="," title="ALT + ,">' . $nextmail['subject'] . '</a>, from <a href="residentprofile.php?resident=' . link_safe($from['display']) . '">' . $from['display'] . '</a>' . ($nextmail['new'] == 'yes' ? '</b>' : '') . '</li>';
  }

  echo '</ul>';
}

echo '<ul><li><a href="reportmail.php?idnum=' . $mailid . '">Report this message</a></li></ul>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
