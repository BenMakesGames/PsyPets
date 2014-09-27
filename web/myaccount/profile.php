<?php
require_once 'commons/init.php';

$require_petload = 'no';
$child_safe = false;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/encryption.php';
require_once 'commons/formatting.php';
require_once 'commons/psypetsformatting.php';
require_once 'commons/globals.php';
require_once 'commons/profiles.php';

$avatargfx = array_combine(get_global('avatargfx'), get_global('avatardesc'));

$profile = get_user_profile_text($user['idnum']);

if($_POST['submit'] == 'Update Privacy')
{
  if($_POST['publicfriends'] == 'on' || $_POST['publicfriends'] == 'yes')
    $user['publicfriends'] = 'yes';
  else
    $user['publicfriends'] = 'no';

  if($_POST['profilecomments'] == 'all')
    $user['profilecomments'] = 'all';
  else if($_POST['profilecomments'] == 'friends')
    $user['profilecomments'] = 'friends';
  else
    $user['profilecomments'] = 'none';

  $command = 'UPDATE monster_users SET publicfriends=' . quote_smart($user['publicfriends']) . ', ' .
             'profilecomments=' . quote_smart($user['profilecomments']) . ' ' .
             'WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'updating privacy settings');

  $general_message = 'Privacy preferences updated!';
}
else if($_POST['submit'] == 'Update Profile')
{
  $profile_text = trim($_POST['profile']);

  if($profile === false && $profile_text != '')
    create_user_profile($user['idnum'], $profile_text);
  else if($profile_text != $profile['text'])
    update_user_profile($user['idnum'], $profile_text);

  $profile = get_user_profile_text($user['idnum']);
}
else if($_POST['submit'] == 'Update Avatar')
{
  if(array_key_exists($_POST['avatar'], $avatargfx))
  {
    if($user['graphic'] != $_POST['avatar'])
    {
      $user['graphic'] = $_POST['avatar'];

      $command = 'UPDATE monster_users SET `graphic`=' . quote_smart($user['graphic']) . ' ' .
                 'WHERE `user`=' . quote_smart($user['user']) . ' LIMIT 1';
      $database->FetchNone($command, 'changing avatar');
    }
  }

  $color = substr($_POST['color'], 1, 6);
	
  if(preg_match('/[a-z0-9]/i', $_POST['color']) > 0)
  { 
    $user['color'] = $color;
    $database->FetchNone('UPDATE monster_users SET color="' . $user['color'] . '" WHERE idnum=' . $user['idnum'] . ' LIMIT 1');
  }

  $user['defaultstyle'] = trim($_POST['autoformat']);

  $database->FetchNone('
		UPDATE monster_users
		SET
			defaultstyle=' . $database->Quote($user['defaultstyle']) . '
		WHERE idnum=' . $user['idnum'] . '
		LIMIT 1
	');

  $general_message = 'Avatar preferences updated!';
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Account &gt; Resident Profile</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript">
		$(function() {
			init_textarea_editor();
		});
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myaccount/">My Account</a> &gt; Resident Profile</h4>
     <ul class="tabbed">
      <li class="activetab"><a href="/myaccount/profile.php">Resident&nbsp;Profile</a></li>
      <li><a href="/myaccount/searchable.php">Searchable&nbsp;Profile</a></li>
      <li><a href="/myaccount/petprofile.php">Pet&nbsp;Profiles</a></li>
      <li><a href="/myaccount/display.php">Display&nbsp;Settings</a></li>
      <li><a href="/myaccount/behavior.php">Behavior&nbsp;Settings</a></li>
      <li><a href="/myaccount/security.php">Account&nbsp;Management</a></li>
      <li><a href="/myaccount/favorhistory.php">Favor&nbsp;History</a></li>
      <li><a href="/myaccount/contentcontrol.php">Content&nbsp;Control</a></li>
     </ul>
<?php
 if($general_message)
   echo '     <p style="color:blue;">' . $general_message . '</p>';
?>
  <form method="post">
  <table>
   <tr class="titlerow">
    <td colspan="2" class="centered"><h4>Resident&nbsp;Profile</h4></td>
    <td></td>
   </tr>
   <tr>
    <td class="leftbar" valign="top">Profile&nbsp;preview:</td>
    <td colspan="2">
     <?= format_text($profile['text']) ?>
    </td>
   </tr>
   <tr>
    <td class="leftbar">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
   </tr>
   <tr>
    <td class="leftbar" valign="top">Edit&nbsp;profile:</td>
    <td colspan="2">
		 <ul data-target="profile-body" class="textarea-editor"></ul>
     <textarea id="profile-body" name="profile" cols="60" rows="10" style="width:600px;"><?= htmlspecialchars($profile['text']) ?></textarea>
    </td>
   </tr>
   <tr>
    <td class="leftbar"></td>
    <td colspan="2"></td>
   </tr>
   <tr>
    <td class="leftbar">Profile Items:</td>
    <td colspan="2"><a href="/myaccount/profile_items.php">Change which items are shown on my profile.</a></td>
   </tr>
   <tr>
    <td></td>
    <td colspan="2"></td>
   </tr>
   <tr>
    <td colspan="3" align="center"><input type="submit" name="submit" value="Update Profile" class="bigbutton" /></td>
   </tr>
   <tr>
    <td></td>
    <td colspan="2"></td>
   </tr>

	 <tr class="titlerow">
	  <td colspan="2" class="centered"><h4>Avatar &amp; Public Presence</h4></td>
		<td></td>
	 </tr>
   <tr>
    <td class="leftbar" valign="top">Avatar:</td>
    <td valign="top" colspan="2">
     <table>
      <tr>
<?php
 $col = 0;
 $row = 0;

 if(array_key_exists($user['graphic'], $avatargfx) == false)
 {
   ++$col;
?>
       <td align="center">
        <img src="/gfx/avatars/<?= $user['graphic'] ?>" width="48" height="48" /><br />
        <input type="radio" name="avatar" value="<?= $user['graphic'] ?>" checked="checked" /><br />
       </td>
<?php
 }

 foreach($avatargfx as $imgsrc=>$imgname)
 {
   if($col == 10)
   {
     $col = 0;
     ++$row;
?>
      </tr>
      <tr>
<?php
   }

   ++$col;
?>
       <td align="center">
        <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/avatars/<?= $imgsrc ?>" width="48" height="48" alt="<?= $imgname ?>" /><br />
        <input type="radio" name="avatar" value="<?= $imgsrc ?>"<?= ($user['graphic'] == $imgsrc ? ' checked="checked"' : '') ?> /><br />
       </td>
<?php
 }
?>
      </tr>
     </table>
    </td>
   </tr>
   <tr>
    <td class="leftbar"></td>
    <td colspan="2"></td>
   </tr>
   <tr>
    <td class="leftbar">Worn Badges:</td>
    <td colspan="2">To choose which Badges you're wearing, <a href="/badgedb.php">visit The Badge Archive</a>.</td>
   </tr>
   <tr>
    <td class="leftbar"></td>
    <td colspan="2"></td>
   </tr>
   <tr>
    <td class="leftbar" valign="top">Favorite Color:</td>
    <td>
     <script type="text/javascript" src="/commons/farbtastic/farbtastic.js"></script>
     <link rel="stylesheet" href="/commons/farbtastic/farbtastic.css" type="text/css" />
		 <div id="colorpicker"></div>
		 <input type="text" style="width:100px; margin-left:49px;" id="color" name="color" value="#<?= $user['color'] ?>" />
		 <script type="text/javascript">
		  $(function() { $('#colorpicker').farbtastic('#color'); });
		 </script>
    </td>
    <td valign="top">Your forum posts and PsyMails will be wreathed with this color.</td>
   </tr>
   <tr>
    <td class="leftbar"></td>
    <td colspan="2"></td>
   </tr>
   <tr>
    <td class="leftbar">Pre-formatting:</td>
    <td>
     <input name="autoformat" value="<?= htmlspecialchars($user['defaultstyle']) ?>" maxlength="50" style="width:200px;" /><br />
    </td>
    <td>This text will begin all your Plaza posts and PsyMail.</td>
   </tr>
   <tr>
    <td></td>
    <td colspan="2"></td>
   </tr>
   <tr>
    <td colspan="3" align="center"><input type="submit" name="submit" value="Update Avatar" class="bigbutton" /></td>
   </tr>
   <tr>
    <td></td>
    <td colspan="2"></td>
   </tr>

   <tr class="titlerow">
    <td colspan="2" class="centered"><h4>Privacy&nbsp;Settings</h4></td>
    <td> </td>
   </tr>
   <tr>
    <td class="leftbar">Public&nbsp;friends:</td>
    <td>
     <input type="checkbox" name="publicfriends"<?= ($user['publicfriends'] == 'yes') ? ' checked="checked"' : '' ?> /><br />
    </td>
    <td>If this is checked your friend list will be visible in your profile.</td>
   </tr>
   <tr>
    <td class="leftbar" valign="top">Profile&nbsp;comments:</td>
    <td>
     <input type="radio" name="profilecomments" value="all"<?= ($user['profilecomments'] == 'all') ? ' checked="checked"' : '' ?> /> Anyone may comment on my profile<br />
     <input type="radio" name="profilecomments" value="friends"<?= ($user['profilecomments'] == 'friends') ? ' checked="checked"' : '' ?> /> Only friends may do so<br />
     <input type="radio" name="profilecomments" value="none"<?= ($user['profilecomments'] == 'none') ? ' checked="checked"' : '' ?> /> No one may do so<br />
    </td>
    <td></td>
   </tr>
   <tr>
    <td></td>
    <td colspan="2"></td>
   </tr>
   <tr>
    <td colspan="3" align="center"><input type="submit" name="submit" value="Update Privacy" class="bigbutton" /></td>
   </tr>
   <tr>
    <td></td>
    <td colspan="2"></td>
   </tr>
  </table>
  </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
