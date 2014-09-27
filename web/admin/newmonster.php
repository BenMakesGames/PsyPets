<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

// DISABLED
// Header("Location: /");

$require_petload = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";
require_once 'commons/adminmonstertypes.php';

if($admin["manageitems"] != "yes")
{
  header("Location: /admin/tools.php");
  exit();
}

if($_GET["edit"] == "prey")
{
  $edittype = "prey";
  $param = "prey";
}
else
{
  $edittype = "monsters";
  $param = "monster";
}

if($_POST["action"] == "create")
{
  $level = (int)$_POST['level'];
  $monster = trim($_POST['name']);
  $type = trim($_POST['type']);

  if($level < 1)
    $errors[] = 'Monster level must be at least 1.';

  if(strlen($monster) < 2)
    $errors[] = "Please provide a monster name.";

  $loot = explode(',', $_POST['loot']);
  $realloot = array();

  foreach($loot as $drop)
  {
    $data = explode('|', $drop);
    $itemname = trim($data[1]);
    $item = get_item_byname($itemname);
    if($item === false)
      $errors[] = "There is no item called \"$itemname\".";
    else
      $realloot[] = $data[0] . '|' . $itemname;
  }

  if(count($errors) == 0)
  {
    $command = '
      INSERT INTO monster_' . $edittype . ' (`level`, `name`, `type`, `prizes`) VALUES
      (
        ' . $level . ',
        ' . quote_smart($monster) . ',
        ' . quote_smart($type) . ',
        ' . quote_smart(implode(',', $realloot)) . '
      )
    ';

    $database->FetchNone(($command, 'adminnewmonster.php');
    
    Header("Location: ./adminmonstereditor.php?edit=$edittype");
    exit();
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Monster & Prey Editor &gt; New <?= ucfirst($param) ?></title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; <a href="/admin/monstereditor.php?edit=<?= $param ?>">Monster & Prey Editor</a> &gt; New <?= ucfirst($param) ?></h4>
<?php
if(count($errors) > 0)
{
  echo "<ul>\n";
  foreach($errors as $error)
    echo "<li style=\"color:red;\">$error</li>\n";
  echo "</ul>\n";
}
?>
     <form action="/admin/newmonster.php?edit=<?= $param ?>" method="post">
     <table>
      <tr>
       <th>Level:</th>
       <td><input name="level" maxlength=2 size=2 /></td>
      </tr>
      <tr>
       <th>Name:</th>
       <td><input name="name" maxlength="32" style="width: 256px;" /></td>
      </tr>
      <tr>
       <th>Type:</th>
       <td><?= monster_type_xhtml() ?></td>
      </tr>
      <tr>
       <th>Loot:</th>
       <td><textarea name="loot" rows=3 style="width: 256px;" /></textarea></td>
      </tr>
     </table>
     <p><input type="hidden" name="action" value="create" /><input type="submit" value="Create" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
