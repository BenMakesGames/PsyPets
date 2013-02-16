<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/warninglib.php';

if($admin['manageaccounts'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$resident = get_user_bydisplay($_GET['resident']);

if($resident === false)
{
  header('Location: ./adminresident.php');
  exit();
}

if($resident['is_npc'] == 'yes')
{
  header('Location: ./adminresident.php');
  exit();
}

$warnings = get_warnings_byuserid($resident['idnum']);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Resident Warnings</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Abusive Behavior &gt; <?= $resident['display'] ?></h4>
     <ul>
      <li><a href="/residentprofile.php?resident=<?= link_safe($resident['display']) ?>">View resident's profile</a></li>
     </ul>
     <h5>Abusive Behaviors</h5>
<?php
if(count($warnings) > 0)
{
  echo '<ul>';

  foreach($warnings as $warning)
  {
?>
     <li>
      <h5><?= Duration($now - $warning['timestamp'], 2) ?> ago</h5>
      <p><?= $warning['adminnote'] ?></p>
     </li>
<?php
  }

  echo '</ul>';
}
else
  echo '<p>This resident has no logged abusive behavior.</p>';
?>
     <h5>Log Absuive Behavior</h5>
     <form action="/admin/residentwarnings_add.php?resident=<?= link_safe($resident['display']) ?>" method="post">
     <p><textarea name="adminnote" cols="60" rows="6">no comment</textarea></p>
     <p><input type="submit" value="Submit" class="bigbutton" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
