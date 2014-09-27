<?php
$whereat = 'post';
$wiki = 'Post_Office';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/utility.php';
require_once 'commons/maillib.php';
require_once 'commons/questlib.php';

$GETbox = urldecode($_GET['mailbox']);

$mailbox_tutorial_quest = get_quest_value($user['idnum'], 'tutorial: mailbox');
if($mailbox_tutorial_quest === false)
  $no_tip = true;

if($user['newmail'] == 'yes')
{
  $command = 'UPDATE monster_users SET newmail=\'no\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'clearing new mail flag');

  $user['newmail'] = 'no';
}

$sortoptions = array(
  'datea' => 'monster_mail.date ASC',
  'dated' => 'monster_mail.date DESC',
  'froma' => 'monster_users.display ASC',
  'fromd' => 'monster_users.display DESC',
  'subjecta' => 'monster_mail.subject ASC',
  'subjectd' => 'monster_mail.subject DESC'
);

$items = array();

$mailbox_folders = take_apart(',', $user['mailboxes']);

if(strlen($GETbox) > 0)
{
  if(in_array($GETbox, $mailbox_folders) || $GETbox == 'Trash')
    $whereat = $GETbox;
}

$whereto = $_POST['whereto'];

if(array_key_exists('sort', $_GET))
{
  $sortwith = $_GET['sort'];
  if(array_key_exists($sortwith, $sortoptions))
  {
    $user['postofficesort'] = $sortwith;
    $command = 'UPDATE monster_users SET postofficesort=' . quote_smart($sortwith) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'post.php');
  }
  else
    $user['postofficesort'] = 'dated';
}

if(strlen($_POST['submit']) > 0)
{
  foreach($_POST as $key=>$value)
  {
/*
    $key = preg_replace("/_/", " ", $key);
    echo "$key, $value";
*/
    if(is_numeric($key))
    {
      if($value == 'yes' || $value == 'on')
        $mail_ids[] = (int)$key;
    }
  }
}

if(count($mail_ids) > 0)
{
  if($_POST['submit'] == 'Delete')
  {
    $command = 'UPDATE monster_mail SET location=\'Trash\' WHERE idnum IN (' . implode(',', $mail_ids) . ') AND `to`=' . quote_smart($user['user']) . ' LIMIT ' . count($mail_ids);
    $database->FetchNone($command, 'deleting selected mail');

    $deleted = $database->AffectedRows();

    if($deleted > 0)
    {
      $messages[] = '<span class="success">Moved ' . $deleted . ' PsyMail' . ($deleted == 1 ? '' : 's') . ' to your <a href="post.php?mailbox=Trash">Trash</a>.</span>';
    }
  }
  else if($_POST['submit'] == 'Move To')
  {
    if($whereto == 'post' || in_array($whereto, $mailbox_folders))
    {
      $command = 'UPDATE monster_mail SET location=' . quote_smart($whereto) . ' WHERE idnum IN (' . implode(',', $mail_ids) . ') AND `to`=' . quote_smart($user['user']) . ' LIMIT ' . count($mail_ids);
      $database->FetchNone($command, 'moving psymails');

      $moved = $database->AffectedRows();

      $messages[] = '<span class="success">Moved ' . $moved . ' PsyMail' . ($moved == 1 ? '' : 's') . '.</span>';
    }
    else if($whereto == $user['email'])
    {
      $command = 'SELECT idnum,`from`,subject,message FROM monster_mail WHERE idnum IN (' . implode(',', $mail_ids) . ') AND `to`=' . quote_smart($user['user']) . ' LIMIT ' . count($mail_ids);
      $mails = $database->FetchMultiple($command, 'fetching psymails for e-mailing');

      $deleted_mails = 0;
      
      foreach($mails as $mail)
      {
        if(email_psymail($mail, $user['email']))
        {
          delete_mail_by_id($mail['idnum']);
          $deleted_mails++;
        }
        else
          $messages[] = '<span class="failure">Failed to send one or more e-mails.  (If this problem persists, please let ' . $SETTINGS['author_resident_name'] . ' know!)</span>';
      }
      
      if($deleted_mails > 0)
      {
        $messages[] = '<span class="success">Forwarded ' . $deleted_mails . ' PsyMail' . ($deleted_mails == 1 ? '' : 's') . ' to ' . $user['email'] . '.</span>';

        require_once 'commons/statlib.php';
        record_stat($user['idnum'], 'PsyMails Forwarded to Your E-mail', $deleted_mails);
      }
    }
    else
      $error_message = 'The chosen folder does not exist!';
  }
  else
    $error_message = 'Do what now?';
}

include 'commons/html.php';
?>
 <head>
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s Mailbox<?= ($whereat != 'post' ? " &gt; $whereat" : '') ?></title>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/jquery.tablesorter.min.js"></script>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/mail4.js"></script>
  <style type="text/css">
   .newmail td, .newmail td * { font-weight: bold; }
  </style>
 </head>
 <body>
<?php
include 'commons/header_2.php';

if($mailbox_tutorial_quest === false)
{
  include 'commons/tutorial/post.php';
  add_quest_value($user['idnum'], 'tutorial: mailbox', 1);
}

$command = '
  SELECT * FROM monster_mail
  WHERE `to`=' . quote_smart($user['user']) . '
  AND location=' . quote_smart($whereat) . '
  ORDER BY idnum DESC
';
$mails = $database->FetchMultiple($command, 'fetching mail');

$command = 'SELECT COUNT(idnum) AS c FROM monster_mail WHERE `to`=' . quote_smart($user['user']) . ' AND location!=\'Trash\'';
$data = $database->FetchSingle($command, 'fetching mail count');

$mail_amount = (int)$data['c'];

?>
     <h4><?= $user['display'] ?>'s Mailbox<?= ($whereat != 'post' ? ' &gt; ' . $whereat : '') ?> <i>(<?= $mail_amount ?>/<?= $user['postsize'] ?>; <?= floor(($mail_amount * 100) / $user['postsize']) ?>% full)</i></h4>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo '<p class="failure">' . $error_message . '</p>';

if(count($messages) > 0)
  echo '<ul><li>' . implode('</li><li>', $messages) . '</li></ul>';
?>
     <ul class="tabbed">
      <li class="activetab"><a href="/post.php">Mailbox</a></li>
      <li><a href="/post_sent.php">Sent Mail</a></li>
      <li><a href="/writemail.php">Write Mail</a></li>
     </ul>
<?php
if($mail_amount >= $user['postsize'])
  echo '<p class="failure">Your mailbox is currently full.  You will not be able to receive mail from users, however official mail (event results, etc) will be delivered.  You might want to consider deleting some old mail.</p>';
  
echo '
  <ul class="tabbed">
   <li' . ($whereat == 'post' ? ' class="activetab"' : '') . '><a href="/post.php">Inbox</a></li>
';

foreach($mailbox_folders as $folder)
  echo '<li' . ($whereat == $folder ? ' class="activetab"' : '') . '><a href="/post.php?mailbox=' . urlencode($folder) . '">' . $folder . '</a></li>';

echo '
   <li' . ($whereat == 'Trash' ? ' class="activetab"' : '') . '><a href="/post.php?mailbox=Trash">Trash</a></li>
   <li style="border: 0pt none; background-color: transparent;"><a href="/postfolders.php"><img src="/gfx/pencil_small.png" alt="" class="inlineimage" /></a></li>
  </ul>
';

if($GETbox == 'Trash')
  echo '<p>Mail in Trash does not count against your total.  It will be deleted during server maintenance.</p>';

if(count($mails) > 0)
{
?>
     <form method="post" name="maillist" id="maillist">

  <table class="nomargin">
   <tr>
    <td><input type="submit" name="submit" value="Delete"<?php if($whereat == 'Trash') echo ' disabled="disabled"'; ?> /></td>
    <td>
     <input type="submit" name="submit" value="Move To" />
     <select name="whereto" onchange="document.maillist.whereto2.value=document.maillist.whereto.value;" />
<?php
  if($whereat != 'post')
    echo '<option value="post">Inbox</option>';

  foreach($mailbox_folders as $folder)
  {
    if($whereat == $folder)
      continue;

    echo '<option value="' . htmlentities($folder) . '">' . $folder . '</option>';
  }
?>
      <option value="<?= $user['email'] ?>"><?= $user['email'] ?></option>
     </select>
    </td>
   </tr>
  </table>

     <table width="100%" class="nomargin" id="mailtable">
      <thead>
       <tr>
        <th width="24"><input type="checkbox" name="checkall" onclick="check_all_mail();" /></th>
        <th></th>
        <th>From</th>
        <th><div style="width:16px;"></div></th>
        <th width="100%">Subject</th>
        <th>Sent</th>
       </tr>
      </thead>
      <tbody id="mailrows">
<?php
  $bgcolor = begin_row_class();

  foreach($mails as $general_post)
  {
    $sender = get_user_byuser($general_post['from'], 'display,is_admin,is_npc');

    if($sender['is_npc'] == 'yes')
      $sender_note = ' <a href="/npclist.php"><img src="/gfx/npctag.png" style="position:relative; top:-4px;" /></a>';
    else if($sender['is_admin'] == 'yes')
      $sender_note = ' <a href="/admincontact.php"><img src="/gfx/admintag.png" style="position:relative; top:-4px;" /></a>';
    else
      $sender_note = '';

    // if the mail is new, make the row of text bold
    if($general_post['new'] == 'yes')
      echo '<tr class="' . $bgcolor . ' newmail mailrow" id="mail' . $general_post['idnum'] . '">';
    else
      echo '<tr class="' . $bgcolor . ' mailrow" id="mail' . $general_post['idnum'] . '">';

    echo '
      <td valign="top"><input type="checkbox" name="' . $general_post['idnum'] . '"></td>
      <td valign="top" id="mailicon' . $general_post['idnum'] . '">
    ';

    if($general_post['replied'] == 'yes')
      echo '<img src="/gfx/mail_replied.png" width="16" height="16" alt="(replied)" />';
    else if($general_post['new'] == 'yes')
      echo '<img src="/gfx/mail_unread.png" width="16" height="16" alt="(unread)" />';
    else
      echo '<img src="/gfx/mail_read.png" width="16" height="16" alt="(read)" />';

    echo '</td><td valign="top">';

    if($sender === false)
      echo '<i class="dim">[departed]</i>';
    else
      echo '<nobr><a href="/residentprofile.php?resident=' . $sender['display'] . '">' . $sender['display'] . '</a>' . $sender_note . '</nobr>';

    if($general_post['starred'] == 'yes')
      $star_code = '<a href="#" onclick="unstar_mail(' . $general_post['idnum'] . '); return false;" style="color:#c93; font-weight: normal;">&#9733;</a>';
    else
      $star_code = '<a href="#" onclick="star_mail(' . $general_post['idnum'] . '); return false;" class="dim" style="font-weight: normal;">&#9734;</a>';

    echo '
       </td>
       <td class="centered"><span id="starmail' . $general_post['idnum'] . '">' . $star_code . '</span></td>
       <td valign="top"><a href="/readmail.php?mail=' . $general_post['idnum'] . '" onclick="readmail(' . $general_post['idnum'] . '); return false;">' . format_text($general_post["subject"]) . '</a></td>
       <td valign="top"><span style="display:none;">' . $general_post['idnum'] . '# </span><nobr>' . duration($now - $general_post['date'], 2) . ' ago</nobr></td>
      </tr>
      <tr class="' . $bgcolor . ' mailbodyrow" style="display:none;" id="mailbody' . $general_post['idnum'] . '"><td>&nbsp;</td><td>&nbsp;</td><td colspan="4"><div id="mailbodyxhtml' . $general_post['idnum'] . '"><center><img src="/gfx/throbber.gif" width="16" height="16" alt="loading..." /></center></div></td></tr>
    ';

    $bgcolor = alt_row_class($bgcolor);
  } // for each mail
?>
   </tbody>
  </table>
  <table>
   <tr>
   <td><input type="submit" name="submit" value="Delete"<?php if($whereat == 'Trash') echo ' disabled="disabled"'; ?> /></td>
    <td>
     <input type="submit" name="submit" value="Move To" />
     <select name="whereto2" onchange="document.maillist.whereto.value=document.maillist.whereto2.value;" />
<?php
    if($whereat != 'post')
      echo '<option value="post">Inbox</option>';

    foreach($mailbox_folders as $folder)
    {
      if($whereat == $folder)
        continue;

      echo '<option value="' . $folder . '">' . $folder . '</option>';
    }

    echo '<option value="' . $user['email'] . '">' . $user['email'] . '</option>';
?>
     </select>
    </td>
   </tr>
  </table>
     </form>
<?php
}
else if(count($mailbox_folders) == 0)
  echo '<p>You have no mail.</p>';
else
  echo '<p>There is no mail in this folder.</p>';
?>
     <ul>
      <li><a href="/myaccount/behavior.php">Change e-mail notification settings</a></li>
     </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
