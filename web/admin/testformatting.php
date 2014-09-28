<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";
require_once "commons/donationlib.php";

if($user['admin']['clairvoyant'] != 'yes')
{
    header('Location: /admin/tools.php');
    exit();
}

if($_POST['submit'] == 'Submit')
{
  $NO_PIRATE = ($_POST['pirateor'] == 'yes' || $_POST['pirateor'] == 'on');
  
  $fmt = nl2br(format_text($_POST['text']));
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Formatting Test</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Administrative Tools &gt; Formatting Test</h4>
     <form action="admintestformatting.php" method="post">
     <table>
<!--      <tr><td>Presets:</td><td><select name="presets">
       <option value=""></option>
       <option value="sws">Standard with Smileys</option>
       <option value="swos">Standard w/o Smileys</option>
      </select></td></tr> -->
      <tr><td>Pirate Day Override:</td><td><input type="checkbox" name="pirateor" /></td></tr>
      <tr><td>Size-limited:</td><td><input type="checkbox" name="small" /></td></tr>
      <tr><td>Links OK:</td><td><input type="checkbox" name="links" checked /></td></tr>
      <tr><td>Emoticons:</td><td><input type="checkbox" name="emote" checked /></td></tr>
      <tr><td>Format Font:</td><td><input type="checkbox" name="ffont" checked /></td></tr>
      <tr><td>Text:</td><td><textarea name="text" style="width:500px; height:350px;"></textarea></tr>
     </table>
     <p><input type="submit" name="submit" value="Submit" /></p>
     </form>
<?php
if(strlen($fmt) > 0)
  echo '<hr><div>' . htmlentities($fmt) . '</div>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
