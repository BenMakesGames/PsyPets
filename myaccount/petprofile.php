<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/globals.php';
require_once 'commons/petlib.php';

$petid = (int)$_GET['petid'];

$this_pet = get_pet_byid($petid);

$command = 'SELECT idnum,petname FROM monster_pets WHERE user=' . quote_smart($user['user']) . ' AND location=\'home\' ORDER BY orderid ASC';
$petlist = fetch_multiple($command, 'fetching pet list');

if(count($petlist) == 0)
{
  header('Location: /myaccount/petprofile_none.php');
  exit();
}

if($this_pet === false || $this_pet['user'] !== $user['user'])
{
  header('Location: /myaccount/petprofile.php?petid=' . $petlist[0]['idnum']);
  exit();
}

$profile = fetch_single('SELECT * FROM psypets_profile_pet WHERE petid=' . $petid . ' LIMIT 1');

if($_POST['submit'] == 'Update Profile')
{
  $profile_text = $_POST['profile'];

  if($profile === false)
  {
    $command = 'INSERT INTO psypets_profile_pet (petid, lastupdate, profile) VALUES ' .
               '(' . $petid . ', ' . $now . ', ' . quote_smart($profile_text) . ')';
    fetch_none($command, 'inserting pet profile');
  }
  else
  {
    $extra = '';

    if($profile_text != $profile['profile']);
      $extra .= ',lastupdate=' . $now;

    $command = 'UPDATE psypets_profile_pet SET `profile`=' . quote_smart($profile_text) . $extra .
             ' WHERE `petid`=' . $petid . ' LIMIT 1';
    fetch_none($command, 'updating pet profile');
  }

  header('Location: /myaccount/petprofile.php?petid=' . $petid);
  exit();
}
else
	$profile_text = $profile['profile'];

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Account &gt; Pet Profiles &gt; <?= $this_pet['petname'] ?></title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myaccount/">My Account</a> &gt; Pet Profiles &gt; <?= $this_pet['petname'] ?></h4>
     <ul class="tabbed">
      <li><a href="/myaccount/profile.php">Resident&nbsp;Profile</a></li>
      <li><a href="/myaccount/searchable.php">Searchable&nbsp;Profile</a></li>
      <li class="activetab"><a href="/myaccount/petprofile.php">Pet&nbsp;Profiles</a></li>
      <li><a href="/myaccount/display.php">Display&nbsp;Settings</a></li>
      <li><a href="/myaccount/behavior.php">Behavior&nbsp;Settings</a></li>
      <li><a href="/myaccount/security.php">Account&nbsp;Management</a></li>
      <li><a href="/myaccount/favorhistory.php">Favor&nbsp;History</a></li>
      <li><a href="/myaccount/contentcontrol.php">Content&nbsp;Control</a></li>
     </ul>
     <ul class="tabbed"><?php
foreach($petlist as $pet)
{
  echo '<li';
  if($pet['idnum'] == $petid)
    echo ' class="activetab"';
  echo '><a href="/myaccount/petprofile.php?petid=' . $pet['idnum'] . '">' . $pet['petname'] . '</a></li> ';
}
?></ul>
  <ul><li><a href="/petprofile.php?petid=<?= $petid ?>">View <?= $this_pet['petname'] ?>'s profile</a></li></ul>
<?php
 if($general_message)
   echo '     <p style="color:blue;">' . $general_message . '</p>';
?>
  <form action="/myaccount/petprofile.php?petid=<?= $petid ?>" method="post">
  <table>
   <tr class="titlerow">
    <td> </td>
    <td><h4>Pet&nbsp;Profile</h4></td>
   </tr>
   <tr>
    <td class="leftbar" valign="top">Profile&nbsp;preview:</td>
    <td>
     <?= format_text($profile['profile']) ?>
    </td>
   </tr>
   <tr>
    <td class="leftbar"> </td>
    <td> </td>
   </tr>
   <tr>
    <td class="leftbar" valign="top">Edit&nbsp;profile:</td>
    <td>
     <textarea name="profile" cols="50" rows="10" style="width:500px;"><?= $profile_text ?></textarea>
    </td>
   </tr>
  </table>
  <p><input type="submit" name="submit" value="Update Profile" class="bigbutton" /></p>
  </form>
<?php echo formatting_help(); ?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
