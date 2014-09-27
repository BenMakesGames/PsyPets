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
  header('Location: /gl_upload_cr_age.php');
  exit();
}

$base_path = substr(__FILE__, 0, strrpos(__FILE__, '/')); // ex: /home/a001337/psypets.net

if(array_key_exists("user", $_GET) && $admin["clairvoyant"] == "yes")
  $as_user = $_GET["user"];
else
  $as_user = $user["user"];

if($user['is_a_bad_person'] == 'yes')
{
  header('Location: /gl_browse.php?dialog=3');
  exit();
}

if($_POST['action'] == 'upload')
{
  $author = trim($_POST['author']);
  $title = trim($_POST['title']);

  $for = $_POST["recipient"];
  $recipient = 0;
  
  if(strlen($for) > 0)
  {
    $foruser = get_user_bydisplay($for);
    if($foruser === false)
      $error = "Could not find a resident by that name.";
    else
      $recipient = $foruser["idnum"];
  }

  $itype = $_FILES["file"]["type"];

  if(strlen($error) > 0)
    ;
  else if(strlen($author) < 1)
    $error = 'Please provide your name, as the author.';
  else if($_FILES["file"]["size"] == 0)
    $error = "The file you selected does not exist, or - strangely enough - is 0 bytes in size!  Check to make sure the file you're uploading does exist, and is, in fact, an image.";
  else if($_FILES["file"]["size"] > 6 * 1024)
    $error = "File sizes are limited to 6KB.";
  else if(!is_uploaded_file($_FILES["file"]["tmp_name"]))
  {
    $error = 'PsyPets had trouble receiving your file!  An administrator has been sent a PsyMail containing the details so that they can look in to it.  Sorry about the inconvenience.';
    psymail_user('telkoth', 'psypets', 'resident encountered trouble while uploading a graphic', $user['display'] . ' tried to upload ' . $_FILES["file"]["tmp_name"] . '<br />Check www.php.net/is_uploaded_file for details.');
  }
  else if($itype != 'image/gif' && $itype != 'image/png' && $itype != 'image/x-png')
    $error = 'Graphics must be either GIF or PNG (not ' . $itype . ').';
  else if($_FILES['file']['error'] > 0)
    $error = 'Unknown error: ' . $FILES['file']['error'];
  else
  {
    $image_info = getimagesize($_FILES['file']['tmp_name']);
    $w = $image_info[0];
    $h = $image_info[1];

    if(!(($w == 48 && $h == 48) || ($w >= 4 && $w <= 48 && $h == 32)))
      $error = 'The graphic\'s dimensions are not correct (' . $w . 'x' . $h . ').';
    else
    {
      $command = 'INSERT INTO `monster_graphicslibrary` (`uploader`, `w`, `h`, `recipient`, `title`, `author`, `rights`) VALUES ' .
                 '(' . $user['idnum'] . ", '$w', '$h', " . $recipient . ', ' . quote_smart($title) . ', ' . quote_smart($author) . ', \'pd_released\')';
      $database->FetchNone($command, 'gl_upload.php');

      $id = $database->InsertID();

      if($itype == 'image/gif')
        $filename = "$id.gif";
      else if($itype == 'image/png' || $itype == 'image/x-png')
        $filename = "$id.png";

      $newfile = 'gfx/library/' . $filename;

      if(move_uploaded_file($_FILES['file']['tmp_name'], $base_path . '/' . $newfile))
      {
        $command = 'UPDATE `monster_graphicslibrary` SET `url`=' . quote_smart($newfile) . " WHERE `idnum`=$id LIMIT 1";
        $database->FetchNone($command, 'gl_upload.php');

        psymail_user('telkoth', 'psypets', 'new graphics library content', '{r ' . $user['display'] . '} uploaded the following image (' . $newfile . '):<br /><img src="' . $newfile . '" /><br />They are claiming ownership, and releasing the graphic to the public domain.');

        header('Location: /gl_browse.php?dialog=4');
        exit();
      }
      else
      {
        $error = 'Upload failed.  If the problem continues, you should probably notify <a href="admincontact.php">an administrator</a> about this.';

        $command = "DELETE FROM `monster_graphicslibrary` WHERE `idnum`=$id LIMIT 1";
        $database->FetchNone($command, 'gl_upload.php');
      } // moved and renamed the image
    } // image is correct size
  } // no initial errors
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
     <h4><a href="/library.php">Library</a> &gt; <a href="/gl_browse.php">Graphics Library</a> &gt; Upload</h4>
     <p><strong>Please pay close attention and provide the most accurate information you can.</strong>  Players that repeatedly misuse this form may not be allowed access to it in the future.</p>
     <ul class="tabbed">
      <li><a href="/library.php">Information</a></li>
      <li><a href="/badgedb.php">Badge Archive</a></li>
      <li class="activetab"><a href="/gl_browse.php">Graphics Library</a></li>
     </ul>
<?php
echo '<a href="/npcprofile.php?npc=Marian+Witford"><img src="//saffron.psypets.net/gfx/npcs/marian-the-librarian.png" align="right" width="350" height="350" alt="(Marian the Librarian)" /></a>';

include 'commons/dialog_open.php';
if($error)
  echo '     <p>' . $error . '</p>';
else
{
?>
<p>Great!  Remember that by releasing your graphic into the public domain - which you are agreeing to do by uploading a graphic using this form - you are giving up <em>all of your rights</em> to that graphic.  Anyone anywhere will be allowed to use, copy, or modify the graphic, or even use it as the basis for their own work, without having any obligation to compensate you, or even give you credit.</p>
<p>Pornographic images are not allowed, as per <a href="/meta/termsofservice.php">PsyPets' Terms of Service</a>.</p>
<p>Misuse of the Graphic Library could result in you being banned from it.</p>
<?php
}
include 'commons/dialog_close.php';
?>
     <ul>
      <li><a href="gl_upload2.php">I made a mistake.  Let me pick a different option.</a></li>
     </ul>
     <h5>Upload</h5>
     <p><i>Avatar and pet graphics are 48 pixels by 48 pixels, so any graphic you intend for that use needs to be this same size.</i></p>
     <p><i>Item graphics are always 32 pixels tall, and may be from 8 to 48 pixels wide.</i></p>

     <form enctype="multipart/form-data" action="/gl_upload_cr_pd.php" method="post">
     <table>
      <tr>
       <th>Graphic title:</th>
       <td><input name="title" value="<?= $_POST['title'] ?>" /></td>
      </tr>
      <tr>
       <th>Copyright holder's name:</th>
       <td><input name="author" value="<?= $_POST['author'] ?>" /></td>
       <td>Your legal name, or a pen name.</td>
      </tr>
      <tr>
       <th>Graphic file:</th>
       <td><input type="file" name="file" /></td>
       <td>Only PNG and GIF files are allowed.  The file must be 6KB in size, or smaller.</td>
      </tr>
      <tr>
       <th>Available to*:</th>
       <td><input name="recipient" value="<?= $_POST["recipient"] ?>" /></td>
       <td>(optional)</td>
      </tr>
     </table>
     <p><input type="hidden" name="action" value="upload" /><input type="submit" value="Upload" /></p>
     </form>
     <p>* <i>If you want to make the graphic available only to a particular Resident, enter that Resident's name here. You may of course enter your own Resident name here. If you leave this blank, any PsyPets Resident will be able to use the graphic for their pet/item/avatar/etc.</i></p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
