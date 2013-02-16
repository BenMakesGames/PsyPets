<?php
$require_login = 'no';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/petgraphics.php';

$pets_per_page = 52;

$pages = ceil(count($PET_GRAPHICS) / $pets_per_page);

$page = (int)$_GET['page'];

if($page < 1 || $page > $pages)
  $page = 1;
  
$graphics = array_slice($PET_GRAPHICS, ($page - 1) * $pets_per_page, $pets_per_page, true);

$pages = paginate($pages, $page, 'petencyclopedia.php?page=%s');

$search_time = microtime(true);

$ingame_command = 'SELECT graphic,COUNT(graphic) AS c FROM `monster_pets` WHERE graphic IN (\'' . implode('\', \'', $graphics) . '\') GROUP BY (graphic)';
$ingame = $database->FetchMultipleBy($ingame_command, 'graphic', 'fetching number of pets in-game');

$search_time = microtime(true) - $search_time;

$footer_note .= '<br />Took ' . round($search_time, 4) . 's querying the database.';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Pet Encyclopedia</title>
<?php include 'commons/head.php'; ?>
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
     <h4>Pet Encyclopedia</h4>
<?= $pages ?>
<ul id="petencyclopedia">
<?php
$rowclass = begin_row_class();

foreach($graphics as $i=>$graphic)
{
?>
<li><a href="/petencyclopedia_owners.php?i=<?= ($i + 1) ?>">
 <img src="//<?= $SETTINGS['static_domain'] ?>/gfx/pets/<?= $graphic ?>" width="48" height="48" alt="" />
 <?= (int)$ingame[$graphic]['c'] ?>
</a></li>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
</ul>
<div style="clear:both;"></div>
<?= $pages ?>
<p><i>(The number below each graphic indicates how many pets of that type exist in the game.)</i></p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
