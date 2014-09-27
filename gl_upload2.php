<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';

$base_path = substr(__FILE__, 0, strrpos(__FILE__, '/')); // ex: /home/a001337/psypets.net

function imagetograyscale(&$im)
{
  if(imageistruecolor($im))
    imagetruecolortopalette($im, false, 256);

  for($c = 0; $c < imagecolorstotal($im); $c++)
  {
    $col = imagecolorsforindex($im, $c);
    $gray = round(0.299 * $col['red'] + 0.587 * $col['green'] + 0.114 * $col['blue']);
    imagecolorset($im, $c, $gray, $gray, $gray);
  }
}

if(array_key_exists("user", $_GET) && $admin["clairvoyant"] == "yes")
  $as_user = $_GET["user"];
else
  $as_user = $user["user"];

if($user['is_a_bad_person'] == 'yes')
{
  header('Location: ./gl_browse.php?dialog=3');
  exit();
}

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; Library &gt; Graphics Library &gt; Upload</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript">
   function upload_free()
   {
     document.getElementById('dialog').style.display = 'none';
     document.getElementById('uploadfree').style.display = '';
   }
   
   function upload_paid()
   {
     document.getElementById('dialog').style.display = 'none';
     document.getElementById('uploadpaid').style.display = '';
   }
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="library.php">Library</a> &gt; <a href="gl_browse.php">Graphics Library</a> &gt; Upload</h4>
     <p><strong>Please pay close attention and provide the most accurate information you can.</strong>  Players that repeatedly misuse this form may not be allowed access to it in the future.</p>
     <ul class="tabbed">
      <li><a href="library.php">Information</a></li>
      <li><a href="badgedb.php">Badge Archive</a></li>
      <li class="activetab"><a href="gl_browse.php">Graphics Library</a></li>
     </ul>
<?php
echo '<a href="/npcprofile.php?npc=Marian+Witford"><img src="//saffron.psypets.net/gfx/npcs/marian-the-librarian.png" align="right" width="350" height="350" alt="(Marian the Librarian)" /></a>';

include 'commons/dialog_open.php';
if($error)
  echo '     <p>' . $error . '</p>';
else
{
?>
<p>Which of the following describes the graphic you are going to upload?</p>
<p>If the graphic is your original creation, then you are its copyright holder (you don't have to have filled out any forms: copyright is automatic by law - if you made it, you own it).</p>
<p>If the graphic's author died more than 75 years ago, or the graphic was specifically released into the public domain by its author, then it is part of the public domain.</p>
<p>Please note: PsyPets graphics are <em>not</em> public domain, and should not be reuploaded to the graphics library in any form.</p>
<?php
}
include 'commons/dialog_close.php';
?>
<ul>
 <li><a href="gl_upload_pd.php">The graphic is part of the public domain.</a></li>
 <li><a href="gl_upload_cr.php">I am the copyright holder for the graphic.</a></li>
 <li><a href="gl_upload_no.php">None of the above.</a></li>
</ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
