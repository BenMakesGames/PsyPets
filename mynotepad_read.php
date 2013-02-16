<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/notepadlib.php';

$noteid = (int)$_GET['id'];

$this_note = get_note_byid($noteid);

if($this_note === false || $this_note['userid'] != $user['idnum'])
{
  header('Location: ./mynotepad.php');
  exit();
}

if($_POST['action'] == 'Save')
{
  $title = trim($_POST['title']);
  $note = trim($_POST['note']);
  $category = trim($_POST['category']);
  
  if(strlen($title) == 0 || strlen($note) == 0)
    $message = '<p class="failure">I mean, it is <em>your</em> note, but it should at least have a title and some content, right?</p>';
  else
  {
    save_note($noteid, '', $category, $title, $note);
    
    $this_note['title'] = $title;
    
    $message = '<p class="success">Saved!</p>';
  }
}
else
{
  $title = $this_note['title'];
  $note = $this_note['body'];
  $category = $this_note['category'];
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Notepad &gt; <?= $this_note['title'] ?></title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="mynotepad.php">My Notepad</a> &gt; <?= $this_note['title'] ?></h4>
     <ul><li><a href="mynotepad_delete.php?id=<?= $noteid ?>" onclick="return confirm('Really delete this note forever and ever and ever?');">Delete this note</a></li></ul>
<?php
echo $message;
?>
     <form action="mynotepad_read.php?id=<?= $noteid ?>" method="post">
     <table>
      <tr>
       <th>Title:</th><td><input name="title" value="<?= $title ?>" style="width:280px;" /></td>
       <th>Category:</th><td><input name="category" value="<?= $category ?>" style="width:100px;" /></td>
      </tr>
     </table>
     <h5>Note:</h5>
     <p><textarea name="note" cols="50" rows="22" style="width:500px;"><?= $note ?></textarea></p>
     <p><input type="submit" name="action" value="Save" /></p>
     <p><i>("Category" is optional, and can be whatever you want.  You can sort your notes by category, so...)</i></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
