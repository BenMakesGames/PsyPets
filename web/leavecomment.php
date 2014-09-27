<?php
if($_POST['ajax'] == 'yes')
  $resident_name = urldecode($_POST['resident']);
else
  $resident_name = $_GET['resident'];

$require_petload = 'no';
$child_safe = false;

if($resident_name == $SETTINGS['site_ingame_mailer'])
{
  if($_POST['ajax'] == 'yes')
    die('failed');
  else
  {
    header('Location: /cityhall.php');
    exit();
  }
}

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/formatting.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/userlib.php';
require_once 'commons/utility.php';

$profile_user = get_user_bydisplay($resident_name);

if($profile_user['is_npc'] != 'no')
{
  if($_POST['ajax'] == 'yes')
    die('failed');
  else
  {
    header('Location: ./directory.php');
    exit();
  }
}

if(($profile_user['childlockout'] == 'yes' || $profile_user['activated'] != 'yes' || $profile_user['disabled'] != 'no') && $user['admin']['manageaccounts'] !== 'yes')
{
  if($_POST['ajax'] == 'yes')
    die('failed');
  else
  {
    header('Location: /directory.php');
    exit();
  }
}

if($profile_user['profilecomments'] == 'none' || is_enemy($user, $profile_user) || is_enemy($profile_user, $user) || ($profile_user['profilecomments'] == 'friends' && !is_friend($profile_user, $user)))
{
  if($_POST['ajax'] == 'yes')
    die('failed');
  else
  {
    header('Location: /residentprofile.php?resident=' . link_safe($profile_user['display']));
    exit();
  }
}

$command = 'SELECT authorid,timestamp FROM psypets_profilecomments WHERE userid=' . $profile_user['idnum'] . ' ORDER BY idnum DESC LIMIT 1';
$last_comment = $database->FetchSingle($command, 'leaving comment');

if($last_comment['authorid'] != $user['idnum'] || $last_comment['timestamp'] < $now - (60 * 60))
{
  if($_POST['ajax'] == 'yes')
    $comment_text = trim(urldecode($_POST['comment']));
  else
    $comment_text = trim($_POST['comment']);

  if($comment_text != '')
  {
    $command = 'INSERT INTO psypets_profilecomments (userid, authorid, timestamp, comment) VALUES ' .
               '(' . $profile_user['idnum'] . ', ' . $user['idnum'] . ', \'' . $now . '\', ' .
               quote_smart($comment_text) . ')';
    $database->FetchNone($command, 'leaving comment');

    if($user['idnum'] != $profile_user['idnum'])
    {
      $command = 'UPDATE monster_users SET newcomment=\'yes\' WHERE idnum=' . $profile_user['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'leaving comment');
    }
  }
  else if($_POST['ajax'] == 'yes')
    die('blank');
}
else
  die('failed');

if($_POST['ajax'] == 'yes')
{
  $xhtml .= '
<table width="100%" class="profilecomment">
 <tr>
  <td valign="top" class="centered" width="56">
   <img src="' . user_avatar($user) . '" width="48" height="48" alt="" /><br />
  </td>
  <td valign="top">
   <table width="100%">
    <tr style="border-bottom: 1px solid #000">
     <td><b>' . $user['display'] . ' says...</b></td>
     <td align="right"><a href="commentdialog.php?1=' . $profile_user['display'] . '&2=' . $user['display'] . '"><img src="gfx/speak.gif" alt="(dialog view)" height="16" width="16" /></a></td>
    </tr>
    <tr>
     <td colspan="2">' . format_text($comment_text) . '</td>
    </tr>
   </table>
  </td>
 </tr>
</table>
  ';

  die($xhtml);
}
else
{
  header('Location: ./residentprofile.php?resident=' . link_safe($profile_user['display']) . '#comments');
  exit();
}
?>
