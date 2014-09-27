<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

$require_petload = 'no';
$invisible = 'yes';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/changeloglib.php';

//$changelog = get_latest_changelog();

if($admin['coder'] != 'yes')
{
  header('Location: /admintools.php');
  exit();
}

if($_POST['action'] == 'Preview' || $_POST['action'] == 'Add')
{
  $summary = htmlentities(trim($_POST['summary']));
  $details = nl2br(htmlentities(trim($_POST['details'])));
  $category = $_POST['category'];
  $impact = $_POST['impact'];
  
  if(!in_array($category, $CHANGELOG_CATEGORIES))
    $message_list[] = '<span class="failure">Don\'t forgot to select a category!</span>';
  if(!in_array($impact, $CHANGELOG_IMPACTS))
    $message_list[] = '<span class="failure">Don\'t forget to select an impact level!</span>';
  else if($_POST['action'] == 'Add')
  {
    new_changelog($category, $impact, $summary, $details);

    $message_list[] = '<span class="success">Added!</span>';
  
    $summary = $details = '';

    if($impact == 'small')
      $impact_value = 1;
    else if($impact == 'medium')
      $impact_value = 2;
    else if($impact == 'large')
      $impact_value = 3;
    else
      die('bad impact value!');
    
    $command = 'UPDATE monster_users SET newchangelogentries=\'yes\' WHERE notifynewchangelogentries<=' . $impact_value;
    $database->FetchNone($command, 'notifying players of new changelog entry');
  }
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Changelog</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Changelog</h4>
     <ul>
      <li><a href="/changelog.php">View changelog</a></li>
     </ul>
     <ul class="tabbed">
      <li class="activetab"><a href="/admin/changelog.php">Add Entry</a></li>
     </ul>
     <p style="color:#906;"><b>Hey, <?= $SETTINGS['author_real_name'] ?>:</b> Do you need to update immediategoal.php?</p>
<?php
if($_POST['action'] == 'Preview')
  echo '
    <h5>Preview</h5>
    <p><img src="//' . $SETTINGS['static_domain'] . '/gfx/changelog/' . $category . '.png" width="24" height="24" class="inlineimage" /> <b>' . $summary . '</b><p>' . $details . '</p><hr />
  ';
?>
     <form method="post">
     <table>
      <tr><th>Summary:</th><td><input type="text" maxlength="120" size="50" name="summary" value="<?= $summary ?>" /></td></tr>
      <tr>
       <th>Category:</th>
       <td><select name="category">
        <option value=""></option>
<?php
foreach($CHANGELOG_CATEGORIES as $cat)
  echo '<option value="' . $cat . '"' . ($category == $cat ? ' selected' : '') . '>' . $cat . '</option>';
?>
       </select></td>
      </tr>
      <tr>
       <th>Impact:</th>
       <td><select name="impact">
        <option value=""></option>
<?php
foreach($CHANGELOG_IMPACTS as $cat)
  echo '<option value="' . $cat . '"' . ($impact == $cat ? ' selected' : '') . '>' . $cat . '</option>';
?>
       </select></td>
      </tr>
      <tr><th colspan="2">Details (optional):</th><td></td>
      <tr><td colspan="2"><textarea cols="60" rows="10" name="details"><?= $details ?></textarea></td></tr>
      <tr><td colspan="2" class="righted"><input type="submit" name="action" value="Preview" /> <input type="submit" name="action" value="Add" /></td></tr>
     </table>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
