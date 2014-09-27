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
require_once "commons/messages.php";
require_once 'commons/adminmonstertypes.php';

if($admin['manageitems'] != "yes")
{
  header('Location: /admin/tools.php');
  exit();
}

if(array_key_exists("monster", $_GET))
{
  $edittype = "monsters";
  $param = "monster";
  $name = urldecode($_GET['monster']);
}
else if(array_key_exists("prey", $_GET))
{
  $edittype = "prey";
  $param = "prey";
  $name = urldecode($_GET["prey"]);
}
else
{
  header("Location: /admin/monstereditor.php");
  exit();
}

$command = "SELECT * FROM monster_$edittype WHERE name=" . quote_smart($name) . " LIMIT 1";
$result = mysql_query($command);

if(!$result)
{
  echo "/admin/editmonster.php<br />\n" .
       "Error in <i>$command</i><br />\n" .
       mysql_error() . "<br />\n";
  exit();
}

if(mysql_num_rows($result) == 0)
{
  header("Location: /admin/monstereditor.php");
  exit();
}

$monster = mysql_fetch_assoc($result);

mysql_free_result($result);

if($_POST['action'] == 'update')
{
  if($_POST["name"] != $name)
  {
    $command = "SELECT * FROM monster_$edittype WHERE name=" . quote_smart($_POST['name']) . " LIMIT 1";
    $result = mysql_query($command);

    if(!$result)
    {
      echo "/admin/editmonster.php<br />\n" .
           "Error in <i>$command</i><br />\n" .
           mysql_error() . "<br />\n";
      exit();
    }

    if(mysql_num_rows($result) > 0)
    {
      mysql_free_result($result);
      $errors[] = "There is already a $param \"" . $_POST['name'] . "\"";
    }
  }

  $loot = explode(',', $_POST['loot']);
  $realloot = array();

  foreach($loot as $prize)
  {
    $rate = explode('|', $prize);

    if(substr($prize, -7) == ' moneys')
    {
      $money = (int)substr($prize, 0, strlen($prize) - 7);
      if($money < 1)
        $errors[] = 'The amount of moneys should be at least 1.';
      else
        $realloot[] = $prize;
    }
    else
    {
      $itemname = $rate[1];
      $itemname = trim($itemname, "\n\r\t ");

      $item = get_item_byname($itemname);
      if($item === false)
        $errors[] = "There is no item called \"$itemname\".";
      else
        $realloot[] = $prize;
    }
  }

  if(count($errors) == 0)
  {
    $level = (int)$_POST['level'];
    $q_name = quote_smart($_POST['name']);
    $q_type = quote_smart($_POST['type']);
    $q_prizes = quote_smart(implode(',', $realloot));
    $q_graphic = quote_smart($_POST['graphic']);
    $min_stealth = (int)$_POST['min_stealth'];
    $min_stamina = (int)$_POST['min_stamina'];
    $min_athletics = (int)$_POST['min_athletics'];
    $min_wits = (int)$_POST['min_wits'];

    $command = "UPDATE monster_$edittype SET level=$level,name=$q_name,type=$q_type,prizes=$q_prizes,graphic=$q_graphic,min_stealth=$min_stealth,min_stamina=$min_stamina,min_athletics=$min_athletics,min_wits=$min_wits WHERE name=" . quote_smart($name) . " LIMIT 1";

    $result = mysql_query($command);

    if(!$result)
    {
      echo "admineditmonster.php<br />\n" .
           "Error in <i>$command</i><br />\n" .
           mysql_error() . "<br />\n";
      exit();
    }

    if($_POST['submit'] == 'Update and Back')
      header('Location: /admin/monstereditor.php?edit=' . ($monster['activity'] == 'fish' ? 'fish' : $edittype));
    else
      header('Location: /admin/editmonster.php?msg=48&' . $param . '=' . link_safe($name));

    exit();
  }
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Monster & Prey Editor &gt; Edit <?= ucfirst($param) ?></title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; <a href="/admin/monstereditor.php?edit=<?= $edittype ?>">Monster & Prey Editor</a> &gt; Edit <?= ucfirst($param) ?></h4>
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
       <td colspan="2"><input name="level" maxlength="2" size="2" value="<?= $monster['level'] ?>" /></td>
      </tr>
      <tr>
       <th>Minimum Stealth:</th>
       <td colspan="2"><input name="min_stealth" maxlength="2" size="2" value="<?= $monster['min_stealth'] ?>" /></td>
      </tr>
      <tr>
       <th>Minimum Stamina:</th>
       <td colspan="2"><input name="min_stamina" maxlength="2" size="2" value="<?= $monster['min_stamina'] ?>" /></td>
      </tr>
      <tr>
       <th>Minimum Athletics:</th>
       <td colspan="2"><input name="min_athletics" maxlength="2" size="2" value="<?= $monster['min_athletics'] ?>" /></td>
      </tr>
      <tr>
       <th>Minimum Wits:</th>
       <td colspan="2"><input name="min_wits" maxlength="2" size="2" value="<?= $monster['min_wits'] ?>" /></td>
      </tr>
      <tr>
       <th>Name:</th>
       <td colspan="2"><input name="name" maxlength="56" style="width: 256px;" value="<?= $monster['name'] ?>" /></td>
      </tr>
      <tr>
       <th>Type:</th>
       <td colspan="2"><?= monster_type_xhtml($monster['type']) ?></td>
      </tr>
      <tr>
       <th>Appearance:</th>
       <td><div id="appearance"><img src="/gfx/monsters/<?= $monster['graphic'] ?>" alt="" width="48" height="48" /></div></td>
       <td><input name="graphic" maxlength="24" style="width:200px;" value="<?= $monster['graphic'] ?>" onblur="document.getElementById('appearance').innerHTML = '<img src=\'/gfx/monsters/' + this.value + '\' width=\'48\' height=\'48\' alt=\'\' />';" /></td>
      </tr>
      <tr>
       <th>Loot:</th>
       <td colspan="2"><textarea name="loot" rows="3" style="width: 256px;" /><?= $monster['prizes'] ?></textarea></td>
      </tr>
     </table>
     <p><input type="hidden" name="action" value="update" /><input type="submit" name="submit" value="Update" /> <input type="submit" name="submit" value="Update and Back" class="bigbutton" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
