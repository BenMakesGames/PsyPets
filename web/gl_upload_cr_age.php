<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';

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
?>
<p>Sorry, I can't let people under the age of 18 upload their own graphics, since minors may not enter contracts.</p>
<p>(Do <em>not</em> lie to get around this, either about a graphic's origins, or your age.  That would be illegal, and will result in your being banned from PsyPets as per the <a href="/meta/termsofservice.php">Terms of Service</a>.)</p>
<?php
include 'commons/dialog_close.php';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
