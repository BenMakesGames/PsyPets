<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

$wiki = 'To-do List';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/messages.php';
require_once 'commons/formatting.php';
require_once 'commons/threadfunc.php';
require_once 'commons/todolistlib.php';

if($user['admin']['coder'] != 'yes')
{
  header('Location: /todolist.php');
  exit();
}
	
if($_POST['action'] == 'add')
{
  $_POST['entry'] = trim($_POST['entry']);

  // make sure there's a title at all
  $_POST['title'] = trim($_POST['title']);
  if(strlen($_POST['title']) > 120)
    $_POST['title'] = substr($_POST['title'], 0, 120);
  else if(strlen($_POST['title']) < 5)
    $errors[] = '<p class="failure">Please provide a meaningful summary.</p>';

  $sdesc = $_POST['title'];
  $ldesc = $_POST['entry'];
  $category = $_POST['category'];

  $sdesc = htmlspecialchars($sdesc);
  $ldesc = htmlspecialchars($ldesc);
  $final_ldesc = nl2br($ldesc);

  if(!in_array($category, $TODO_LIST_CATEGORIES))
    $errors[] = '<p class="failure">You must choose a category.</p>';

  if(count($errors) == 0)
  {
    $database->FetchNone('
	    INSERT INTO psypets_ideachart (postdate, sdesc, ldesc, category, authorid)
	    VALUES
      (
	      ' . $now . ',
		    ' . quote_smart($sdesc) . ',
		    ' . quote_smart($final_ldesc) . ',
		    ' . quote_smart($category) . ',
		    ' . $user['idnum'] . '
	    )
	  ');
    
    $id = $database->InsertID();

    if($user['email'] != $SETTINGS['author_email'])
      mail($SETTINGS['author_email'], 'new wish list wish: ' . $sdesc, 'posted by ' . $user['display'] . ":\n" . $ldesc, 'From: ' . $SETTINGS['site_name'] . ' <' . $SETTINGS['site_mailer'] . '>');

    $command = 'UPDATE monster_users SET wishlistupdate=\'yes\'';
    $database->FetchNone($command, 'notifying residents of change (addition)');

    header('Location: /tododetails.php?id=' . $id);
    exit();
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Admin Tools &gt; To-do List &gt; Add</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/arrangewishes.php">To-do List</a> &gt; Add</h4>
<?php
if(count($errors) > 0)
  echo '<ul><li>' . implode('</li><li>', $errors) . '</li></ul>';
?>
     <p>Do not combine two suggestions into one wish!  Submit each suggestion as its own wish.</p>
     <form method="post">
     <h5>Summary</h5>
     <p>The summary should tell us what we're voting on in a few words, but <strong>be specific!</strong>  For example instead of "improvement to trading house", tell us what the improvement is; "add expiry time to public trades", for example.</p>
     <p><input maxlength="120" name="title" style="width:600px;" value="<?= $sdesc ?>" /></p>
     <h5>Full Description (optional)</h5>
     <p>If there are additional details, and/or rammifications residents should be aware of, provide those details here.</p>
     <p><textarea name="entry" cols="50" rows="3" style="width:500px; height:200px;"><?= $ldesc ?></textarea></p>
     <h5>Category</h5>
     <p><select name="category">
      <option value="">-</option>
<?php
foreach($TODO_LIST_CATEGORIES as $category)
  echo '<option value="' . $category . '">' . $category . '</option>';
?>
     </select></p>
     <h5>Tags</h5>
		 <p>...</p>
     <p><input type="hidden" name="action" value="add" /><input type="submit" value="Submit" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
