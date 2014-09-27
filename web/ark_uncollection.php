<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/arklib.php';
require_once 'commons/petgraphics.php';

if($user['show_ark'] != 'yes')
{
  header('Location: /404/');
  exit();
}

$graphics_wanted = array();

foreach($PET_GRAPHICS as $graphic)
{
  $graphics_wanted[] = 'male ' . $graphic;
  $graphics_wanted[] = 'female ' . $graphic;
}

$command = 'SELECT graphic,gender FROM psypets_ark WHERE userid=' . $user['idnum'];
$donated = $database->FetchMultiple($command, 'fetching pets you\'ve donated');

foreach($donated as $pet)
{
  $i = array_search($pet['gender'] . ' ' . $pet['graphic'], $graphics_wanted);
  
  if($i !== false)
    unset($graphics_wanted[$i]);
}

$page = (int)$_GET['page'];

$num_items = count($graphics_wanted);
$num_pages = ceil($num_items / 20);

if($page < 1 || $page > $num_pages)
  $page = 1;

$graphics_wanted = array_slice($graphics_wanted, ($page - 1) * 20, 20);

if($_GET['dialog'] == 1)
  $dialog_text = '<p>Hm?  What?  Have you seen that somewhere else before?  A coincidence, I\'m sure.</p>';
else
{
  $dialog_text = '<p>Oh, here\'s a list of pets you have <em>not</em> found for me!  It\'s useful, yes?  The computer is so good at making lists like this.</p>';
  $options[] = '<a href="ark_uncollection.php?page=' . $page . '&dialog=1">Ask about the word "uncollection"</a>';
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Ark &gt; Uncollection</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Ark</h4>
     <ul class="tabbed">
      <li><a href="ark.php">My Collection</a></li>
      <li class="activetab"><a href="ark_uncollection.php">My Uncollection</a></li>
      <li><a href="ark_donate.php">Make Donation</a></li>
      <li><a href="ark_collections.php">Collections</a></li>
     </ul>
<?php
//echo '<img src="gfx/npcs/flowergirl.jpg" align="right" width="350" height="706" alt="(Vanessa the Florist)" />';

include 'commons/dialog_open.php';

echo $dialog_text;

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

if($num_items == 0)
  echo '<p>There is nothing left for you to collect!</p>';
else
{
  $pages = paginate($num_pages, $page, 'ark_uncollection.php?page=%s');

  echo $pages .
       '<table>' .
       '<tr class="titlerow"><th class="centered">Pet</th><th>Gender</th></tr>';

  $rowclass = begin_row_class();

  foreach($graphics_wanted as $graphic)
  {
    list($gender, $graphic) = explode(' ', $graphic);
    echo '<tr class="' . $rowclass . '"><td class="centered"><img src="gfx/pets/' . $graphic . '" /></td><td class="centered">' . gender_graphic($gender, 'yes') . '</td></tr>';

    $rowclass = alt_row_class($rowclass);
  }

  echo '</table>' .
       $pages;
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
