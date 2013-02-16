<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/polllib.php';

if($admin['viewpolls'] != 'yes' || $admin['createpolls'] != 'yes')
{
  header('Location: /403.php');
  exit();
}

if($_POST['action'] == 'create')
{
  $title = trim($_POST['title']);
  $description = trim($_POST['description']);
  $options = take_apart('|', $_POST['options']);

  if(count($options) > 0)
  {
    $pollid = create_poll($title, $description, $options);

    header('Location: /admin/polls.php');
    exit();
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &nbsp; Administrative Tools &nbsp; Poll Management</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; <a href="adminpolls.php">Poll Management</a> &gt; Create Poll</h4>
     <form method="post">
     <h5>Title</h5>
     <p><input name="title" value="<?= $title ?>" style="width:400px;" /></p>
     <h5>Additional Description</h5>
     <p>This field is optional.  It's prefered that the entire poll be summed up in the title, but if that is not feasible, this field should be used.</p>
     <p><textarea name="description"><?= $description ?></textarea></p>
     <h5>Options</h5>
     <p>Separate options with the pipe character "|".</p>
     <p><textarea name="options"><?= $options ?></textarea></p>
     <p><input type="hidden" name="action" value="create" /><input type="submit" value="Create" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
