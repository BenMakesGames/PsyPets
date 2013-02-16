<?php
$whereat = 'post';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';

$now = time();

if(strlen($user['mailboxes']) > 0)
  $mailbox_folders = explode(',', $user['mailboxes']);
else
  $mailbox_folders = array();

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s Mailbox &gt; Manage Folders</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/post.php"><?= $user['display'] ?>'s Mailbox</a> &gt; Manage Folders</h4>
     <ul class="tabbed">
      <li class="activetab"><a href="/post.php">Mailbox</a></li>
      <li><a href="/post_sent.php">Sent Mail</a></li>
      <li><a href="/writemail.php">Write Mail</a></li>
     </ul>
     <p>Deleting a folder will move all of the mail in that folder back to the Inbox.</p>
<?php
if($_POST['message'])
  echo '<p class="failure">', $_POST['message'], '</p>';
?>
     <table>
      <tr class="titlerow">
       <th class="centered">Action</th>
       <th>Folder&nbsp;Name</th>
      </tr>
<?php
$bgcolor = begin_row_class();

if(count($mailbox_folders) > 0)
{
  foreach($mailbox_folders as $id=>$folder)
  {
?>
      <form action="/updatefolder.php?delete" method="post">
      <input type="hidden" name="folder" value="<?= $id ?>" />
      <tr class="<?= $bgcolor ?>">
       <td><input type="submit" value="Delete" /></td>
       <td><?= $folder ?></td>
      </tr>
      </form>
<?php
    $bgcolor = alt_row_class($bgcolor);
  }
}
?>
      <form action="/updatefolder.php?create" method="post">
      <tr class="<?= $bgcolor ?>">
       <td><input type="submit" value="Create" /></td>
       <td><input name="folder" maxlength="16" size="16" /></td>
      </tr>
      </form>
     </table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
