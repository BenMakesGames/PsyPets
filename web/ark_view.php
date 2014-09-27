<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/arklib.php';

if($user['show_ark'] != 'yes')
{
  header('Location: /404/');
  exit();
}

$residentname = trim($_GET['resident']);

$resident = get_user_bydisplay($residentname, 'display,idnum');

if($resident === false)
{
  header('Location: ./ark_collections.php');
  exit();
}

$options = array();

$item_count = get_user_ark_count($resident['idnum']);

if($item_count > 0)
{
  $page = (int)$_GET['page'];
  $max_pages = ceil($item_count / 20);

  if($page < 1 || $page > $max_pages)
    $page = 1;

  $sort = $_GET['sort'];

  $graphic_sort = '<a href="ark_view.php?resident=' . link_safe($resident['display']) . '&sort=graphic">&#9661;</a>';
  $gender_sort = '<a href="ark_view.php?resident=' . link_safe($resident['display']) . '&sort=gender">&#9661;</a>';
  $time_sort = '<a href="ark_view.php?resident=' . link_safe($resident['display']) . '&sort=time">&#9661;</a>';

  if($sort == 'time')
  {
    $graphic_list = get_user_ark_page_by_time($resident['idnum'], $page);
    $time_sort = '&#9660;';
  }
  else if($sort == 'gender')
  {
    $graphic_list = get_user_ark_page_by_gender($resident['idnum'], $page);
    $gender_sort = '&#9660;';
  }
  else // by graphic
  {
    $graphic_list = get_user_ark_page_by_graphic($resident['idnum'], $page);
    $graphic_sort = '&#9660;';
    $sort = 'graphic';
  }

  $pages = paginate($max_pages, $page, 'ark_view.php?resident=' . link_safe($resident['display']) . '&sort=' . $sort . '&page=%s');
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Ark &gt; <?= $resident['display'] ?>'s Ark</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Ark &gt; <?= $resident['display'] ?>'s Ark</h4>
     <ul class="tabbed">
      <li><a href="ark.php">My Collection</a></li>
      <li><a href="ark_uncollection.php">My Uncollection</a></li>
      <li><a href="ark_donate.php">Make Donation</a></li>
      <li><a href="ark_collections.php">Collections</a></li>
      <li class="activetab"><a href="ark_view.php?resident=<?= link_safe($resident['display']) ?>"><?= $resident['display'] ?>'s Ark</a></li>
     </ul>
<?php
//echo '<img src="gfx/npcs/flowergirl.jpg" align="right" width="350" height="706" alt="(Vanessa the Florist)" />';

$descriptions = array(
  'The tall one, right?  I\'m pretty sure.',
  'The bed-head.  Hm.',
  'What about them?',
  'Don\'t they wear contacts?',
  'I think I met their parents once.',
  'Did you know they have a twin!?  Every time one of them comes in, I\'m not sure which it is!  I hope they don\'t mind sharing the same Ark profile...',
  'I hear they\'re doing well at that Museum thing...',
  'How long have they been in PsyPettia, anyway?  Do you know?',
  'I can\'t tell how old they are!  It\'s driving me crazy!  You know how some people are like that?',
  'How\'re their pets doing?',
);  

mt_srand($resident['idnum']);
$num = mt_rand(0, count($descriptions) - 1); 

include 'commons/dialog_open.php';

echo '<p>Who?  ' . $resident['display'] . '?  Oh yes!  I remember!  ' . $descriptions[$num] . '</p>' .
     '<p>Oh, their collection!  Of course!</p>' .
     '<p>' . $item_count . ' pet' . ($item_count != 1 ? 's' : '') . '!  It\'s almost hard to believe!</p>';

include 'commons/dialog_close.php';

echo '<ul><li><a href="residentprofile.php?resident=' . link_safe($resident['display']) . '">View ' . $resident['display'] . '\'s profile</a></li></ul>';

if($item_count == 0)
  echo '<p>' . $resident['display'] . ' hasn\'t collected anything for The Ark.</p>';
else
{
  echo $pages .
       '<table>' .
       '<tr class="titlerow"><th>Pet ' . $graphic_sort . '</th><th>Gender ' . $gender_sort . '</th><th class="centered">Donated ' . $time_sort . '</th></tr>';

  $rowclass = begin_row_class();

  foreach($graphic_list as $graphic)
  {
    echo '<tr class="' . $rowclass . '"><td class="centered"><img src="gfx/pets/' . $graphic['graphic'] . '" /></td><td class="centered">' . gender_graphic($graphic['gender'], 'yes') . '</td><td class="centered">' . Duration($now - $graphic['timestamp'], 2) . ' ago</td></tr>';

    $rowclass = alt_row_class($rowclass);
  }

  echo '</table>' .
       $pages;
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
