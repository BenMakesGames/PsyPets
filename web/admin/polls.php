<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

$require_petload = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/polllib.php';
require_once 'commons/globals.php';

if($admin['viewpolls'] != 'yes' && $admin['createpolls'] != 'yes')
{
  header('Location: /403.php');
  exit();
}

if($admin['createpolls'] == 'yes')
{
  if($_POST['submit'] == 'Change')
  {
    set_global('currentpoll', (int)$_POST['current']);
    
    $command = 'UPDATE monster_users SET newpoll=\'yes\'';
    $database->FetchNone($command, 'setting newpoll flag for all players');
    
    $user['newpoll'] = 'yes';
  }

  $current_poll = get_global('currentpoll');
}

$polls = get_polls();

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &nbsp; Administrative Tools &nbsp; Poll Management</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Poll Management</h4>
<?php
if($error_message)
  echo "<p style=\"color:red;\">" . $error_message . "</p>\n";

if($admin['createpolls'] == 'yes')
{
?>
<ul>
 <li><a href="/admin/newpoll.php">Create new poll</a></li>
</ul>
<?php
}
?>
<h5>Poll Details</h5>
<form method="post">
<?php
if($admin['createpolls'] == 'yes')
  echo '<p><input type="submit" name="submit" value="Change" /></p>';
?>
<table>
 <tr class="titlerow">
  <?= $admin['createpolls'] == 'yes' ? '<th>Current</th>' : '' ?><th></th><th>Poll</th>
 </tr>
<?php
$rowclass = begin_row_class();

foreach($polls as $poll)
{
?>
 <tr class="<?= $rowclass ?>">
<?php
  if($admin['createpolls'] == 'yes')
    echo '  <td class="centered"><input type="radio" name="current" value="' . $poll['idnum'] . '"' . ($current_poll == $poll['idnum'] ? ' checked' : '') . ' /></td>';
?>
  <td><a href="/polldetails.php?id=<?= $poll['idnum'] ?>"><img src="/gfx/search.gif" border="0" alt="" /></a></td>
  <td><?= $poll['title'] ?></td>
 </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
</table>
<?php
if($admin['createpolls'] == 'yes')
  echo '<p><input type="submit" name="submit" value="Change" /></p>';
?>
</form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
