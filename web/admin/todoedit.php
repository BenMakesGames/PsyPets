<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

$require_login = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/messages.php';
require_once 'commons/formatting.php';
require_once 'commons/todolistlib.php';

$ideaid = (int)$_GET['id'];

$command = 'SELECT * FROM psypets_ideachart WHERE idnum=' . $ideaid . ' LIMIT 1';
$idea = $database->FetchSingle($command, 'fetching idea details');

if($idea === false)
{
  header('Location: /arrangewishes.php');
  exit();
}

$is_manager = ($user['admin']['coder'] == 'yes');

if(!$is_manager)
{
    header('Location: /tododetails.php?id=' . $ideaid);
    exit();
}

$tags = array_keys($database->FetchMultipleBy(
  'SELECT * FROM psypets_ideachart_tags	WHERE ideaid=' . $ideaid,
  'tag'
));

$statuses = array('implemented', 'obsolete', 'duplicate', 'against-philosophies');

if($_POST['action'] == 'delete')
{
  $status = $_POST['status'];
  if(in_array($status, $statuses))
  {
    $database->FetchNone('
      INSERT INTO psypets_ideachart_complete
      (postdate, completedate, status, sdesc, ldesc, category, authorid, moverid)
      VALUES
      (
        ' . $idea['postdate'] . ',
        ' . $now . ',
        ' . quote_smart($status) . ',
        ' . quote_smart($idea['sdesc']) . ',
        ' . quote_smart($idea['ldesc']) . ',
        ' . quote_smart($idea['category']) . ',
        ' . $idea['authorid'] . ',
        ' . $user['idnum'] . '
      )
    ');

		// delete to-do list entry, and associated data
    $database->FetchNone('DELETE FROM psypets_ideachart WHERE idnum=' . $ideaid . ' LIMIT 1');
    $database->FetchNone('DELETE FROM psypets_ideachart_tags WHERE ideaid=' . $ideaid);
    $database->FetchNone('DELETE FROM psypets_ideavotes WHERE ideaid=' . $ideaid);

    header('Location: /todolist_completed.php');
    exit();
  }
}
else if($_POST['action'] == 'Edit')
{
  if($user['admin']['managewishlist'] == 'yes' && ($user['admin']['coder'] == 'yes' || $user['idnum'] == $idea['authorid']))
  {
    $sdesc = htmlspecialchars(trim($_POST['title']));
    $ldesc = htmlspecialchars(trim($_POST['entry']));
    $category = $_POST['category'];
    
    if(!in_array($category, $TODO_LIST_CATEGORIES))
      $category = $idea['category'];
    
    $database->FetchNone('
      UPDATE psypets_ideachart
      SET
        sdesc=' . quote_smart($sdesc) . ',
        ldesc=' . quote_smart(nl2br($ldesc)) . ',
        category=' . quote_smart($category) . '
      WHERE idnum=' . $ideaid . '
      LIMIT 1
    ');

    $idea['sdesc'] = $sdesc;
    $idea['ldesc'] = $ldesc;
    $idea['category'] = $category;
		
		$tags = explode(',', $_POST['tags']);

		$database->FetchNone('DELETE FROM psypets_ideachart_tags WHERE ideaid=' . $ideaid);
		
		foreach($tags as $i=>$tag)
		{
			$tags[$i] = trim($tag);
			$database->FetchNone('INSERT INTO psypets_ideachart_tags (ideaid, tag) VALUES (' . $ideaid . ', ' . quote_smart(trim($tags[$i])) . ')');
		}
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; To-do List &gt; <?= $idea['sdesc'] ?> &gt; Edit</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
    <h4><a href="/arrangewishes.php">To-do List</a> &gt; <a href="/tododetails.php?id=<?= $ideaid ?>"><?= $idea['sdesc'] ?></a> &gt; Edit</h4>
<?php
if($user['admin']['managewishlist'] == 'yes' && ($user['admin']['coder'] == 'yes' || $user['idnum'] == $idea['authorid']))
{
?>
    <form action="todoedit.php?id=<?= $ideaid ?>" method="post">
     <h5>Summary</h5>
     <p><input maxlength="120" name="title" style="width:600px;" value="<?= $idea['sdesc'] ?>" /></p>
     <h5>Long Description</h5>
     <p><textarea name="entry" cols="50" rows="3" style="width:600px;height:200px;"><?= br2nl($idea['ldesc']) ?></textarea></p>
     <h5>Category</h5>
     <p><select name="category">
<?php
  foreach($TODO_LIST_CATEGORIES as $category)
  {
    if($category == $idea['category'])
      echo '<option value="' . $category . '" selected="selected">' . $category . '</option>';
    else
      echo '<option value="' . $category . '">' . $category . '</option>';
  }
?>
     </select></p>
     <h5>Tags</h5>
     <p><input type="text" name="tags" style="width:600px;" value="<?= implode(',', $tags) ?>" /></p>
     <p><input type="submit" name="action" value="Edit" /></p>
    </form>
    <hr />
    <h5>Remove Wish</h5>
    <form method="post" onsubmit="return confirm('Really and for seriously delete this idea?');">
    <p>
     <input type="hidden" name="action" value="delete" />
     <select name="status">
      <option value="implemented">Implemented</option>
      <option value="obsolete">Obsolete</option>
      <option value="against-philosophies">Does not support design philosophies</option>
      <option value="duplicate">Duplicate entry</option>
     </select>
     <input type="submit" value="Delete" />
    </p>
    </form>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
