<?php
$IGNORE_MAINTENANCE = true;

require_once 'commons/init.php';

$require_petload = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";

if($admin["clairvoyant"] != "yes")
{
  header('Location: /');
  exit();
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &nbsp; Administrative Tools &nbsp; List of All Pet Graphics</title>
<?php include "commons/head.php"; ?>
  <style type="text/css">
   ul#petencyclopedia
   {
     list-style: none;
     margin: 0 0 1em 0;
     padding: 0;
   }

   ul#petencyclopedia li
   {
     display: block;
     width: 64px;
     height: 96px;
     float: left;
     text-align: center;
     padding: 0;
     margin: 0;
     list-style: none;
   }

   ul#petencyclopedia li img
   {
     display: block;
     border: 0;
     margin: 0 auto;
     padding: 0;
   }
  </style>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; List of All Pet Graphics</h4>
<ul id="petencyclopedia">
<?php
$rowclass = begin_row_class();

foreach($PET_GRAPHICS as $i=>$graphic)
{
  echo '<li><img src="gfx/pets/' . $graphic . '" width="48" height="48" /></li>';
  $rowclass = alt_row_class($rowclass);
}
?>
</ul>
<div style="clear:both;"></div>
<?php include "commons/footer_2.php"; ?>
 </body>
</html>
