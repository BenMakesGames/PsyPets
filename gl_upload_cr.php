<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';

if($user['idnum'] < 37476 && user_age($user) < 18)
{
  header('Location: ./gl_upload_cr_age.php');
  exit();
}

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; Library &gt; Graphics Library &gt; Upload</title>
<?php include "commons/head.php"; ?>
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
<p>PsyPets allows artists to upload graphics that they own in one of two ways:</p>
<ul>
 <li>The artist may give up all of their rights to the graphic, releasing into the public domain for anyone and everyone (in the world) to freely use.</li>
 <li>The artist may retain their rights, and give PsyPets the right to reproduce the graphic for purposes of use within the PsyPets game.</li>
</ul>
<p>How would you like to upload your graphic?</p>
<?php
}
include 'commons/dialog_close.php';
?>
<ul>
 <li><a href="gl_upload_cr_pd.php">I would like to release my graphic into the public domain.</a></li>
 <li><a href="gl_upload_cr_cr.php">I would like to retain my rights while giving PsyPets the right to reproduce the graphic.</a></li>
 <li><a href="gl_upload_no.php">None of the above.</a></li>
</ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
