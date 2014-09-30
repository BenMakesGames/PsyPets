<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

if($admin['admintools'] != 'yes')
{
  header('Location: /404/');
  exit();
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools</title>
<?php include "commons/head.php"; ?>
  <style type="text/css">
   a.admintool { font-weight: bold; }
  </style>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Administrative Tools</h4>
<?php
if($admin['seeserversettings'] == 'yes')
  echo "<h5>Server Information</h5>\n" .
       "<ul class=\"spacedlist\">\n" .
       ' <li><a href="/admin/phpinfo.php" class="admintool">PHP Info</a></li>' .
       ' <li><a href="/admin/mysqlvars.php" class="admintool">MySQL Variables</a></li>' .
       ' <li><a href="/admin/errorlogs.php" class="admintool">Error Log Viewer</a></li>' .
       ' <li><a href="/admin/memcached.php" class="admintool">Memcached</a></li>' .
       '</ul>';

// RESIDENT ADMINISTRATION
if($admin['clairvoyant'] == 'yes' || $admin['massgift'] == 'yes' || $admin['viewpolls'] == 'yes' || $admin['createpolls'] == 'yes' || $admin['abusewatcher'] == 'yes' || $admin['manageaccounts'] == 'yes')
{
  echo '<h5>Resident Tools</h5>' .
       '<ul class="spacedlist">';

  if($admin['clairvoyant'] == 'yes')
    echo '
      <li><a href="/admin/resident.php" class="admintool">Resident Lookup & Tools</a></li>
      <li><a href="/admin/dailystats.php" class="admintool">Resident Activity Statistics</a><br />Statistics on Favor expenditure, and other key player activities.  (Should some day make graphs!)</li>
      <li><a href="/admin/avataruse.php" class="admintool">Avatar Graphic Use</a></li>
      <li><a href="/admin/wealthiest.php" class="admintool">Wealthiest 10</a></li>
      <li><a href="/admin/demographics.php" class="admintool">Resident Demographics</a><br />Genders of residents, grouped by age.</li>
      <li><a href="/admin/stats.php" class="admintool">Active Residents</a><br />Gives a count of all the users active within a given number of hours.</li>
    ';

  if($admin['massgift'] == 'yes')
    echo '<li><a href="/admin/gifts.php" class="admintool">Gift Residents</a></li>';

  if($admin['manageaccounts'] == 'yes')
    echo '<li><a href="/admin/loginfailures.php" class="admintool">Failed Login Attempts</a></li>';

  echo '</ul>';
}

// PET ADMINISTRATION
if($admin['clairvoyant'] == 'yes' || $admin['massgift'] == 'yes')
{
  echo '<h5>Pet Tools</h5><ul class="spacedlist">';

  if($admin['clairvoyant'] == 'yes')
    echo '
      <li><a href="/admin/petgraphics.php" class="admintool">Pet Graphic Use</a></li>
      <li><a href="/admin/allpetgraphics.php" class="admintool">List of All Pet Graphics</a></li>
      <li><a href="/admin/petcategories.php" class="admintool">Pet Graphic Breeding Categories</a></li>
      <li><a href="/admin/brokenequips.php" class="admintool">List of Pets With Missing Equipment</a></li>
    ';

  if($admin['massgift'] == 'yes')
    echo '<li><a href="/admin/massres.php" class="admintool">Perform Mass Resurrection</a></li>';

  echo '</ul>';
}

// PLAZA ADMINISTRATION
if($admin['abusewatcher'] == 'yes' || $admin['manageaccounts'] == 'yes' ||
  $admin['viewpolls'] == 'yes' || $admin['createpolls'] == 'yes')
{
  echo '<h5>Plaza Tools</h5><ul class="spacedlist">';

  if($admin['viewpolls'] == 'yes' || $admin['createpolls'] == 'yes')
    echo '<li><a href="/admin/polls.php" class="admintool">Poll Management</a></li>';

  if($admin['abusewatcher'] == 'yes' || $admin['manageaccounts'] == 'yes')
    echo '<li><a href="/admin/possibletrolls.php" class="admintool">Bayesian Troll Detection</a></li>';

  if($admin['abusewatcher'] == 'yes' || $admin['manageaccounts'] == 'yes')
    echo '<li><a href="/admin/traintrollsastrolls.php" class="admintool">Reset Bayesian Training</a></li>';

  if($admin['abusewatcher'] == 'yes')
    echo '<li><a href="/admin/abusereports.php" class="admintool">Abuse Reports</a></li>';

  if($admin['abusewatcher'] == 'yes')
    echo '<li><a href="/admin/plazapostvotes.php" class="admintool">Plaza Post Votes</a></li>';

  echo '</ul>';
}

// ITEM ADMINISTRATION
if($admin['manageitems'] == 'yes' || $admin['clairvoyant'] == 'yes')
{
  echo "     <h5>Item Tools</h5>\n" .
       "     <ul class=\"spacedlist\">\n";

  if($admin['manageitems'] == 'yes')
  {
    echo '<li><a href="/admin/anagramize_items.php" class="admintool">Anagramizer</a><br />View all item anagrams.</li>' .
         '<li><a href="/admin/recipeeditor.php" class="admintool">Recipe Viewer</a></li>';
  }

  if($admin['clairvoyant'] == 'yes')
  {
    echo '<li><a href="/admin/recipemaker.php" class="admintool">Recipe Maker helper</a></li>';
    echo '<li><a href="/admin/fusion_possibilities.php" class="admintool">Nuclear fusion possibilities</a></li>';
  }

  if($admin['manageitems'] == 'yes')
    echo '
      <li><a href="/admin/projecteditor.php" class="admintool">Project Viewer</a></li>
      <li><a href="/admin/monstereditor.php" class="admintool">Monster &amp; Prey Viewer</a></li>
      <li><b>Location Editor</b><br /><a href="/admin/locationeditor_gathering.php">Gathering</a> | <a href="/admin/locationeditor_mines.php">Mining</a> | <a href="/admin/locationeditor_lumberjacking.php">Lumberjacking</a></li>
      <li><a href="/admin/inconsistentitems.php" class="admintool">Inconsistent Items</a><br />Checks for items which have inconsistent properties (like being permanent, but gamesellable)</li>
      <li><a href="/admin/equipoutliers.php" class="admintool">Equipment Effect-Availability Outliers</li>
    ';

  if($admin['clairvoyant'] == 'yes')
    echo '
      <li><a href="/admin/equipments.php" class="admintool">Equipment by Stat</a></li>
      <li><a href="/admin/training.php" class="admintool">Training Items by Stat</a></li>
    ';

  if($admin['clairvoyant'] == 'yes')
    echo '
      <li><a href="/admin/itemthrowaways.php" class="admintool">Top Throw-away Items</a></li>
      <li><a href="/admin/wanteditems.php" class="admintool">Most-wanted Items</a></li>
      <li><a href="/admin/itemvalue.php" class="admintool">Market Analyzer</a><br />Take a look at the projected market values, or force them to update (requires special rights).</li>
      <li><a href="/admin/oddstats.php" class="admintool">Miscellaneous Item Statistics</a></li>
      <li><a href="/admin/encyclopedialessitems.php" class="admintool">Items Without Encyclopedia Entries</a></li>
    ';

  if($admin['manageitems'] == 'yes')
    echo '<li><a href="/admin/cheatsheet.php" class="admintool">Item Stat Cheat Sheet</a></li>';

  echo '</ul>';
}

// PAYMENT & FAVOR TOOLS
if($admin['managedonations'] == 'yes')
  echo '
    <h5>Payment &amp; Favor Tools</h5>
    <ul class="spacedlist">
     <li><a href="/admin/donations.php" class="admintool">Manage Payments</a><br />To view and <a href="/admin/newdonate.php">add payment</a> records.</li>
     <li><a href="/admin/favors.php" class="admintool">Manage Favors</a><br />To view and <a href="/admin/newfavor.php">add favor</a> records.</li>
    </ul>
  ';

echo '
  <h5>Miscellaneous Tools</h5>
  <ul class="spacedlist">
   <li><a href="/admin/testformatting.php" class="admintool">Formatting Test</a></li>
  </ul>
';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
