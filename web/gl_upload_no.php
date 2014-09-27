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
<p>Unfortunately, the options provided are the only available at this time. Sorry we weren't able to come to an agreement.</p>
<p>Do <strong>not</strong> go back and pick a different option if it does not describe the graphic you want to upload!  Lying about this kind of thing is a good way to get yourself banned from the Graphics Library, or even from PsyPets entirely.</p>
<?php
}
include 'commons/dialog_close.php';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
