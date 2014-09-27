<?php
$IGNORE_MAINTENANCE = true;

require_once 'commons/init.php';

// DISABLED
// Header("Location: /");

$require_petload = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';

if($admin['manageitems'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$idnum = (int)$_GET['idnum'];

$command = 'SELECT * FROM psypets_locations WHERE idnum=' . $idnum . ' LIMIT 1';
$location = $database->FetchSingle($command, 'admineditlocation.php');

if($_POST['action'] == 'update')
{
  $_POST['name'] = trim($_POST['name']);

  if($_POST['name'] != $location['name'])
  {
    $command = 'SELECT * FROM psypets_locations WHERE name=' . quote_smart($_POST['name']) . ' LIMIT 1';
    $other_location = $database->FetchSingle($command, 'admineditlocation.php');
    
    if($other_location !== false)
      $errors[] = 'There is already a location called "' . $_POST['name'] . '".';
  }

  $loot = explode(',', $_POST['loot']);
  $realloot = array();

  foreach($loot as $prize)
  {
    $rate = explode('|', $prize);

    $itemname = $rate[1];
    $itemname = trim($itemname);

    $item = get_item_byname($itemname);
    if($item === false)
      $errors[] = 'There is no item called "' . $itemname . '".';
    else
      $realloot[] = $rate[0] . '|' . $item['itemname'];
  }

  if(count($errors) == 0)
  {
    $level = (int)$_POST['level'];
    $q_name = quote_smart($_POST['name']);
    $q_prizes = quote_smart(implode(',', $realloot));

    $command = "UPDATE psypets_locations SET level=$level,name=$q_name,prizes=$q_prizes WHERE idnum=" . $idnum . " LIMIT 1";
    $database->FetchNone($command, 'admineditlocation.php');

    if($_POST['submit'] == 'Update and Back')
      header('Location: /admin/locationeditor.php');
    else
      header('Location: /admin/editlocation.php?msg=48&idnum=' . $idnum);

    exit();
  }
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Location Editor &gt; Edit <?= ucfirst($location['name']) ?></title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; <a href="/admin/locationeditor.php">Location Editor</a> &gt; Edit <?= ucfirst($location['name']) ?></h4>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

if($error_message)
  echo "<p>$error_message</p>";

if(count($errors) > 0)
{
  echo "<ul>\n";
  foreach($errors as $error)
    echo "<li>$error</li>\n";
  echo "</ul>\n";
}
?>
     <form method="post">
     <table>
      <tr>
       <th>Level:</th>
       <td colspan="2"><input name="level" maxlength="2" size="2" value="<?= $location['level'] ?>" /></td>
      </tr>
      <tr>
       <th>Name:</th>
       <td colspan="2"><input name="name" maxlength="56" style="width: 256px;" value="<?= $location['name'] ?>" /></td>
      </tr>
      <tr>
       <th>Loot:</th>
       <td colspan="2"><textarea name="loot" rows="3" style="width: 256px;" /><?= $location['prizes'] ?></textarea></td>
      </tr>
     </table>
     <p><input type="hidden" name="action" value="update" /><input type="submit" name="submit" value="Update" /> <input class="bigbutton" type="submit" name="submit" value="Update and Back" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
