<?php
require_once 'commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/globals.php';
require_once 'commons/petlib.php';

$command = 'SELECT idnum,petname FROM monster_pets WHERE user=' . quote_smart($user['user']) . ' AND location=\'home\' ORDER BY orderid ASC';
$petlist = fetch_multiple($command, 'fetching pet list');

if(count($petlist) > 0)
{
  header('Location: /myaccount/petprofile.php?idnum=' . $petlist[0]['idnum']);
  exit();
}

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
     <p>You do not have any pets!</p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
