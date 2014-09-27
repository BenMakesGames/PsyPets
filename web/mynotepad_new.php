<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/notepadlib.php';

if($_POST['action'] == 'Save')
{
  $title = trim($_POST['title']);
  $note = trim($_POST['note']);
  $category = trim($_POST['category']);
  
  if(strlen($title) == 0 || strlen($note) == 0)
    $message = '<p class="failure">I mean, it is <em>your</em> note, but it should at least have a title and some content, right?</p>';
  else
  {
    new_note($user['idnum'], '', $category, $title, $note);
    
    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Created a My Notepad Note', 1);

    header('Location: ./mynotepad.php');
    exit();
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Notepad &gt; New Note</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="mynotepad.php">My Notepad</a> &gt; New Note</h4>
<?php
echo $message;
?>
     <form action="mynotepad_new.php" method="post">
     <table>
      <tr>
       <th>Title:</th><td><input name="title" value="<?= $title ?>" style="width:280px;" speech x-webkit-speech /></td>
       <th>Category:</th><td><input name="category" value="<?= $category ?>" style="width:100px;" speech x-webkit-speech /></td>
      </tr>
     </table>
     <h5>Note:</h5>
     <p><textarea name="note" cols="50" rows="10" style="width:500px;" speech x-webkit-speech><?= $note ?></textarea></p>
     <p><input type="submit" name="action" value="Save" /></p>
     <p><i>("Category" is optional, and can be whatever you want.  You can sort your notes by category, so...)</i></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
