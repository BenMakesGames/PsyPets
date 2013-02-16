<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";

if($admin['seeserversettings'] != 'yes')
{
  Header("Location: /");
  exit();
}

$requested = 0;
$deleted = 0;

if($_POST['submit'] == 'Delete')
{
  foreach($_POST as $key=>$value)
  {
    if(preg_match('/^[0-9]{4}(-[0-9]{2}){5}$/', $key) && ($value == 'yes' || $value == 'on'))
    {
      $filename = '/errorlogs/' . $key . '.html';

      $requested++;

      if(unlink($filename))
        $deleted++;
    }
  }
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &nbsp; Administrative Tools &nbsp; Error Log Viewer</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Error Log Viewer</h4>
<?php
if($requested > 0)
  echo '<p>Requested deletion of ' . $requested . ' file' . ($requested != 1 ? 's' : '') . '; ' . $deleted . ' ' . ($deleted == 1 ? 'was' : 'were') . ' deleted.</p>';

if($dh = opendir('/errorlogs/'))
{
  $any = false;
  $rowclass = begin_row_class();

  while(($file = readdir($dh)) !== false)
  {
    if(filetype('/errorlogs/' . $file) == 'dir')
      continue;

    if(!$any)
    {
      echo '<form method="post">' .
           '<table>' .
           '<tr class="titlerow"><th></th><th>Log File</th></tr>';

      $any = true;
    }

    echo '<tr class="' . $rowclass . '"><td><input type="checkbox" name="' . substr($file, 0, 19) . '" /></td><td><a href="errorlogs/' . $file . '">' . $file . '</a></td></tr>';

    $rowclass = alt_row_class($rowclass);
  }

  if($any)
  {
    echo '</table>' .
         '<p><input type="submit" name="submit" value="Delete" /></p>' .
         '</form>';
  }
  else
    echo '<p>No error logs were found.</p>';

  closedir($dh);
}
else
  echo '<p class="failure">Could not open /errorlogs/</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
