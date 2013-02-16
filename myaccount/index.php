<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Account</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>My Account</h4>
     <ul>
      <li><a href="/myaccount/stats.php">My Statistics</a></li>
     </ul>
     <h5>Management</h5>
     <ul class="spacedlist">
<?php
if($user['childlockout'] == 'no')
{
?>
      <li>
       <a href="/myaccount/profile.php">Resident Profile</a><br />
       Edit your profile, avatar, and privacy settings here.<br />
      </li>
      <li>
       <a href="/myaccount/searchable.php">Searchable Profile</a><br />
       Create and edit your searchable profile here.  Once created, other residents will be able to find you using the <a href="directory.php">Resident Directory</a>.<br />
      </li>
<?php
}
?>
      <li>
       <a href="/myaccount/petprofile.php">Pet Profiles</a><br />
       Create and edit your pets' profile pages.
      </li>
      <li>
       <a href="/myaccount/display.php">Display Settings</a><br />
       Change visual settings, disable graphic smilies, alter your time-zone, etc.<br />
      </li>
      <li>
       <a href="/myaccount/behavior.php">Behavior Settings</a><br />
       Change behavior settings.<br />
      </li>
      <li>
       <a href="/myaccount/security.php">Account Management</a><br />
       Change your password or e-mail address.<br />
      </li>
      <li>
       <a href="/myaccount/favorhistory.php">Favor History</a><br />
       A complete history of Favor purchased, spent, and exchanged.<br />
      </li>
      <li>
       <a href="/myaccount/contentcontrol.php">Content Control</a><br />
       Settings for parents that want to lock out certain aspects of the site from their child.<br />
      </li>
     </ul>
     <h5>Tools</h5>
     <ul class="spacedlist">
      <li>
       <a href="/autofavor.php">Favor Dispenser</a><br />
       The Favor Dispenser can hand out some of the more common favor requests for you <em>instantly!</em><br />
      </li>
      <li>
       <a href="/graphicslibrary.php">Graphics Library</a><br />
       A few of the Favor Dispenser's functions require a graphic.  In these cases, graphics are provided by the Graphics Library.  Anyone may upload graphics to the Graphics Library.<br />
      </li>
      <li>
       <a href="/myaccount/loginhistory.php">Login History</a><br />
       Gives you information about your last several logins.  If you suspect someone else may be logging in to your account this could be helpful.<br />
      </li>
     </ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
