<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

// DISABLED
// Header("Location: /");

$require_petload = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";

if($admin["manageitems"] != "yes")
{
  Header("Location: /admin/tools.php");
  exit();
}

$eggs = array("red", "blue", "yellow", "rainbow", "silver", "gold");

$threads = array();

if($_POST["action"] == "egg")
{
  $number = (int)$_POST["eggcount"];

  if($number > 0)
  {
    $command = "SELECT * FROM monster_posts WHERE egg IN ('none', 'taken') ORDER BY rand() LIMIT $number";
    $result = mysql_query($command);
    if(!$result)
    {
      echo "/admin/eastereggs.php<br />\n" .
           "Error in <i>$command</i><br />\n" .
           mysql_error() . "<br />\n";
      exit();
    }

    while($post = mysql_fetch_assoc($result))
    {
      $threads[$post["threadid"]]++;
      
      $randomegg = $eggs[array_rand($eggs)];

      $command = "UPDATE monster_posts SET egg='$randomegg' WHERE idnum=" . $post["idnum"] . " LIMIT 1";
      $other_result = mysql_query($command);
      if(!$other_result)
      {
        echo "/admin/eastereggs.php<br />\n" .
             "Error in <i>$command</i><br />\n" .
             mysql_error() . "<br />\n";
        exit();
      }
    }
    
    mysql_free_result($result);
  }
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Easter Egger</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Easter Egger</h4>
     <form action="/admin/eastereggs.php" method="POST">
     <p><input name="eggcount" /> <input type="hidden" name="action" value="egg" /><input type="submit" value="Place Eggs" />
     </form>
<?php
if(count($threads) > 0)
{
  foreach($threads as $idnum=>$count)
    echo "Placed $count eggs in thread id #$idnum<br />\n";
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
