<?php
$IGNORE_MAINTENANCE = true;

require_once 'commons/init.php';

$require_petload = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

if($admin['seeserversettings'] != 'yes')
{
  Header('Location: /');
  exit();
}

if($_POST['submit'] == 'Clear All')
  $database->FetchNone('TRUNCATE psypets_404_log');

$logged_404s = $database->FetchMultiple('
  SELECT *
  FROM psypets_404_log
  ORDER BY lastlog DESC
');
  
require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &nbsp; Administrative Tools &nbsp; 404 Logs</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; 404 Logs</h4>
<?php
if(count($logged_404s) > 0)
{
?>
  <form method="post">
  <p><input type="submit" name="submit" value="Clear All" /></p>
  </form>
  <table>
   <thead>
    <tr><th>URL</th><th>Attempts</th><th>Last Attempt</th></tr>
   </thead>
   <tbody>
<?php
  $rowclass = begin_row_class();

  foreach($logged_404s as $log)
  {
    echo '
      <tr class="' . $rowclass . '">
       <td>' . $log['url'] . '</td>
       <td class="centered">' . $log['count'] . '</td>
       <td class="centered">' . duration($now - $log['lastlog'], 2) . ' ago</td>
      </tr>
    ';
    
    $rowclass = alt_row_class($rowclass);
  }
?>
   </tbody>
  </table>
<?php
}
else
  echo '<p>No 404s have been logged.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
