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

$GETbox = urldecode($_GET['mailbox']);

$locid = $user['locid'];
$house = get_house_byuser($user['idnum'], $locid);

if($house['locid'] != $locid)
{
  echo "Failed to load your house.<br />\n";
  exit();
}

$addons = take_apart(',', $house['addons']);

$sortoptions = array(
  'datea' => 'monster_mail.date ASC',
  'dated' => 'monster_mail.date DESC',
  'froma' => 'monster_users.display ASC',
  'fromd' => 'monster_users.display DESC',
  'subjecta' => 'monster_mail.subject ASC',
  'subjectd' => 'monster_mail.subject DESC'
);

$now = time();
$items = array();

$mailbox_folders = take_apart(',', $user['mailboxes']);

if(strlen($GETbox) > 0)
{
  if(in_array($GETbox, $mailbox_folders))
    $whereat = $GETbox;
}

$whereto = $_POST['whereto'];

if(strlen($_GET['sort']) > 0)
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
  $delete_command = array();
  $move_command = array();

  foreach($_POST as $key=>$value)
  {
/*
    $key = preg_replace("/_/", " ", $key);
    echo "$key, $value";
*/
    if(is_numeric($key))
    {
      if($value == "yes" || $value == "on")
      {
        $delete_command[] = "DELETE FROM monster_mail " .
                            "WHERE idnum=" . quote_smart($key) . " AND `to`=" . quote_smart($user["user"]) . " LIMIT 1";

        $move_command[] = "UPDATE monster_mail SET location=" . quote_smart($whereto) . " WHERE idnum=" . (int)$key . " AND `to`=" . quote_smart($user["user"]) . " LIMIT 1";
      }
    }
  }
}

if($_POST['submit'] == 'Delete')
{
  foreach($delete_command as $command)
    $database->FetchNone($command, 'deleting selected mail');
}
else if($_POST['submit'] == 'Move To')
{
  if($whereto == 'post' || in_array($whereto, $mailbox_folders))
  {
    foreach($move_command as $command)
      $database->FetchNone($command, 'moving selected mail');
  }
  else if($whereto == $user['email'])
  {
    foreach($_POST as $key=>$value)
    {
      if(is_numeric($key))
      {
        if($value == "yes" || $value == "on")
        {
          $command = 'SELECT idnum,`from`,subject,message FROM monster_mail WHERE idnum=' . (int)$key . ' AND `to`=' . quote_smart($user['user']) . ' LIMIT 1';
          $mail_info = $database->FetchSingle($command, 'fetching mail message');
          if($mail_info !== false)
          {
            if(email_psymail($mail_info, $user['email']))
              delete_mail_by_id($mail_info['idnum']);
            else
              $_POST['message'] = 'Failed to send one or more e-mails.  Lame.';
          }
        } // if the mail was selected
      }
    } // for each $_POST entry
  }
  else
    $_POST['message'] = 'The chosen folder does not exist.';
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s Sent Mail</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript">
   function check_all_mail()
   {
     i = document.maillist.elements.length;
     for(j = 1; j < i; ++j)
     {
       document.maillist.elements[j].checked = document.maillist.checkall.checked;
     }
   }
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><?= $user['display'] ?>'s Sent Mail</h4>
     <ul class="tabbed">
      <li><a href="post.php">Mailbox</a></li>
      <li class="activetab"><a href="post_sent.php">Sent Mail</a></li>
      <li><a href="writemail.php">Write Mail</a></li>
     </ul>
<?php
$command = 'SELECT COUNT(idnum) AS c FROM monster_mail WHERE `from`=' . quote_smart($user['user']) . ' ORDER BY idnum DESC';
$data = $database->FetchSingle($command, 'fetching mail count');
$mail_count = (int)$data['c'];

if($mail_count > 0)
{
  $num_pages = ceil($mail_count / 50);

  $page = (int)$_GET['page'];
  if($page < 1 || $page > $num_pages)
    $page = 1;
    
  $page_list = paginate($num_pages, $page, 'post_sent.php?page=%s');  

  $command = 'SELECT * FROM monster_mail ' .
             'WHERE `from`=' . quote_smart($user['user']) . ' ' .
             'ORDER BY idnum DESC LIMIT ' . (($page - 1) * 50) . ',50';

	$mails = $database->FetchMultiple($command);
?>
  <p>Sent mail remains here until the receiving Resident deletes it.</p>
  <?= $page_list ?>
  <table width="100%">
   <tr class="titlerow">
    <th>To</th>
    <th width="100%">Subject</th>
    <th>Sent</th>
   </tr>
<?php
  $mail_count = 0;

  $bgcolor = alt_row_class(begin_row_class());

  foreach($mails as $general_post)
  {
    $receiver = get_user_byuser($general_post['to']);
?>
   <tr class="<?= $bgcolor ?>">
<?php
    if($receiver === false)
    {
?>
    <td valign="top"><i class="dim">[departed]</i></td>
<?php
    }
    else
    {
?>
    <td valign="top"><nobr><a href="residentprofile.php?resident=<?= $receiver['display'] ?>"><?= $receiver["display"] ?></a></nobr></td>
<?php
    }
?>
    <td valign="top"><a href="readsentmail.php?mail=<?= $general_post['idnum'] ?>"><?= format_text($general_post["subject"]) ?></a></td>
    <td valign="top"><nobr><?= duration($now - $general_post['date'], 2) ?> ago</nobr></td>
   </tr>
<?php
    $bgcolor = alt_row_class($bgcolor);
  } // for mail
?>
   <tr>
  </table>
  <?= $page_list ?>
<?php
}
else
  echo "      <p>You have no sent mail.</p>\n";
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
