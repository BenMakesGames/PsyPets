<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

$require_petload = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";

if($admin["uploadpetgraphics"] != "yes")
{
  header("Location: /");
  exit();
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Gift Residents</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Upload Graphics</h4>
<?php
 if($error_message)
   echo "<p style=\"color:red;\">" . $error_message . "</p>\n";
?>
     <table border=0 cellspacing=0 cellpadding=4>
      <form enctype="multipart/form-data" action="uploadgraphic.php" method="POST">
      <tr>
       <td colspan=2 class="titlerow">
        <h5>Upload Graphic
       </td>
      </tr>
      <tr>
       <td bgcolor="#f0f0f0">
        <p>File:</p>
       </td>
       <td>
        <input type="file" name="gfxfile"><br>
       </td>
      </tr>
      <tr>
       <td bgcolor="#f0f0f0">
        <p>Description:</p>
       </td>
       <td>
        <input name="description"><br>
       </td>
      </tr>
      <tr>
       <td bgcolor="#f0f0f0" valign="top">
        <p>Graphic type:</p>
       </td>
       <td>
        <p>
         <input type="radio" name="gfxtype" value="none" checked> Do not upload<br>
         <input type="radio" name="gfxtype" value="publicpet"> Public pet graphic<br>
         <input type="radio" name="gfxtype" value="custompet"> Custom pet graphic<br>
         <input type="radio" name="gfxtype" value="publicavatar" disabled> Public avatar<br>
         <input type="radio" name="gfxtype" value="customavatar" disabled> Custom avatar<br>
         <input type="radio" name="gfxtype" value="item" disabled> Item/encyclopedic graphic<br>
        </p>
       </td>
      </tr>
      <tr>
       <td bgcolor="#f0f0f0">
        <p>Overwrite:</p>
       </td>
       <td>
        <input type="checkbox" name="overwrite"><br>
       </td>
      </tr>
      <tr>
       <td bgcolor="#f0f0f0"><p>&nbsp;</p></td>
       <td align="right">
        <input type="submit" name="submit" value="Upload" style="width:100px;">
       </td>
      </tr>
      <tr>
       <td colspan=2><p>&nbsp;</p></td>
      </tr>
      </form>
     </table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
