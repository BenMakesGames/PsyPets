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

if($admin['manageitems'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &nbsp; Administrative Tools &nbsp; Item Stats Cheat Sheet</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Item Stats Cheat Sheet</h4>
<?php
 if($error_message)
   echo "<p style=\"color:red;\">" . $error_message . "</p>\n";
?>

<h4>Stats (in order)</h4>
<p>Stat bonuses should not exceed the requirements.</p>
<p>Stat bonuses should not exceed half the crafting/inventing difficulty.</p>
<p>For every -2 stats, allow an extra 1</p>
<ul>
 <li>str</li>
 <li>dex</li>
 <li>sta</li>
 <li class="separator">per</li>
 <li>int</li>
 <li>wit</li>
 <li class="separator">bra</li>
 <li>ath</li>
 <li>ste</li>
 <li class="separator">sur</li>
 <li>cra</li>
 <li>eng</li>
 <li class="separator">min</li>
 <li>cap</li>
 <li>smi</li>
 <li class="separator">pregnancy</li>
 <li class="separator">tai</li>
 <li>pil</li>
</ul>
<p>
<h4>Durabilities</h4>
<ul>
 <li>600 - High Magic and Giamonds</li>
 <li>500 - Hyper Tech/Magic</li>
 <li>400 - Metal (gems, living plants)</li>
 <li>300 - Wood</li>
 <li>200 - Cardboard & stuffed things</li>
 <li>100 - Paper</li>
</ul>
<h4>Specific Durabilities</h4>
<ul>
 <li>700 - The Fatal Crossing of the Evening and the Morning</li>
 <li>450 - Unholy Vorpal... and Elusive Shield...</li>
 <li>400 - Compound Bow, the various plants, Giamond-Blade Knife</li>
 <li>350 - Couches, Mural</li>
 <li>325 - Small Couches</li>
 <li>300 - Chairs, Shoji Screen, Bokken, China</li>
 <li>275 - the cloaks</li>
 <li>250 - small paintings</li>
 <li>225 - Plushies</li>
 <li>200 - Pillows, Figurines</li>
 <li>150 - Wallpaper, Fans</li>
 <li>100 - Balloons, Clovers, Paper Hat</li>
</ul>

<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
